DELIMITER $$

CREATE TRIGGER update_room_status
BEFORE UPDATE ON rooms
FOR EACH ROW
BEGIN
    IF NEW.status NOT IN ('Under Maintenance', 'Reserved') THEN
        IF NEW.current_occupancy >= NEW.capacity THEN
            SET NEW.status = 'Fully Occupied';
        ELSEIF NEW.current_occupancy > 0 THEN
            SET NEW.status = 'Partially Occupied';
        ELSE
            SET NEW.status = 'Vacant';
        END IF;
    END IF;
END$$

DELIMITER;

DELIMITER $$
-- Trigger to handle role changes between Student and Admin
CREATE TRIGGER before_user_role_change
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
    
    IF OLD.role = 'Student' AND NEW.role = 'Admin' THEN
        DELETE FROM students WHERE user_id = OLD.user_id;
    END IF;

   
    IF OLD.role = 'Admin' AND NEW.role = 'Student' THEN
        DELETE FROM admins WHERE user_id = OLD.user_id;
    END IF;
  END$$  


DELIMITER;

DELIMITER $$

-- Trigger 1: Set check_in_time when status changes to Checked-In
CREATE TRIGGER before_visitor_checkin
BEFORE UPDATE ON visitors
FOR EACH ROW
BEGIN
    IF NEW.status = 'Checked-In' AND OLD.status != 'Checked-In' THEN
        -- If check_in_time is not set by the application, set it to the current timestamp
        IF NEW.check_in_time IS NULL THEN
            SET NEW.check_in_time = CURRENT_TIMESTAMP;
        END IF;
    END IF;
END$$

-- Trigger 2: Set check_out_time when status changes to Checked-Out

CREATE TRIGGER before_visitor_checkout
BEFORE UPDATE ON visitors
FOR EACH ROW
BEGIN
    IF NEW.status = 'Checked-Out' AND OLD.status != 'Checked-Out' THEN
        -- If check_out_time is not set by the application, set it to the current timestamp
        IF NEW.check_out_time IS NULL THEN
            SET NEW.check_out_time = CURRENT_TIMESTAMP;
        END IF;
    END IF;
END$$

-- Trigger 3: Reset timestamps when status reverts to Pending or Approved
CREATE TRIGGER before_visitor_status_reset
BEFORE UPDATE ON visitors
FOR EACH ROW
BEGIN
    IF NEW.status IN ('Pending', 'Approved') AND OLD.status NOT IN ('Pending', 'Approved') THEN
        -- Reset timestamps if reverting to Pending or Approved
        SET NEW.check_in_time = NULL;
        SET NEW.check_out_time = NULL;
    END IF;
END$$

-- Trigger 4: Prevent invalid status transitions
CREATE TRIGGER before_visitor_status_transition
BEFORE UPDATE ON visitors
FOR EACH ROW
BEGIN
    -- Prevent transitioning from Checked-Out back to Checked-In or Approved
    IF OLD.status = 'Checked-Out' AND NEW.status IN ('Checked-In', 'Approved', 'Pending') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot revert status from Checked-Out to Checked-In, Approved, or Pending';
    END IF;

    -- Prevent transitioning from Cancelled or Denied to any active status
    IF OLD.status IN ('Cancelled', 'Denied') AND NEW.status IN ('Pending', 'Approved', 'Checked-In') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot revert status from Cancelled or Denied to Pending, Approved, or Checked-In';
    END IF;

    -- Prevent transitioning to Checked-In unless status was Approved
    IF NEW.status = 'Checked-In' AND OLD.status != 'Approved' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot set status to Checked-In unless current status is Approved';
    END IF;

    -- Prevent transitioning to Checked-Out unless status was Checked-In
    IF NEW.status = 'Checked-Out' AND OLD.status != 'Checked-In' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot set status to Checked-Out unless current status is Checked-In';
    END IF;

    -- Prevent transitioning to Approved unless status was Pending
    IF NEW.status = 'Approved' AND OLD.status != 'Pending' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot set status to Approved unless current status is Pending';
    END IF;
END$$

DELIMITER;

-- Drop the trigger first if it already exists to avoid errors on re-creation
DELIMITER $$

DROP TRIGGER IF EXISTS after_allocation_insert_activate_student;

DELIMITER;

DELIMITER $$

CREATE TRIGGER after_allocation_insert_activate_student AFTER
INSERT
  ON allocations FOR EACH ROW BEGIN IF NEW.status = 'Active' THEN
