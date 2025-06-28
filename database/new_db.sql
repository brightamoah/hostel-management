CREATE DATABASE IF NOT EXISTS hostel_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE hostel_management;

-- Table structure for table `users`
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Student', 'Admin') NOT NULL DEFAULT 'Student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_email_verified TINYINT(1) DEFAULT 0,
    last_login TIMESTAMP NULL DEFAULT NULL
) ENGINE = InnoDB;

-- Table structure for table `students`
CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    date_of_birth DATE NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    address VARCHAR(255) NOT NULL,
    emergency_contact_name VARCHAR(100) NOT NULL,
    emergency_contact_number VARCHAR(20) NOT NULL,
    health_condition TEXT DEFAULT NULL,
    enrollment_date DATE NOT NULL,
    resident_status ENUM(
        'Active',
        'Inactive',
        'Suspended',
        'Graduated',
        'Withdrawn'
    ) DEFAULT 'Inactive',
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

-- Table structure for table `admins`
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    department VARCHAR(100) NOT NULL,
    access_level ENUM(
        'Super Admin',
        'Regular Admin',
        'Support Staff'
    ) NOT NULL DEFAULT 'Regular Admin',
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

-- Table structure for table `rooms`
CREATE TABLE rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(50) NOT NULL,
    building VARCHAR(100) NOT NULL,
    floor INT NOT NULL,
    room_type ENUM(
        'Single',
        'Double',
        'Triple',
        'Quad'
    ) NOT NULL,
    capacity INT NOT NULL,
    current_occupancy INT NOT NULL DEFAULT 0,
    features TEXT,
    amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    status ENUM(
        'Fully Occupied',
        'Partially Occupied',
        'Vacant',
        'Under Maintenance',
        'Reserved'
    ) NOT NULL DEFAULT 'Vacant',
    CONSTRAINT chk_capacity CHECK (capacity > 0),
    CONSTRAINT chk_occupancy CHECK (
        current_occupancy >= 0
        AND current_occupancy <= capacity
    ),
    CONSTRAINT unique_room UNIQUE (building, room_number)
) ENGINE = InnoDB;

-- I

-- Table structure for table `allocations`
CREATE TABLE allocations (
    allocation_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL DEFAULT NULL,
    status ENUM(
        'Active',
        'Expired',
        'Canceled',
        'Pending'
    ) NOT NULL DEFAULT 'Pending',
    allocated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms (room_id) ON DELETE CASCADE,
    CONSTRAINT chk_dates CHECK (
        end_date IS NULL
        OR end_date >= start_date
    ),
    CONSTRAINT unique_active_allocation UNIQUE (student_id, status)
) ENGINE = InnoDB;

-- Table structure for table `visitors`
CREATE TABLE visitors (
    visitor_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    visitor_name VARCHAR(100) NOT NULL,
    relation VARCHAR(50) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    visit_date DATE NOT NULL,
    check_in_time TIMESTAMP NULL DEFAULT NULL,
    check_out_time TIMESTAMP NULL DEFAULT NULL,
    status ENUM(
        'Pending',
        'Approved',
        'Checked-In',
        'Checked-Out',
        'Cancelled',
        'Denied'
    ) NOT NULL DEFAULT 'Pending',
    purpose TEXT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE
) ENGINE = InnoDB;

-- Table structure for table `announcements`
CREATE TABLE announcements (
    announcement_id INT AUTO_INCREMENT PRIMARY KEY,
    posted_by INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    priority ENUM(
        'Low',
        'Medium',
        'High',
        'Urgent'
    ) NOT NULL DEFAULT 'Medium',
    target_audience ENUM(
        'Students',
        'Admins',
        'All',
        'Specific'
    ) NOT NULL DEFAULT 'All',
    is_read TINYINT(1) DEFAULT 0,
    FOREIGN KEY (posted_by) REFERENCES admins (admin_id) ON DELETE CASCADE
) ENGINE = InnoDB;


