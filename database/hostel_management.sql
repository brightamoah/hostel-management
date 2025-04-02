-- Create a database named 'hostel_management'
CREATE DATABASE hostel_management;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Student', 'Admin') NOT NULL DEFAULT 'Student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

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
    health_condition TEXT,
    enrollment_date DATE NOT NULL,
    resident_status ENUM(
        'Active',
        'Inactive',
        'Suspended'
    ) DEFAULT 'Active',
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
);

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
    room_number INT NOT NULL,
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

CREATE TABLE visitors (
    visitor_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    visitor_name VARCHAR(100) NOT NULL,
    relation VARCHAR(50) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    visit_date DATE NOT NULL,
    check_in_time TIMESTAMP,
    check_out_time TIMESTAMP NULL,
    status ENUM(
        'Pending',
        'Approved',
        'Checked-In',
        'Checked-Out',
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
    payment_id INT NOT NULL,
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

--Add some data to the tables
INSERT INTO
    users (name, email, password, role)
VALUES (
        'Bright Amoah',
        'brghtmalone@gmail.com',
        'Admin@2025!',
        'Admin'
    ),
    (
        'Albert Smith',
        'asmith@test.com',
        'Test@2025!',
        'Student'
    ),
    (
        'Grace Brown',
        'grace@test.com',
        'Test@2025!',
        'Student'
    ),
    (
        'John Doe',
        'john@test.com',
        'Test@2025!',
        'Student'
    ),
    (
        'Jane Doe',
        'jane@test.com',
        'Admin@2025!',
        'Admin'
    ),
    (
        'Michael Johnson',
        'mjohnson@test.com',
        'Admin@2025!',
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
        status
    )
VALUES (
        101,
        'Hostel A',
        1,
        'Single',
        1,
        0,
        'Air-conditioned, Wi-Fi',
        'Vacant'
    ),
    (
        102,
        'Hostel B',
        1,
        'Single',
        1,
        1,
        'Air-conditioned, Wi-Fi, Balcony',
        'Fully Occupied'
    ),
    (
        205,
        'Hostel A',
        2,
        'Double',
        2,
        2,
        'Air-conditioned, Wi-Fi',
        'Fully Occupied'
    ),
    (
        309,
        'Hostel A',
        3,
        'Triple',
        3,
        1,
        'Wi-Fi',
        'Partially Occupied'
    ),
    (
        206,
        'Hostel B',
        2,
        'Double',
        2,
        0,
        'Air-conditioned, Wi-Fi',
        'Vacant'
    ),
    (
        310,
        'Hostel B',
        3,
        'Triple',
        3,
        0,
        'Air-conditioned, Wi-Fi',
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
        2,
        'John Doe',
        'Friend',
        '+2335012345678',
        '2025-04-10',
        '2025-04-10 10:00:00',
        NULL,
        'Approved',
        'Study group meeting'
    ),
    (
        3,
        'Mary Johnson',
        'Mother',
        '+2335098765432',
        '2025-04-20',
        '2025-04-20 09:00:00',
        '2025-04-20 12:00:00',
        'Checked-out',
        'Family visits'
    ),
    (
        1,
        'Jane Doe',
        'Sister',
        '+2335012345678',
        '2025-04-15',
        NULL,
        NULL,
        'Pending',
        'Family visits'
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

INSERT INTO
    maintenance_requests (
        student_id,
        room_id,
        issue_type,
        description,
        priority,
        status
    )
VALUES (
        3,
        3,
        'plumbing',
        'Leaking faucet in the bathroom.',
        'medium',
        'assigned'
    ),
    (
        2,
        1,
        'electrical',
        'Lights flickering in the study area.',
        'high',
        'in-progress'
    ),
    (
        1,
        2,
        'furniture',
        'Broken chair leg.',
        'low',
        'pending'
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