-- Create a database named 'hostel_management'
CREATE DATABASE hostel_management;

USE hostel_management;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Student', 'Admin') NOT NULL DEFAULT 'Student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_email_verified TINYINT(1) DEFAULT 0,
    last_login TIMESTAMP NULL
);

CREATE INDEX idx_role ON users (role);

CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    date_of_birth DATE NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    address VARCHAR(255) NOT NULL,
    emergency_contact_name VARCHAR(100) NOT NULL,
    emergency_contact_number VARCHAR(15) NOT NULL,
    health_condition TEXT DEFAULT NULL,
    enrollment_date DATE NOT NULL,
    resident_status ENUM(
        'Active',
        'Inactive',
        'Suspended'
    ) DEFAULT 'Inactive',
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
);

ALTER TABLE students
ADD disciplinary_record_id INT NULL,
ADD FOREIGN KEY (disciplinary_record_id) REFERENCES disciplinary_records (record_id) ON DELETE SET NULL;

CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    department VARCHAR(100) NOT NULL,
    access_level ENUM(
        'Super Admin',
        'Regular Admin'
    ) NOT NULL DEFAULT 'Regular Admin',
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
);

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
        'Under Maintenance'
    ) NOT NULL DEFAULT 'Vacant',
    CONSTRAINT chk_capacity CHECK (capacity > 0),
    CONSTRAINT chk_occupancy CHECK (
        current_occupancy >= 0
        AND current_occupancy <= capacity
    ),
    CONSTRAINT unique_room UNIQUE (building, room_number)
);

-- ALTER TABLE rooms
-- ADD COLUMN amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00 AFTER features;

-- ALTER TABLE rooms MODIFY room_number VARCHAR(50) NOT NULL;

-- UPDATE rooms
-- SET
--     features = 'Private bathroom, Smart TV (4K), Mini fridge, Premium bedding, Soundproofing, Air-conditioning, Desk with ergonomic chair, High-speed Wi-Fi',
--     amount = 5000.00
-- WHERE
--     room_type = 'Single';

-- UPDATE rooms
-- SET
--     features = 'Shared bathroom, Smart TV, Mini fridge, Air-conditioning, Desk, High-speed Wi-Fi',
--     amount = 2500.00
-- WHERE
--     room_type = 'Double';

-- UPDATE rooms
-- SET
--     features = 'Shared bathroom, LED TV, Wi-Fi, Desk, Air-conditioning',
--     amount = 1500.00
-- WHERE
--     room_type = 'Triple';

-- UPDATE rooms
-- SET
--     features = 'Wi-Fi, Shared bathroom, Basic furniture (bed, desk, chair)',
--     amount = 1000.00
-- WHERE
--     room_type = 'Quad';

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
END
DELIMITER $$
$$
$$
$$
$$
$$
$$
$$
$$
$$

CREATE INDEX idx_building ON rooms (building);

CREATE INDEX idx_floor ON rooms (floor);

CREATE TABLE allocations (
    allocation_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    status ENUM(
        'Active',
        'Expired',
        'Canceled',
        'Pending'
    ) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms (room_id) ON DELETE CASCADE,
    CONSTRAINT chk_unique_allocation UNIQUE (student_id, room_id),
    CONSTRAINT chk_dates CHECK (
        end_date IS NULL
        OR end_date >= start_date
    ),
    CONSTRAINT unique_active_allocation UNIQUE (student_id, status)
);

CREATE INDEX idx_allocation_dates ON allocations (start_date, end_date);

CREATE TABLE visitors (
    visitor_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    visitor_name VARCHAR(100) NOT NULL,
    relation VARCHAR(50) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    visit_date DATE NOT NULL,
    check_in_time TIMESTAMP NULL DEFAULT NULL, -- Explicitly set default to NULL
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
    FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE
);

CREATE TABLE announcements (
    announcement_id INT AUTO_INCREMENT PRIMARY KEY,
    posted_by INT NOT NULL,
    title VARCHAR(100) NOT NULL,
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
    FOREIGN KEY (posted_by) REFERENCES admins (admin_id) ON DELETE CASCADE
);

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
    FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms (room_id) ON DELETE CASCADE
);


CREATE TABLE maintenance_responses (
    response_id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    user_id INT NOT NULL,
    response_text TEXT NOT NULL,
    response_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES maintenance_requests (request_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE,
    CONSTRAINT chk_response_text CHECK (TRIM(response_text) != '')
);

CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
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
        'Other'
    ) NOT NULL,
    status ENUM(
        'Pending',
        'Completed',
        'Failed',
        'Refunded'
    ) NOT NULL DEFAULT 'Pending',
    FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE
);

CREATE TABLE billing (
    billing_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    payment_id INT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description VARCHAR(255) NOT NULL,
    date_issued TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_due DATETIME NOT NULL,
    status ENUM(
        'Unpaid',
        'Fully Paid',
        'Partially Paid',
        'Overdue',
        'Cancelled'
    ) NOT NULL DEFAULT 'Unpaid',
    late_fee DECIMAL(10, 2) DEFAULT 0.00,
    FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE,
    FOREIGN KEY (payment_id) REFERENCES payments (payment_id) ON DELETE CASCADE
);

CREATE TABLE disciplinary_records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    violation_type ENUM(
        'Noise Complaint',
        'Curfew Violation',
        'Substance Abuse',
        'Vandalism',
        'Theft',
        'Other'
    ) NOT NULL,
    description TEXT NOT NULL,
    date_reported TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    severity ENUM('Minor', 'Moderate', 'Severe') NOT NULL,
    action_taken TEXT NOT NULL,
    status ENUM(
        'Pending',
        'Resolved',
        'Dismissed',
        'Rejected'
    ) NOT NULL DEFAULT 'Pending',
    resolution_date TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE
)

CREATE TABLE remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
);

CREATE TABLE verification_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    code VARCHAR(6) NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
);

CREATE TABLE password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE,
    UNIQUE KEY (token)
);