-- Table structure for table `announcement_reads`
-- This table tracks which students have read which announcements
CREATE TABLE announcement_reads (
    read_id INT AUTO_INCREMENT PRIMARY KEY,
    announcement_id INT NOT NULL,
    student_id INT NOT NULL,
    read_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (announcement_id) REFERENCES announcements (announcement_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE,
    UNIQUE KEY (announcement_id, student_id)
) ENGINE = InnoDB;

-- Table structure for table `maintenance_requests`
CREATE TABLE maintenance_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    issue_type ENUM(
        'Plumbing',
        'Electrical',
        'Furniture',
        'Appliance',
        'Structural',
        'Pest Control',
        'Internet/Wi-Fi',
        'Other'
    ) NOT NULL,
    description TEXT NOT NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    priority ENUM(
        'Low',
        'Medium',
        'High',
        'Emergency'
    ) NOT NULL DEFAULT 'Medium',
    status ENUM(
        'Pending',
        'Assigned',
        'In-Progress',
        'Completed',
        'Rejected'
    ) NOT NULL DEFAULT 'Pending',
    completion_date TIMESTAMP NULL,
    assigned_to INT NULL DEFAULT NULL,
    FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms (room_id) ON DELETE CASCADE
) ENGINE = InnoDB;

-- Table structure for table `maintenance_responses`
CREATE TABLE maintenance_responses (
    response_id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    user_id INT NOT NULL,
    response_text TEXT NOT NULL,
    response_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES maintenance_requests (request_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE,
    CONSTRAINT chk_response_text CHECK (TRIM(response_text) != '')
) ENGINE = InnoDB;

-- Table structure for table `payments`
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    billing_id INT NULL DEFAULT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    transaction_reference VARCHAR(100) NOT NULL,
    payment_method ENUM(
        'Cash',
        'Credit Card',
        'Bank Transfer',
        'Mobile Money'
    ) NOT NULL,
    purpose ENUM(
        'Hostel Fee',
        'Penalty',
        'Security Deposit',
        'Maintenance Charge',
        'Other'
    ) NOT NULL,
    status ENUM(
        'Pending',
        'Completed',
        'Failed',
        'Refunded'
    ) NOT NULL DEFAULT 'Pending',
    FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE
) ENGINE = InnoDB;

-- Table structure for table `billing`
CREATE TABLE billing (
    billing_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    allocation_id INT NULL DEFAULT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description VARCHAR(255) NOT NULL,
    date_issued TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_due DATE NOT NULL,
    status ENUM(
        'Unpaid',
        'Fully Paid',
        'Partially Paid',
        'Overdue',
        'Cancelled'
    ) NOT NULL DEFAULT 'Unpaid',
    late_fee DECIMAL(10, 2) DEFAULT 0.00,
    paid_amount DECIMAL(10, 2) DEFAULT 0.00,
    FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE,
    FOREIGN KEY (allocation_id) REFERENCES allocations (allocation_id) ON DELETE SET NULL,
    CONSTRAINT chk_paid_amount CHECK (paid_amount >= 0)
);

-- Table structure for table `disciplinary_records`
CREATE TABLE disciplinary_records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    reported_by INT NULL DEFAULT NULL,
    violation_type ENUM(
        'Noise Complaint',
        'Curfew Violation',
        'Substance Abuse',
        'Vandalism',
        'Theft',
        'Unauthorized Guest',
        'Other'
    ) NOT NULL,
    description TEXT NOT NULL,
    date_reported TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_occurred DATETIME NULL DEFAULT NULL,
    severity ENUM('Minor', 'Moderate', 'Severe') NOT NULL,
    action_taken TEXT NOT NULL,
    status ENUM(
        'Pending',
        'Resolved',
        'Dismissed',
        'Rejected'
    ) NOT NULL DEFAULT 'Pending',
    resolution_date TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE,
    FOREIGN KEY (reported_by) REFERENCES users (user_id) ON DELETE SET NULL
) ENGINE = InnoDB;

-- Table structure for table `complaints`
CREATE TABLE complaints (
    complaint_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    room_id INT NULL DEFAULT NULL, -- Optional, for room-specific complaints
    complaint_type ENUM(
        'Room Condition',
        'Staff Behavior',
        'Amenities',
        'Noise',
        'Security',
        'Billing Issue',
        'Other'
    ) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM(
        'Low',
        'Medium',
        'High',
        'Emergency'
    ) NOT NULL DEFAULT 'Medium',
    status ENUM(
        'Pending',
        'In-Progress',
        'Resolved',
        'Rejected'
    ) NOT NULL DEFAULT 'Pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL DEFAULT NULL,
    resolved_by INT NULL DEFAULT NULL,
    FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms (room_id) ON DELETE SET NULL,
    FOREIGN KEY (resolved_by) REFERENCES admins (admin_id) ON DELETE SET NULL,
    CONSTRAINT chk_description CHECK (TRIM(description) != '')
) ENGINE = InnoDB;

