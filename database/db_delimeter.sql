DELIMITER $$

CREATE TRIGGER update_room_status
BEFORE UPDATE ON rooms
FOR EACH ROW
BEGIN
    IF NEW.current_occupancy = NEW.capacity THEN
        SET NEW.status = 'Fully Occupied';
    ELSEIF NEW.current_occupancy > 0 THEN
        SET NEW.status = 'Partially Occupied';
    ELSEIF NEW.current_occupancy = 0 THEN
        SET NEW.status = 'Vacant';
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