-- Complaints table to store student-submitted complaints
CREATE TABLE complaints (
    complaint_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    room_id INT NULL, -- Optional, for room-specific complaints
    complaint_type ENUM(
        'Room Condition',
        'Staff Behavior',
        'Amenities',
        'Noise',
        'Security',
        'Billing',
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
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES students (student_id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms (room_id) ON DELETE SET NULL,
    CONSTRAINT chk_description CHECK (TRIM(description) != '')
);

-- Indexes for performance
CREATE INDEX idx_complaint_student ON complaints (student_id);

CREATE INDEX idx_complaint_status ON complaints (status);

CREATE INDEX idx_complaint_priority ON complaints (priority);

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

-- Index for complaint responses
CREATE INDEX idx_response_complaint ON complaint_responses (complaint_id);




--Add some data to the tables
INSERT INTO
    users (name, email, password, role)
VALUES (
        'Bright Amoah',
        'brghtmalone@gmail.com',
        '$2y$10$mrt83dmawSv1zjjiN1ctH.2fXjJ.sUrk3qcDuUWx/2VSV5t.A.Hwu',
        'Admin'
    ),
    (
        'Albert Smith',
        'asmith@test.com',
        '$2y$10$mrt83dmawSv1zjjiN1ctH.2fXjJ.sUrk3qcDuUWx / 2VSV5t.A.Hwu',
        'Student'
    ),
    (
        'Grace Brown',
        'grace@test.com',
        '$2y$10$mrt83dmawSv1zjjiN1ctH.2fXjJ.sUrk3qcDuUWx / 2VSV5t.A.Hwu',
        'Student'
    ),
    (
        'John Doe',
        'john@test.com',
        '$2y$10$mrt83dmawSv1zjjiN1ctH.2fXjJ.sUrk3qcDuUWx / 2VSV5t.A.Hwu',
        'Student'
    ),
    (
        'Jane Doe',
        'jane@test.com',
        '$2y$10$mrt83dmawSv1zjjiN1ctH.2fXjJ.sUrk3qcDuUWx / 2VSV5t.A.Hwu',
        'Admin'
    ),
    (
        'Michael Johnson',
        'mjohnson@test.com',
        '$2y$10$mrt83dmawSv1zjjiN1ctH.2fXjJ.sUrk3qcDuUWx / 2VSV5t.A.Hwu',
        'Admin'
    );

-- query to get the id of users who are admins
SELECT user_id, name, email FROM users WHERE role = 'Admin';

INSERT INTO
    admins (
        user_id,
        first_name,
        last_name,
        department,
        access_level
    )
VALUES (
        (
            SELECT user_id
            FROM users
            WHERE
                email = 'brghtmalone@gmail.com'
        ),
        'Bright',
        'Amoah',
        'IT',
        'Super Admin'
    ),
    (
        (
            SELECT user_id
            FROM users
            WHERE
                email = 'jane@test.com'
        ),
        'Jane',
        'Doe',
        'Hostel Management',
        'Regular Admin'
    ),
    (
        (
            SELECT user_id
            FROM users
            WHERE
                email = 'mjohnson@test.com'
        ),
        'Michael',
        'Johnson',
        'Maintenance',
        'Regular Admin'
    );

SELECT * FROM admins;

INSERT INTO
    students (
        user_id,
        first_name,
        last_name,
        gender,
        date_of_birth,
        phone_number,
        address,
        emergency_contact_name,
        emergency_contact_number,
        health_condition,
        enrollment_date,
        resident_status
    )
VALUES (
        (
            SELECT user_id
            FROM users
            WHERE
                email = 'asmith@test.com'
        ),
        'Albert',
        'Smith',
        'Male',
        '2000-01-21',
        '+2335012345678',
        '123 Main St., New York, NY',
        'Smith, Benedict',
        '+2335098765432',
        'No health issues',
        '2025-03-01',
        'Active'
    ),
    (
        (
            SELECT user_id
            FROM users
            WHERE
                email = 'grace@test.com'
        ),
        'Grace',
        'Brown',
        'Female',
        '2002-05-15',
        '+2335098765432',
        '456 Elm St., Los Angeles, CA',
        'Brown, Joseph',
        '+2335012345678',
        'Asthma, Allergies',
        '2024-09-01',
        'Active'
    ),
    (
        (
            SELECT user_id
            FROM users
            WHERE
                email = 'john@test.com'
        ),
        'John',
        'Doe',
        'Male',
        '2001-12-31',
        '+2335098765432',
        '789 Oak St., Chicago, IL',
        'Doe Charles',
        '+2335012345678',
        'Peanut allergy',
        '2023-06-01',
        'Active'
    );

SELECT * FROM students;

INSERT INTO
    rooms (
        room_number,
        building,
        floor,
        room_type,
        capacity,
        current_occupancy,
        features,
        amount,
        status
    )
VALUES (
        103,
        'Hostel A',
        1,
        'Single',
        1,
        0,
        'Private bathroom, Smart TV (4K), Mini fridge, Premium bedding, Soundproofing, Air-conditioning, Balcony, Desk with ergonomic chair, High-speed Wi-Fi',
        250.00,
        'Vacant'
    ),
    (
        312,
        'Hostel B',
        3,
        'Single',
        1,
        0,
        'Private bathroom, Smart TV (4K), Mini fridge, Premium bedding, Soundproofing, Air-conditioning, Desk with ergonomic chair, High-speed Wi-Fi',
        250.00,
        'Vacant'
    ),
    (
        314,
        'Hostel B',
        3,
        'Single',
        1,
        0,
        'Private bathroom, Smart TV (4K), Mini fridge, Premium bedding, Soundproofing, Air-conditioning, Desk with ergonomic chair, High-speed Wi-Fi',
        250.00,
        'Vacant'
    ),
    (
        104,
        'Hostel A',
        1,
        'Double',
        2,
        1,
        'Shared bathroom, Smart TV, Mini fridge, Air-conditioning, Desk, High-speed Wi-Fi',
        200.00,
        'Partially Occupied'
    ),
    (
        311,
        'Hostel A',
        3,
        'Double',
        2,
        0,
        'Shared bathroom, Smart TV, Mini fridge, Air-conditioning, Desk, High-speed Wi-Fi',
        200.00,
        'Vacant'
    ),
    (
        209,
        'Hostel B',
        2,
        'Double',
        2,
        0,
        'Shared bathroom, Smart TV, Mini fridge, Air-conditioning, Desk, High-speed Wi-Fi',
        200.00,
        'Under Maintenance'
    ),
    (
        315,
        'Hostel B',
        3,
        'Double',
        2,
        0,
        'Shared bathroom, Smart TV, Mini fridge, Air-conditioning, Desk, High-speed Wi-Fi',
        200.00,
        'Vacant'
    ),
    (
        207,
        'Hostel B',
        2,
        'Triple',
        3,
        0,
        'Shared bathroom, LED TV, Wi-Fi, Desk, Air-conditioning',
        150.00,
        'Vacant'
    ),
    (
        105,
        'Hostel A',
        1,
        'Triple',
        3,
        3,
        'Shared bathroom, LED TV, Wi-Fi, Desk, Air-conditioning',
        150.00,
        'Fully Occupied'
    ),
    (
        316,
        'Hostel A',
        3,
        'Triple',
        3,
        0,
        'Shared bathroom, LED TV, Wi-Fi, Desk, Air-conditioning',
        150.00,
        'Vacant'
    ),
    (
        208,
        'Hostel B',
        2,
        'Quad',
        4,
        4,
        'Wi-Fi, Shared bathroom, Basic furniture (bed, desk, chair)',
        100.00,
        'Fully Occupied'
    ),
    (
        313,
        'Hostel A',
        3,
        'Quad',
        4,
        0,
        'Wi-Fi, Shared bathroom, Basic furniture (bed, desk, chair)',
        100.00,
        'Vacant'
    );

SELECT * FROM rooms;

INSERT INTO
    allocations (
        student_id,
        room_id,
        start_date,
        end_date,
        status
    )
VALUES (
        1,
        1,
        '2025-04-01',
        '2025-10-30',
        'Active'
    ),
    (
        2,
        5,
        '2025-04-15',
        '2025-12-05',
        'Active'
    ),
    (
        3,
        6,
        '2025-03-01',
        '2025-09-30',
        'Active'
    );

SELECT * FROM allocations;

INSERT INTO
    visitors (
        student_id,
        visitor_name,
        relation,
        phone_number,
        visit_date,
        check_in_time,
        check_out_time,
        status,
        purpose
    )
VALUES (
        1,
        'Emmanuel Addo',
        'Friend',
        '+233501234528',
        '2025-04-15',
        NULL,
        NULL,
        'Pending',
        'Project discussion'
    ),
    (
        2,
        'Elizabeth Osei',
        'Sister',
        '+233501234529',
        '2025-04-16',
        '2025-04-16 09:00:00',
        NULL,
        'Approved',
        'Family visit'
    ),
    (
        3,
        'Kwabena Asante',
        'Cousin',
        '+233501234530',
        '2025-04-17',
        '2025-04-17 14:00:00',
        '2025-04-17 16:00:00',
        'Checked-Out',
        'Casual visit'
    ),
    (
        2,
        'Sophia Mensah',
        'Friend',
        '+233501234531',
        '2025-04-18',
        '2025-04-18 11:00:00',
        NULL,
        'Checked-In',
        'Study group'
    ),
    (
        1,
        'Michael Kwarteng',
        'Brother',
        '+233501234532',
        '2025-04-19',
        NULL,
        NULL,
        'Denied',
        'Late-night visit'
    ),
    (
        3,
        'Abigail Yeboah',
        'Aunt',
        '+233501234533',
        '2025-04-20',
        '2025-04-20 10:30:00',
        '2025-04-20 12:30:00',
        'Checked-Out',
        'Family visit'
    ),
    (
        1,
        'Daniel Owusu',
        'Friend',
        '+233501234534',
        '2025-04-21',
        NULL,
        NULL,
        'Pending',
        'Gaming session'
    ),
    (
        2,
        'Rebecca Ansah',
        'Mother',
        '+233501234535',
        '2025-04-22',
        '2025-04-22 09:00:00',
        '2025-04-22 11:00:00',
        'Checked-Out',
        'Family visit'
    ),
    (
        3,
        'Thomas Agyeman',
        'Friend',
        '+233501234536',
        '2025-04-23',
        '2025-04-23 15:00:00',
        NULL,
        'Checked-In',
        'Study session'
    ),
    (
        1,
        'Janet Boateng',
        'Sister',
        '+233501234537',
        '2025-04-24',
        NULL,
        NULL,
        'Approved',
        'Family visit'
    ),
    (
        2,
        'Peter Amankwah',
        'Cousin',
        '+233501234538',
        '2025-04-25',
        NULL,
        NULL,
        'Cancelled',
        'Cancelled visit'
    ),
    (
        3,
        'Grace Adjei',
        'Friend',
        '+233501234539',
        '2025-04-26',
        '2025-04-26 12:00:00',
        '2025-04-26 14:00:00',
        'Checked-Out',
        'Casual visit'
    ),
    (
        1,
        'Samuel Tetteh',
        'Brother',
        '+233501234540',
        '2025-04-27',
        NULL,
        NULL,
        'Pending',
        'Family visit'
    ),
    (
        2,
        'Mary Nkrumah',
        'Aunt',
        '+233501234541',
        '2025-04-28',
        '2025-04-28 10:00:00',
        NULL,
        'Checked-In',
        'Family visit'
    ),
    (
        3,
        'Joseph Appiah',
        'Friend',
        '+233501234542',
        '2025-04-29',
        NULL,
        NULL,
        'Denied',
        'No prior notice'
    ),
    (
        1,
        'Esther Quaye',
        'Mother',
        '+233501234543',
        '2025-04-30',
        '2025-04-30 11:00:00',
        '2025-04-30 13:00:00',
        'Checked-Out',
        'Family visit'
    ),
    (
        2,
        'David Asare',
        'Friend',
        '+233501234544',
        '2025-05-01',
        NULL,
        NULL,
        'Pending',
        'Study group'
    ),
    (
        3,
        'Linda Adu',
        'Sister',
        '+233501234545',
        '2025-05-02',
        '2025-05-02 14:00:00',
        NULL,
        'Approved',
        'Family visit'
    ),
    (
        1,
        'Isaac Kofi',
        'Friend',
        '+233501234546',
        '2025-05-03',
        '2025-05-03 15:30:00',
        '2025-05-03 17:30:00',
        'Checked-Out',
        'Casual visit'
    ),
    (
        2,
        'Patricia Darko',
        'Cousin',
        '+233501234547',
        '2025-05-04',
        NULL,
        NULL,
        'Pending',
        'Dropping off items'
    ),
    (
        3,
        'Rose Mensah',
        'Friend',
        '+233501234548',
        '2025-05-05',
        '2025-05-05 09:00:00',
        NULL,
        'Checked-In',
        'Study session'
    ),
    (
        1,
        'Ebenezer Osei',
        'Brother',
        '+233501234549',
        '2025-05-06',
        NULL,
        NULL,
        'Approved',
        'Family visit'
    ),
    (
        2,
        'Nancy Amponsah',
        'Mother',
        '+233501234550',
        '2025-05-07',
        '2025-05-07 10:00:00',
        '2025-05-07 12:00:00',
        'Checked-Out',
        'Family visit'
    ),
    (
        3,
        'Victor Yeboah',
        'Friend',
        '+233501234551',
        '2025-05-08',
        NULL,
        NULL,
        'Cancelled',
        'Cancelled visit'
    ),
    (
        1,
        'Theresa Adomako',
        'Sister',
        '+233501234552',
        '2025-05-09',
        '2025-05-09 13:00:00',
        NULL,
        'Checked-In',
        'Family visit'
    ),
    (
        2,
        'Edward Bonsu',
        'Friend',
        '+233501234553',
        '2025-05-10',
        NULL,
        NULL,
        'Pending',
        'Casual visit'
    ),
    (
        3,
        'Lydia Boateng',
        'Aunt',
        '+233501234554',
        '2025-05-11',
        '2025-05-11 11:00:00',
        '2025-05-11 13:00:00',
        'Checked-Out',
        'Family visit'
    ),
    (
        1,
        'Stephen Appiah',
        'Friend',
        '+233501234555',
        '2025-05-12',
        NULL,
        NULL,
        'Denied',
        'Unauthorized visit'
    ),
    (
        2,
        'Janet Agyeman',
        'Sister',
        '+233501234556',
        '2025-05-13',
        '2025-05-13 14:00:00',
        NULL,
        'Approved',
        'Family visit'
    ),
    (
        3,
        'Philip Danso',
        'Friend',
        '+233501234557',
        '2025-05-14',
        '2025-05-14 15:00:00',
        '2025-05-14 17:00:00',
        'Checked-Out',
        'Project work'
    ),
    (
        1,
        'Andrew Owusu',
        'Cousin',
        '+233501234558',
        '2025-05-15',
        NULL,
        NULL,
        'Pending',
        'Casual visit'
    ),
    (
        2,
        'Cecilia Annan',
        'Mother',
        '+233501234559',
        '2025-05-16',
        '2025-05-16 09:30:00',
        NULL,
        'Checked-In',
        'Family visit'
    ),
    (
        3,
        'Mark Adu',
        'Friend',
        '+233501234560',
        '2025-05-17',
        NULL,
        NULL,
        'Approved',
        'Study group'
    ),
    (
        1,
        'Priscilla Mensah',
        'Friend',
        '+233501234561',
        '2025-05-18',
        '2025-05-18 12:00:00',
        '2025-05-18 14:00:00',
        'Checked-Out',
        'Casual visit'
    ),
    (
        2,
        'George Kyei',
        'Brother',
        '+233501234562',
        '2025-05-19',
        NULL,
        NULL,
        'Pending',
        'Family visit'
    ),
    (
        3,
        'Rebecca Otoo',
        'Aunt',
        '+233501234563',
        '2025-05-20',
        '2025-05-20 10:00:00',
        NULL,
        'Checked-In',
        'Family visit'
    ),
    (
        1,
        'Daniel Sarkodie',
        'Friend',
        '+233501234564',
        '2025-05-21',
        NULL,
        NULL,
        'Cancelled',
        'Cancelled visit'
    ),
    (
        2,
        'Ellen Amankwah',
        'Sister',
        '+233501234565',
        '2025-05-22',
        '2025-05-22 11:00:00',
        '2025-05-22 13:00:00',
        'Checked-Out',
        'Family visit'
    ),
    (
        3,
        'Francis Boadu',
        'Friend',
        '+233501234566',
        '2025-05-23',
        NULL,
        NULL,
        'Pending',
        'Study session'
    ),
    (
        1,
        'Agnes Darkwah',
        'Mother',
        '+233501234567',
        '2025-05-24',
        '2025-05-24 14:00:00',
        NULL,
        'Approved',
        'Family visit'
    ),
    (
        2,
        'Timothy Nartey',
        'Friend',
        '+233501234568',
        '2025-05-25',
        '2025-05-25 15:00:00',
        '2025-05-25 17:00:00',
        'Checked-Out',
        'Casual visit'
    ),
    (
        3,
        'Veronica Asiedu',
        'Cousin',
        '+233501234569',
        '2025-05-26',
        NULL,
        NULL,
        'Denied',
        'No prior notice'
    ),
    (
        1,
        'Patrick Yeboah',
        'Brother',
        '+233501234570',
        '2025-05-27',
        NULL,
        NULL,
        'Pending',
        'Family visit'
    ),
    (
        2,
        'Sandra Owusu',
        'Friend',
        '+233501234571',
        '2025-05-28',
        '2025-05-28 09:00:00',
        NULL,
        'Checked-In',
        'Study group'
    ),
    (
        3,
        'Lawrence Agyei',
        'Friend',
        '+233501234572',
        '2025-05-29',
        NULL,
        NULL,
        'Approved',
        'Casual visit'
    ),
    (
        1,
        'Mercy Kusi',
        'Sister',
        '+233501234573',
        '2025-05-30',
        '2025-05-30 11:00:00',
        '2025-05-30 13:00:00',
        'Checked-Out',
        'Family visit'
    ),
    (
        2,
        'Richard Antwi',
        'Friend',
        '+233501234574',
        '2025-05-31',
        NULL,
        NULL,
        'Pending',
        'Project discussion'
    ),
    (
        3,
        'Kofi Mensah',
        'Cousin',
        '+233501234575',
        '2025-06-01',
        '2025-06-01 14:00:00',
        NULL,
        'Checked-In',
        'Family visit'
    ),
    (
        1,
        'Ama Serwaa',
        'Aunt',
        '+233501234576',
        '2025-06-02',
        NULL,
        NULL,
        'Approved',
        'Family visit'
    ),
    (
        2,
        'Yaw Boateng',
        'Friend',
        '+233501234577',
        '2025-06-03',
        '2025-06-03 15:00:00',
        '2025-06-03 17:00:00',
        'Checked-Out',
        'Casual visit'
    ),
    (
        3,
        'Akosua Adjei',
        'Sister',
        '+233501234578',
        '2025-06-04',
        NULL,
        NULL,
        'Pending',
        'Family visit'
    ),
    (
        1,
        'Kojo Asante',
        'Friend',
        '+233501234579',
        '2025-06-05',
        NULL,
        NULL,
        'Denied',
        'Unauthorized visit'
    ),
    (
        2,
        'Adwoa Nkrumah',
        'Mother',
        '+233501234580',
        '2025-06-06',
        '2025-06-06 09:00:00',
        NULL,
        'Checked-In',
        'Family visit'
    ),
    (
        3,
        'Kwesi Appiah',
        'Brother',
        '+233501234581',
        '2025-06-07',
        NULL,
        NULL,
        'Approved',
        'Family visit'
    ),
    (
        1,
        'Efua Mensah',
        'Friend',
        '+233501234582',
        '2025-06-08',
        '2025-06-08 12:00:00',
        '2025-06-08 14:00:00',
        'Checked-Out',
        'Study group'
    ),
    (
        2,
        'Kwame Osei',
        'Cousin',
        '+233501234583',
        '2025-06-09',
        NULL,
        NULL,
        'Pending',
        'Casual visit'
    ),
    (
        3,
        'Abena Yeboah',
        'Friend',
        '+233501234584',
        '2025-06-10',
        '2025-06-10 14:00:00',
        NULL,
        'Checked-In',
        'Study session'
    ),
    (
        1,
        'Kofi Adu',
        'Brother',
        '+233501234585',
        '2025-06-11',
        NULL,
        NULL,
        'Cancelled',
        'Cancelled visit'
    ),
    (
        2,
        'Ama Boateng',
        'Aunt',
        '+233501234586',
        '2025-06-12',
        '2025-06-12 11:00:00',
        '2025-06-12 13:00:00',
        'Checked-Out',
        'Family visit'
    ),
    (
        3,
        'Yaw Asante',
        'Friend',
        '+233501234587',
        '2025-06-13',
        NULL,
        NULL,
        'Pending',
        'Casual visit'
    ),
    (
        1,
        'Akosua Mensah',
        'Sister',
        '+233501234588',
        '2025-06-14',
        '2025-06-14 15:00:00',
        NULL,
        'Approved',
        'Family visit'
    ),
    (
        2,
        'Kwesi Nkrumah',
        'Friend',
        '+233501234589',
        '2025-06-15',
        '2025-06-15 09:00:00',
        '2025-06-15 11:00:00',
        'Checked-Out',
        'Study group'
    ),
    (
        3,
        'Adwoa Appiah',
        'Mother',
        '+233501234590',
        '2025-06-16',
        NULL,
        NULL,
        'Denied',
        'No prior notice'
    ),
    (
        1,
        'Kojo Yeboah',
        'Friend',
        '+233501234591',
        '2025-06-17',
        NULL,
        NULL,
        'Pending',
        'Casual visit'
    ),
    (
        2,
        'Efua Adu',
        'Sister',
        '+233501234592',
        '2025-06-18',
        '2025-06-18 14:00:00',
        NULL,
        'Checked-In',
        'Family visit'
    ),
    (
        3,
        'Kwame Boateng',
        'Brother',
        '+233501234593',
        '2025-06-19',
        NULL,
        NULL,
        'Approved',
        'Family visit'
    ),
    (
        1,
        'Abena Asante',
        'Friend',
        '+233501234594',
        '2025-06-20',
        '2025-06-20 12:00:00',
        '2025-06-20 14:00:00',
        'Checked-Out',
        'Study session'
    ),
    (
        2,
        'Kofi Mensah',
        'Cousin',
        '+233501234595',
        '2025-06-21',
        NULL,
        NULL,
        'Pending',
        'Casual visit'
    ),
    (
        3,
        'Ama Yeboah',
        'Aunt',
        '+233501234596',
        '2025-06-22',
        '2025-06-22 11:00:00',
        NULL,
        'Checked-In',
        'Family visit'
    ),
    (
        1,
        'Yaw Nkrumah',
        'Friend',
        '+233501234597',
        '2025-06-23',
        NULL,
        NULL,
        'Cancelled',
        'Cancelled visit'
    ),
    (
        2,
        'Akosua Appiah',
        'Sister',
        '+233501234598',
        '2025-06-24',
        '2025-06-24 15:00:00',
        '2025-06-24 17:00:00',
        'Checked-Out',
        'Family visit'
    ),
    (
        3,
        'Kwesi Adu',
        'Friend',
        '+233501234599',
        '2025-06-25',
        NULL,
        NULL,
        'Pending',
        'Study group'
    ),
    (
        1,
        'Adwoa Boateng',
        'Mother',
        '+233501234600',
        '2025-06-26',
        '2025-06-26 09:00:00',
        NULL,
        'Approved',
        'Family visit'
    ),
    (
        2,
        'Kojo Asante',
        'Brother',
        '+233501234601',
        '2025-06-27',
        NULL,
        NULL,
        'Denied',
        'Unauthorized visit'
    ),
    (
        3,
        'Efua Mensah',
        'Friend',
        '+233501234602',
        '2025-06-28',
        '2025-06-28 14:00:00',
        '2025-06-28 16:00:00',
        'Checked-Out',
        'Casual visit'
    ),
    (
        1,
        'Kwame Yeboah',
        'Cousin',
        '+233501234603',
        '2025-06-29',
        NULL,
        NULL,
        'Pending',
        'Dropping off items'
    ),
    (
        2,
        'Abena Adu',
        'Friend',
        '+233501234604',
        '2025-06-30',
        '2025-06-30 11:00:00',
        NULL,
        'Checked-In',
        'Study session'
    ),
    (
        3,
        'Kofi Boateng',
        'Brother',
        '+233501234605',
        '2025-07-01',
        NULL,
        NULL,
        'Approved',
        'Family visit'
    ),
    (
        1,
        'Ama Asante',
        'Aunt',
        '+233501234606',
        '2025-07-02',
        '2025-07-02 12:00:00',
        '2025-07-02 14:00:00',
        'Checked-Out',
        'Family visit'
    ),
    (
        2,
        'Yaw Mensah',
        'Friend',
        '+233501234607',
        '2025-07-03',
        NULL,
        NULL,
        'Pending',
        'Casual visit'
    ),
    (
        3,
        'Akosua Nkrumah',
        'Sister',
        '+233501234608',
        '2025-07-04',
        '2025-07-04 15:00:00',
        NULL,
        'Checked-In',
        'Family visit'
    ),
    (
        1,
        'Kwesi Appiah',
        'Friend',
        '+233501234609',
        '2025-07-05',
        NULL,
        NULL,
        'Cancelled',
        'Cancelled visit'
    ),
    (
        2,
        'Adwoa Adu',
        'Mother',
        '+233501234610',
        '2025-07-06',
        '2025-07-06 09:00:00',
        '2025-07-06 11:00:00',
        'Checked-Out',
        'Family visit'
    ),
    (
        3,
        'Kojo Boateng',
        'Brother',
        '+233501234611',
        '2025-07-07',
        NULL,
        NULL,
        'Pending',
        'Family visit'
    ),
    (
        1,
        'Efua Asante',
        'Friend',
        '+233501234612',
        '2025-07-08',
        '2025-07-08 14:00:00',
        NULL,
        'Approved',
        'Study group'
    ),
    (
        2,
        'Kwame Mensah',
        'Cousin',
        '+233501234613',
        '2025-07-09',
        NULL,
        NULL,
        'Denied',
        'No prior notice'
    ),
    (
        3,
        'Abena Nkrumah',
        'Friend',
        '+233501234614',
        '2025-07-10',
        '2025-07-10 11:00:00',
        '2025-07-10 13:00:00',
        'Checked-Out',
        'Casual visit'
    ),
    (
        1,
        'Kofi Appiah',
        'Brother',
        '+233501234615',
        '2025-07-11',
        NULL,
        NULL,
        'Pending',
        'Family visit'
    ),
    (
        2,
        'Ama Adu',
        'Aunt',
        '+233501234616',
        '2025-07-12',
        '2025-07-12 15:00:00',
        NULL,
        'Checked-In',
        'Family visit'
    ),
    (
        3,
        'Yaw Boateng',
        'Friend',
        '+233501234617',
        '2025-07-13',
        NULL,
        NULL,
        'Approved',
        'Study session'
    ),
    (
        1,
        'Akosua Asante',
        'Sister',
        '+233501234618',
        '2025-07-14',
        '2025-07-14 09:00:00',
        '2025-07-14 11:00:00',
        'Checked-Out',
        'Family visit'
    ),
    (
        2,
        'Kwesi Mensah',
        'Friend',
        '+233501234619',
        '2025-07-15',
        NULL,
        NULL,
        'Pending',
        'Casual visit'
    ),
    (
        3,
        'Adwoa Nkrumah',
        'Mother',
        '+233501234620',
        '2025-07-16',
        '2025-07-16 14:00:00',
        NULL,
        'Checked-In',
        'Family visit'
    ),
    (
        1,
        'Kojo Appiah',
        'Brother',
        '+233501234621',
        '2025-07-17',
        NULL,
        NULL,
        'Cancelled',
        'Cancelled visit'
    ),
    (
        2,
        'Efua Boateng',
        'Friend',
        '+233501234622',
        '2025-07-18',
        '2025-07-18 11:00:00',
        '2025-07-18 13:00:00',
        'Checked-Out',
        'Study group'
    ),
    (
        3,
        'Kwame Adu',
        'Cousin',
        '+233501234623',
        '2025-07-19',
        NULL,
        NULL,
        'Pending',
        'Casual visit'
    ),
    (
        1,
        'Abena Mensah',
        'Aunt',
        '+233501234624',
        '2025-07-20',
        '2025-07-20 15:00:00',
        NULL,
        'Approved',
        'Family visit'
    ),
    (
        2,
        'Kofi Nkrumah',
        'Friend',
        '+233501234625',
        '2025-07-21',
        NULL,
        NULL,
        'Denied',
        'Unauthorized visit'
    ),
    (
        3,
        'Ama Appiah',
        'Sister',
        '+233501234626',
        '2025-07-22',
        '2025-07-22 09:00:00',
        '2025-07-22 11:00:00',
        'Checked-Out',
        'Family visit'
    ),
    (
        1,
        'Yaw Asante',
        'Friend',
        '+233501234627',
        '2025-07-23',
        NULL,
        NULL,
        'Pending',
        'Study session'
    );

SELECT * FROM visitors;

INSERT INTO
    announcements (
        title,
        content,
        posted_by,
        priority,
        target_audience
    )
VALUES (
        'Maintenance Schedule',
        'Maintenance for the plumbing system in Hostel A is scheduled on 15th April 2025, from 9 AM to 12 PM. Please ensure your rooms are cleared before the scheduled time.',
        1,
        'High',
        'All'
    ),
    (
        'Holiday Visiting Hours',
        'The visiting hours for holidays are being adjusted from 8 AM - 6 PM to 7AM - 9PM. Please inform your visitors accordingly.',
        3,
        'Medium',
        'Students'
    ),
    (
        'New Room Availability',
        'We have added a new room in Hostel B on the 3rd floor. The room has a capacity of 2 and is available for students to use on weekdays from 10th April 2025.',
        3,
        'Low',
        'Students'
    ),
    (
        'Fire Drill',
        'A mandatory fire drill will take place on November 15th at 3pm. All residents must participate.',
        2,
        'urgent',
        'students'
    );

SELECT * FROM announcements;


-- Insert new maintenance requests
INSERT INTO
    maintenance_requests (
        student_id,
        room_id,
        issue_type,
        description,
        priority,
        status,
        request_date,
        completion_date
    )
VALUES
    -- Student 1 (Albert Smith, room 103, Hostel A)
    (
        1,
        1,
        'Structural',
        'Crack in the wall near the window',
        'High',
        'In-Progress',
        '2025-04-12 09:00:00',
        NULL
    ),
    (
        1,
        1,
        'Appliance',
        'Refrigerator not cooling properly',
        'Medium',
        'Pending',
        '2025-04-16 14:00:00',
        NULL
    ),
    -- Student 2 (Grace Brown, room 311, Hostel A)
    (
        2,
        5,
        'Furniture',
        'Desk drawer stuck and won’t open',
        'Low',
        'Pending',
        '2025-04-17 08:30:00',
        NULL
    ),
    (
        2,
        5,
        'Electrical',
        'Power outlet sparking intermittently',
        'Emergency',
        'Assigned',
        '2025-04-17 10:00:00',
        NULL
    ),
    -- Student 3 (John Doe, room 209, Hostel B)
    (
        1,
        6,
        'Plumbing',
        'Showerhead leaking constantly',
        'Medium',
        'In-Progress',
        '2025-04-14 11:00:00',
        NULL
    ),
    (
        2,
        6,
        'Appliance',
        'Air conditioner making loud noises',
        'High',
        'Completed',
        '2025-04-10 09:00:00',
        '2025-04-11 15:00:00'
    ),
    (
        1,
        6,
        'Structural',
        'Loose floor tiles in bathroom',
        'Low',
        'Rejected',
        '2025-04-08 12:00:00',
        '2025-04-09 10:00:00'
    );

INSERT INTO
    maintenance_requests (
        student_id,
        room_id,
        issue_type,
        description,
        priority,
        status,
        request_date,
        completion_date
    )
VALUES

    (
        1,
        1,
        'Structural',
        'Crack in the wall near the window',
        'High',
        'In-Progress',
        '2025-04-12 09:00:00',
        NULL
    ),
    (
        1,
        1,
        'Appliance',
        'Refrigerator not cooling properly',
        'Medium',
        'Pending',
        '2025-04-16 14:00:00',
        NULL
    ),
   
    (
        2,
        5,
        'Furniture',
        'Desk drawer stuck and won’t open',
        'Low',
        'Pending',
        '2025-04-17 08:30:00',
        NULL
    ),
    (
        2,
        5,
        'Electrical',
        'Power outlet sparking intermittently',
        'Emergency',
        'Assigned',
        '2025-04-17 10:00:00',
        NULL
    ),
    
    (
        2,
        6,
        'Plumbing',
        'Showerhead leaking constantly',
        'Medium',
        'In-Progress',
        '2025-04-14 11:00:00',
        NULL
    ),
    (
        1,
        6,
        'Appliance',
        'Air conditioner making loud noises',
        'High',
        'Completed',
        '2025-04-10 09:00:00',
        '2025-04-11 15:00:00'
    ),
    (
        1,
        6,
        'Structural',
        'Loose floor tiles in bathroom',
        'Low',
        'Rejected',
        '2025-04-08 12:00:00',
        '2025-04-09 10:00:00'
    );


-- Insert maintenance responses
INSERT INTO
    maintenance_responses (
        request_id,
        user_id,
        response_text,
        response_date
    )
VALUES (
        14,
        2,
        'Electrician assigned to inspect the light switch.',
        '2025-04-06 09:00:00'
    ),
    (
        16,
        3,
        'Thanks, when will the electrician arrive?',
        '2025-04-06 12:00:00'
    ),
    (
        28,
        2,
        'Hinge replaced; wardrobe fully functional.',
        '2025-03-21 14:30:00'
    ),
    (
        29,
        1,
        'Rejected: Construction noise is outside hostel control.',
        '2025-04-10 10:00:00'
    ),
    (
        15,
        2,
        'Technician scheduled to check AC on April 18.',
        '2025-04-16 08:00:00'
    ),
    (
        16,
        1,
        'Structural engineer assigned to assess the crack.',
        '2025-04-13 10:00:00'
    ),
    (
        17,
        1,
        'Plumber dispatched to fix the showerhead.',
        '2025-04-15 09:00:00'
    ),
    (
        29,
        2,
        'Please confirm the repair time.',
        '2025-04-15 11:00:00'
    ),
    (
        15,
        1,
        'AC repaired and tested.',
        '2025-04-11 15:00:00'
    ),
    (
        30,
        2,
        'Rejected: Tiles deemed safe; no immediate risk.',
        '2025-04-09 10:00:00'
    );


    -- Insert sample complaints
INSERT INTO
    complaints (
        student_id,
        room_id,
        complaint_type,
        description,
        priority,
        status,
        submitted_at
    )
VALUES (
        1,
        1,
        'Maintenance',
        'Leaking faucet in bathroom causing water pooling',
        'High',
        'In-Progress',
        '2025-01-05 08:00:00'
    ),
    (
        1,
        2,
        'Room Condition',
        'Broken window latch in room 102',
        'Medium',
        'Resolved',
        '2025-01-10 09:30:00'
    ),
    (
        1,
        NULL,
        'Staff Behavior',
        'Reception staff was rude during check-in',
        'Medium',
        'Rejected',
        '2025-01-15 14:00:00'
    ),
    (
        1,
        3,
        'Amenities',
        'No hot water in shower for 2 days',
        'High',
        'In-Progress',
        '2025-01-20 10:15:00'
    ),
    (
        1,
        4,
        'Noise',
        'Loud music from neighboring room at night',
        'Medium',
        'Pending',
        '2025-01-25 22:00:00'
    ),
    (
        1,
        NULL,
        'Security',
        'Main gate lock is faulty, opens easily',
        'Emergency',
        'In-Progress',
        '2025-01-30 07:00:00'
    ),
    (
        1,
        5,
        'Maintenance',
        'Ceiling fan not working in room 101-B',
        'Medium',
        'Resolved',
        '2025-02-02 11:00:00'
    ),
    (
        1,
        NULL,
        'Billing',
        'Overcharged for laundry services',
        'Low',
        'Pending',
        '2025-02-05 13:00:00'
    ),
    (
        1,
        6,
        'Room Condition',
        'Mold on bathroom walls',
        'High',
        'In-Progress',
        '2025-02-10 09:00:00'
    ),
    (
        1,
        7,
        'Amenities',
        'Broken TV in common room',
        'Low',
        'Resolved',
        '2025-02-15 15:30:00'
    ),
    (
        1,
        NULL,
        'Other',
        'Cafeteria food quality has declined',
        'Medium',
        'Pending',
        '2025-02-20 12:00:00'
    ),
    (
        1,
        8,
        'Maintenance',
        'Clogged sink in room 202-B',
        'High',
        'In-Progress',
        '2025-02-25 08:30:00'
    ),
    (
        1,
        9,
        'Noise',
        'Construction noise outside hostel at 6 AM',
        'Medium',
        'Resolved',
        '2025-03-01 06:00:00'
    ),
    (
        1,
        NULL,
        'Staff Behavior',
        'Security guard ignored my request for help',
        'Medium',
        'Rejected',
        '2025-03-05 10:00:00'
    ),
    (
        1,
        10,
        'Security',
        'CCTV camera in hallway not working',
        'Emergency',
        'In-Progress',
        '2025-03-10 09:00:00'
    ),
    (
        1,
        1,
        'Room Condition',
        'Peeling paint on room walls',
        'Low',
        'Pending',
        '2025-03-15 14:00:00'
    ),
    (
        1,
        2,
        'Maintenance',
        'Broken door lock in room 102',
        'High',
        'Resolved',
        '2025-03-20 11:00:00'
    ),
    (
        1,
        NULL,
        'Billing',
        'Incorrect electricity bill for January',
        'Medium',
        'In-Progress',
        '2025-03-25 13:00:00'
    ),
    (
        1,
        3,
        'Amenities',
        'Wi-Fi not working in common area',
        'Medium',
        'Resolved',
        '2025-03-30 15:00:00'
    ),
    (
        1,
        4,
        'Noise',
        'Noisy AC unit in room 202',
        'Medium',
        'Pending',
        '2025-04-01 20:00:00'
    ),
    (
        1,
        NULL,
        'Other',
        'Lack of recycling bins in hostel',
        'Low',
        'Pending',
        '2025-04-05 10:00:00'
    ),
    (
        1,
        5,
        'Maintenance',
        'Leaking roof during rain',
        'Emergency',
        'In-Progress',
        '2025-04-07 09:00:00'
    ),
    (
        1,
        6,
        'Room Condition',
        'Cracked floor tiles in bathroom',
        'Medium',
        'Resolved',
        '2025-04-10 12:00:00'
    ),
    (
        1,
        NULL,
        'Staff Behavior',
        'Cleaning staff skipped my room',
        'Medium',
        'In-Progress',
        '2025-04-12 11:00:00'
    ),
    (
        1,
        7,
        'Security',
        'Unidentified person entered hostel without ID check',
        'Emergency',
        'Resolved',
        '2025-04-15 08:00:00'
    );

-- Update resolved_at for resolved/rejected complaints
INSERT INTO
    complaints (
        student_id,
        room_id,
        complaint_type,
        description,
        priority,
        status,
        submitted_at
    )
VALUES (
        2,
        8,
        'Maintenance',
        'Faulty electrical outlet in room 202-B',
        'High',
        'In-Progress',
        '2025-01-06 09:00:00'
    ),
    (
        2,
        9,
        'Room Condition',
        'Stained mattress in room 301',
        'Medium',
        'Resolved',
        '2025-01-12 10:00:00'
    ),
    (
        2,
        NULL,
        'Staff Behavior',
        'Cafeteria staff ignored dietary restrictions',
        'Medium',
        'Rejected',
        '2025-01-18 12:00:00'
    ),
    (
        2,
        10,
        'Amenities',
        'Gym equipment broken for a week',
        'Low',
        'Pending',
        '2025-01-22 14:00:00'
    ),
    (
        2,
        1,
        'Noise',
        'Loud parties in common area after midnight',
        'Medium',
        'In-Progress',
        '2025-01-28 23:00:00'
    ),
    (
        2,
        NULL,
        'Security',
        'Lost key not replaced promptly',
        'High',
        'Resolved',
        '2025-02-01 08:00:00'
    ),
    (
        2,
        2,
        'Maintenance',
        'Broken showerhead in room 102',
        'Medium',
        'Resolved',
        '2025-02-04 10:00:00'
    ),
    (
        2,
        NULL,
        'Billing',
        'Double-charged for room rent',
        'High',
        'In-Progress',
        '2025-02-08 11:00:00'
    ),
    (
        2,
        3,
        'Room Condition',
        'Bad odor in room 201',
        'Medium',
        'Pending',
        '2025-02-12 09:00:00'
    ),
    (
        2,
        4,
        'Amenities',
        'No water dispenser in hostel block',
        'Low',
        'Resolved',
        '2025-02-18 13:00:00'
    ),
    (
        2,
        NULL,
        'Other',
        'No study room available during exams',
        'Medium',
        'Pending',
        '2025-02-22 15:00:00'
    ),
    (
        2,
        5,
        'Maintenance',
        'AC not cooling properly in room 101-B',
        'High',
        'In-Progress',
        '2025-02-26 10:00:00'
    ),
    (
        2,
        6,
        'Noise',
        'Noisy plumbing pipes at night',
        'Medium',
        'Resolved',
        '2025-03-02 22:00:00'
    ),
    (
        2,
        NULL,
        'Staff Behavior',
        'Maintenance staff entered room without permission',
        'High',
        'In-Progress',
        '2025-03-06 08:00:00'
    ),
    (
        2,
        7,
        'Security',
        'Broken window in common room',
        'Emergency',
        'Resolved',
        '2025-03-12 09:00:00'
    ),
    (
        2,
        8,
        'Room Condition',
        'Loose door hinges in room 202-B',
        'Medium',
        'Pending',
        '2025-03-16 11:00:00'
    ),
    (
        2,
        9,
        'Maintenance',
        'Flickering lights in room 301',
        'Medium',
        'Resolved',
        '2025-03-22 12:00:00'
    ),
    (
        2,
        NULL,
        'Billing',
        'Late fee applied incorrectly',
        'Medium',
        'In-Progress',
        '2025-03-26 14:00:00'
    ),
    (
        2,
        10,
        'Amenities',
        'Laundry machine out of service',
        'Low',
        'Pending',
        '2025-03-31 13:00:00'
    ),
    (
        2,
        1,
        'Noise',
        'Students shouting in hallway at 2 AM',
        'Medium',
        'In-Progress',
        '2025-04-02 02:00:00'
    ),
    (
        2,
        NULL,
        'Other',
        'No parking space for visitors',
        'Low',
        'Pending',
        '2025-04-06 10:00:00'
    ),
    (
        2,
        2,
        'Maintenance',
        'Broken toilet flush valve in room 102',
        'High',
        'In-Progress',
        '2025-04-08 09:00:00'
    ),
    (
        2,
        3,
        'Room Condition',
        'Damaged wardrobe in room 201',
        'Medium',
        'Resolved',
        '2025-04-11 11:00:00'
    ),
    (
        2,
        NULL,
        'Staff Behavior',
        'Security guard was unresponsive',
        'Medium',
        'Rejected',
        '2025-04-13 10:00:00'
    ),
    (
        2,
        4,
        'Security',
        'Emergency exit blocked by furniture',
        'Emergency',
        'Resolved',
        '2025-04-16 08:00:00'
    );

    UPDATE complaints
SET
    resolved_at = DATE_ADD(submitted_at, INTERVAL 2 DAY)
WHERE
    status IN ('Resolved', 'Rejected');


    INSERT INTO
    complaint_responses (
        complaint_id,
        admin_id,
        response_text,
        action_taken,
        response_date
    )
VALUES
    -- Student 1 Complaints
    (
        1,
        1,
        'Assigned to plumbing team for immediate repair',
        'Assigned',
        '2025-01-06 09:00:00'
    ),
    (
        1,
        1,
        'Plumber scheduled to visit tomorrow at 10 AM',
        'Updated',
        '2025-01-07 08:00:00'
    ),
    (
        2,
        1,
        'Window latch replaced successfully',
        'Resolved',
        '2025-01-12 11:00:00'
    ),
    (
        3,
        1,
        'Investigated; no evidence of misconduct found',
        'Rejected',
        '2025-01-17 10:00:00'
    ),
    (
        4,
        1,
        'Assigned to maintenance to check water heater',
        'Assigned',
        '2025-01-21 09:00:00'
    ),
    (
        6,
        1,
        'Security team dispatched to inspect gate',
        'Assigned',
        '2025-01-31 08:00:00'
    ),
    (
        6,
        1,
        'New lock installed on main gate',
        'Updated',
        '2025-02-01 10:00:00'
    ),
    (
        7,
        1,
        'Fan repaired and tested',
        'Resolved',
        '2025-02-04 14:00:00'
    ),
    (
        9,
        1,
        'Assigned to cleaning team to address mold',
        'Assigned',
        '2025-02-11 10:00:00'
    ),
    (
        10,
        1,
        'New TV installed in common room',
        'Resolved',
        '2025-02-17 12:00:00'
    ),
    (
        12,
        1,
        'Plumber assigned to unclog sink',
        'Assigned',
        '2025-02-26 09:00:00'
    ),
    (
        12,
        1,
        'Sink unclogged; issue resolved',
        'Updated',
        '2025-02-27 11:00:00'
    ),
    (
        13,
        1,
        'Construction team instructed to start later',
        'Resolved',
        '2025-03-03 08:00:00'
    ),
    (
        14,
        1,
        'No evidence of security lapse; complaint rejected',
        'Rejected',
        '2025-03-07 09:00:00'
    ),
    (
        15,
        1,
        'CCTV technician scheduled for repair',
        'Assigned',
        '2025-03-11 10:00:00'
    ),
    (
        17,
        1,
        'Lock replaced and tested',
        'Resolved',
        '2025-03-22 13:00:00'
    ),
    (
        18,
        1,
        'Billing team reviewing electricity charges',
        'Assigned',
        '2025-03-26 10:00:00'
    ),
    (
        19,
        1,
        'Wi-Fi router reset and upgraded',
        'Resolved',
        '2025-04-01 12:00:00'
    ),
    (
        22,
        1,
        'Roof repair team assigned',
        'Assigned',
        '2025-04-08 10:00:00'
    ),
    (
        23,
        1,
        'Floor tiles replaced',
        'Resolved',
        '2025-04-12 14:00:00'
    ),
    (
        24,
        1,
        'Cleaning schedule adjusted',
        'Assigned',
        '2025-04-13 09:00:00'
    ),
    (
        25,
        1,
        'Security protocols tightened; issue addressed',
        'Resolved',
        '2025-04-17 10:00:00'
    ),
    -- Student 2 Complaints
    (
        26,
        1,
        'Electrician assigned to fix outlet',
        'Assigned',
        '2025-01-07 10:00:00'
    ),
    (
        27,
        1,
        'Mattress replaced with new one',
        'Resolved',
        '2025-01-14 12:00:00'
    ),
    (
        28,
        1,
        'Dietary issue not reported earlier; rejected',
        'Rejected',
        '2025-01-20 09:00:00'
    ),
    (
        30,
        1,
        'Security warned about noise; monitoring situation',
        'Assigned',
        '2025-01-29 09:00:00'
    ),
    (
        31,
        1,
        'New key issued and lock changed',
        'Resolved',
        '2025-02-03 10:00:00'
    ),
    (
        32,
        1,
        'Showerhead replaced',
        'Resolved',
        '2025-02-06 11:00:00'
    ),
    (
        33,
        1,
        'Billing team investigating double charge',
        'Assigned',
        '2025-02-09 10:00:00'
    ),
    (
        33,
        1,
        'Refund processed for overcharge',
        'Updated',
        '2025-02-10 12:00:00'
    ),
    (
        35,
        1,
        'Water dispenser installed in block',
        'Resolved',
        '2025-02-20 14:00:00'
    ),
    (
        37,
        1,
        'AC technician scheduled for repair',
        'Assigned',
        '2025-02-27 09:00:00'
    ),
    (
        38,
        1,
        'Plumbing issue fixed',
        'Resolved',
        '2025-03-04 10:00:00'
    ),
    (
        39,
        1,
        'Staff retrained on entry protocols',
        'Assigned',
        '2025-03-07 09:00:00'
    ),
    (
        40,
        1,
        'Window repaired and secured',
        'Resolved',
        '2025-03-14 11:00:00'
    ),
    (
        42,
        1,
        'Electrician fixed lighting issue',
        'Resolved',
        '2025-03-24 13:00:00'
    ),
    (
        43,
        1,
        'Billing team correcting late fee',
        'Assigned',
        '2025-03-27 10:00:00'
    ),
    (
        46,
        1,
        'Security warned about noise violations',
        'Assigned',
        '2025-04-03 09:00:00'
    ),
    (
        47,
        1,
        'Plumber assigned to fix flush valve',
        'Assigned',
        '2025-04-09 10:00:00'
    ),
    (
        48,
        1,
        'Wardrobe repaired',
        'Resolved',
        '2025-04-13 12:00:00'
    ),
    (
        49,
        1,
        'No security lapse found; complaint rejected',
        'Rejected',
        '2025-04-15 09:00:00'
    ),
    (
        50,
        1,
        'Emergency exit cleared and checked',
        'Resolved',
        '2025-04-17 10:00:00'
    );

-- these queries displays all the information in the tables created
SELECT * FROM users;

SELECT * FROM students;

SELECT * FROM admins;

SELECT * FROM rooms;

SELECT * FROM allocations;

SELECT * FROM visitors;

SELECT * FROM announcements;

SELECT * FROM maintenance_requests;

SELECT * FROM payments;

SELECT * FROM billing;

SELECT * FROM disciplinary_records;

SELECT * FROM verification_codes;

SELECT * FROM remember_tokens;

-- This query joins Students, Allocations, and Rooms tables to display each student's accommodation details
SELECT
    s.student_id,
    CONCAT(
        s.first_name,
        ' ',
        s.last_name
    ) AS student_name,
    r.building,
    r.room_number,
    r.room_type,
    a.start_date,
    a.end_date,
    a.status AS allocation_status
FROM
    Students s
    JOIN Allocations a ON s.student_id = a.student_id
    JOIN Rooms r ON a.room_id = r.room_id
WHERE
    a.status = 'active';

--This query helps administrators identify issues that need attention, it displays maintenance_requests that are pending.
SELECT mr.request_id, CONCAT(
        s.first_name, ' ', s.last_name
    ) AS requested_by, r.building, r.room_number, mr.issue_type, mr.description, mr.priority, mr.request_date
FROM
    maintenance_requests mr
    JOIN students s ON mr.student_id = s.student_id
    JOIN rooms r ON mr.room_id = r.room_id
WHERE
    mr.status = 'Pending'
ORDER BY FIELD(
        mr.priority, 'Emergency', 'High', 'Medium', 'Low'
    ), mr.request_date;

--List all students with their room allocations
SELECT s.first_name, s.last_name, r.room_number, a.start_date
FROM
    Students s
    JOIN Allocations a ON s.student_id = a.student_id
    JOIN Rooms r ON a.room_id = r.room_id;

--Show all approved visitors
SELECT v.visitor_name, v.relation, v.visit_date, v.check_in_time, v.check_out_time
FROM Visitors v
WHERE
    v.status = 'Approved';

--List all high-priority maintenance requests
SELECT mr.request_id, mr.issue_type, mr.description, mr.priority, mr.status
FROM maintenance_requests mr
WHERE
    mr.priority = 'High';

--List all rooms that are currently vacant
SELECT r.room_id, r.room_number, r.building, r.floor, r.room_type, r.room_type, r.status
FROM Rooms r
WHERE
    r.status = 'Vacant';

-- Show all students with active allocations
SELECT s.student_id, s.first_name, s.last_name, r.room_number, a.start_date
FROM
    Students s
    JOIN Allocations a ON s.student_id = a.student_id
    JOIN Rooms r ON a.room_id = r.room_id
WHERE
    a.status = 'Active';

-- Show all visitors for a specific student (e.g., student_id = 1)
SELECT v.visitor_name, v.relation, v.visit_date, v.check_in_time, v.check_out_time
FROM Visitors v
WHERE
    v.student_id = 1;

-- List all announcements posted by a specific admin (e.g., admin_id = 1)
SELECT a.title, a.content, a.priority, a.target_audience
FROM Announcements a
WHERE
    a.posted_by = 1;

-- Ensure proper order of dropping tables to avoid foreign key constraint errors
DROP TABLE IF EXISTS remember_tokens;

DROP TABLE IF EXISTS verification_codes;

DROP TABLE IF EXISTS billing;

DROP TABLE IF EXISTS payments;

DROP TABLE IF EXISTS disciplinary_records;

DROP TABLE IF EXISTS maintenance_requests;

DROP TABLE IF EXISTS announcements;

DROP TABLE IF EXISTS visitors;

DROP TABLE IF EXISTS allocations;

DROP TABLE IF EXISTS rooms;

DROP TABLE IF EXISTS admins;

DROP TABLE IF EXISTS students;

DROP TABLE IF EXISTS users;

DROP DATABASE IF EXISTS hostel_management;