-- Table structure for table `remember_tokens`
CREATE TABLE remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE verification_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    code VARCHAR(6) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE,
    UNIQUE KEY (token)
) ENGINE = InnoDB;

-- Complaint Responses table to store admin actions and notes
CREATE TABLE complaint_responses (
    response_id INT AUTO_INCREMENT PRIMARY KEY,
    complaint_id INT NOT NULL,
    admin_id INT NOT NULL,
    response_text TEXT NOT NULL,
    action_taken ENUM(
        'Assigned',
        'Updated',
        'Resolved',
        'Rejected'
    ) NOT NULL,
    response_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints (complaint_id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admins (admin_id) ON DELETE CASCADE,
    CONSTRAINT chk_response_text CHECK (TRIM(response_text) != '')
);

CREATE TABLE visitor_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_id INT NOT NULL,
    check_in_time DATETIME NOT NULL,
    check_out_time DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (visitor_id) REFERENCES visitors (visitor_id) ON DELETE CASCADE
);

-- Table structure for table `announcement_specific_targets`
CREATE TABLE announcement_specific_targets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    announcement_id INT NOT NULL,
    target_type ENUM(
        'student',
        'admin',
        'building',
        'room'
    ) NOT NULL,
    target_id INT NOT NULL,
    FOREIGN KEY (announcement_id) REFERENCES announcements (announcement_id) ON DELETE CASCADE
) ENGINE = InnoDB;

INSERT INTO
    visitor_logs (
        visitor_id,
        check_in_time,
        check_out_time
    )
SELECT
    visitor_id,
    check_in_time,
    check_out_time
FROM visitors
WHERE
    check_in_time IS NOT NULL
    OR check_out_time IS NOT NULL;


    -- //alter the visitors table to remove checkintime and checkouttime columns 
ALTER TABLE visitors
    DROP COLUMN check_in_time,
    DROP COLUMN check_out_time;


SHOW COLUMNS FROM visitors LIKE 'visit_date';

INSERT INTO
    visitors (
        student_id,
        visitor_name,
        relation,
        phone_number,
        visit_date,
        purpose,
        status
    )
VALUES (
        1,
        'John Doe',
        'Friend',
        '+233123456789',
        CURDATE(),
        'Meeting',
        'Pending'
    ),
    (
        1,
        'Jane Smith',
        'Family',
        '+233987654321',
        DATE_ADD(CURDATE(), INTERVAL 1 DAY),
        'Visit',
        'Approved'
    );

    SHOW TABLES FROM hostel_management LIKE 'bill%';

    -- Make date_due use the same format as date_issued (with time component)
ALTER TABLE billing MODIFY COLUMN date_due DATETIME NOT NULL;
SHOW TABLES FROM hostel_management;


-- modify the allocation taable allocations to add allocation period enum
ALTER TABLE allocations
    ADD COLUMN academic_period ENUM(
        'Semester 1',
        'Semester 2',
        'Entire Year',
        'Vacation Period'
    ) NOT NULL DEFAULT 'Semester 1';


ALTER TABLE billing
ADD COLUMN billing_type ENUM(
    'Hostel Fee',
    'Security Deposit',
    'Utility Fee',
    'Maintenance Fee',
    'Late Payment Penalty',
    'Other'
) NOT NULL DEFAULT 'Hostel Fee',
ADD COLUMN academic_period ENUM(
    'Semester 1',
    'Semester 2',
    'Entire Year',
    'Vacation Period'
) NOT NULL DEFAULT 'Semester 1',
ADD COLUMN payment_terms ENUM(
    'Net 15 Days',
    'Net 30 Days',
    'Net 45 Days',
    'Immediate Payment'
) NOT NULL DEFAULT 'Net 30 Days';