UPDATE students
SET
  resident_status = 'Active'
WHERE
  student_id = NEW.student_id
  AND resident_status = 'Inactive';

END IF;

END

$$ DELIMITER;

-- Trigger to update the status of billings depending on the paid amount, due date, start date, and cancellation
DELIMITER /
/

-- Drop existing triggers if they exist
DROP TRIGGER IF EXISTS update_billing_status_before_insert;

DROP TRIGGER IF EXISTS update_billing_status_before_update;

-- BEFORE INSERT trigger
CREATE TRIGGER update_billing_status_before_insert
BEFORE INSERT ON billing
FOR EACH ROW
BEGIN
    IF NEW.status = 'Cancelled' THEN
        -- Retain Cancelled status
        SET NEW.status = 'Cancelled';
    ELSEIF NEW.date_due < NOW() AND NEW.paid_amount < NEW.amount THEN
        -- Set to Overdue if due date has passed and not fully paid
        SET NEW.status = 'Overdue';
    ELSEIF NEW.paid_amount >= NEW.amount THEN
        -- Set to Fully Paid if payment covers the full amount
        SET NEW.status = 'Fully Paid';
    ELSEIF NEW.paid_amount > 0 AND NEW.paid_amount < NEW.amount THEN
        -- Set to Partially Paid if partial payment has been made
        SET NEW.status = 'Partially Paid';
    ELSE
        -- Set to Unpaid if no payment has been made
        SET NEW.status = 'Unpaid';
    END IF;
END
/
/

-- BEFORE UPDATE trigger
CREATE TRIGGER update_billing_status_before_update
BEFORE UPDATE ON billing
FOR EACH ROW
BEGIN
    IF NEW.status = 'Cancelled' THEN
        -- Retain Cancelled status
        SET NEW.status = 'Cancelled';
    ELSEIF NEW.date_due < NOW() AND NEW.paid_amount < NEW.amount THEN
        -- Set to Overdue if due date has passed and not fully paid
        SET NEW.status = 'Overdue';
    ELSEIF NEW.paid_amount >= NEW.amount THEN
        -- Set to Fully Paid if payment covers the full amount
        SET NEW.status = 'Fully Paid';
    ELSEIF NEW.paid_amount > 0 AND NEW.paid_amount < NEW.amount THEN
        -- Set to Partially Paid if partial payment has been made
        SET NEW.status = 'Partially Paid';
    ELSE
        -- Set to Unpaid if no payment has been made
        SET NEW.status = 'Unpaid';
    END IF;
END
/
/

DELIMITER;

CREATE EVENT IF NOT EXISTS check_overdue_bills
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 1 DAY
DO
BEGIN
    UPDATE billing
    SET 
        status = 'Overdue',
        late_fee = CASE 
            WHEN late_fee = 0 OR late_fee IS NULL THEN amount * 0.05
            ELSE late_fee
        END
    WHERE 
        status IN ('Unpaid', 'Partially Paid') 
        AND date_due < NOW() 
        AND status != 'Cancelled';
END
/
/

SET GLOBAL event_scheduler = ON;

-- Create an event to update overdue bills every day
CREATE EVENT update_overdue_bills
    ON SCHEDULE EVERY 1 HOUR
    STARTS CURRENT_TIMESTAMP
    DO
      UPDATE billing
      SET status = 'Overdue'
      WHERE date_due < NOW()
        AND paid_amount < amount
        AND status = 'Unpaid';
-- Only update bills currently marked as 'Unpaid'

DROP EVENT IF EXISTS update_overdue_bills;

DROP EVENT IF EXISTS add_monthly_late_fees;

-- Create an event to update overdue bills with late fees every month
DELIMITER / /

CREATE EVENT add_monthly_late_fees
ON SCHEDULE EVERY 1 MONTH
STARTS CURRENT_TIMESTAMP
DO
  UPDATE billing
  SET late_fee = late_fee + (amount * 0.05),  -- Add 5% of the original amount to the existing late_fee
      status = 'Overdue' -- Ensure the status remains 'Overdue'
  WHERE (status = 'Unpaid' OR status = 'Overdue') -- Check if the bill is unpaid or already overdue
    AND date_due < NOW();
//

DELIMITER;
-- Check if the bill is overdue

-- list all events
SHOW EVENTS;

-- run an event immediately
-- CALL add_monthly_late_fees(); -- Removed: Events cannot be called directly; they run on schedule.