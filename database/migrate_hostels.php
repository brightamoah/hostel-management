<?php

/**
 * Hostel Management System Migration Script
 * Run this script to add hostel support to existing database
 */

require_once __DIR__ . "/../database/db.php";

echo "Starting Hostel Management System Migration...\n";

try {
    $conn = getDb();
    $conn->begin_transaction();

    echo "1. Checking if hostels table exists...\n";
    $result = $conn->query("SHOW TABLES LIKE 'hostels'");
    if ($result->num_rows == 0) {
        echo "   Creating hostels table...\n";
        $hostel_sql = "
        CREATE TABLE hostels (
            hostel_id INT AUTO_INCREMENT PRIMARY KEY,
            hostel_name VARCHAR(100) NOT NULL UNIQUE,
            hostel_code VARCHAR(10) NOT NULL UNIQUE,
            address TEXT,
            contact_phone VARCHAR(20),
            contact_email VARCHAR(100),
            manager_id INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('Active', 'Inactive', 'Under Construction') DEFAULT 'Active'
        ) ENGINE = InnoDB";
        $conn->query($hostel_sql);
        echo "   ✓ Hostels table created\n";
    } else {
        echo "   ✓ Hostels table already exists\n";
    }

    echo "2. Checking if hostel_id column exists in admins table...\n";
    $result = $conn->query("SHOW COLUMNS FROM admins LIKE 'hostel_id'");
    if ($result->num_rows == 0) {
        echo "   Adding hostel_id column to admins table...\n";
        $conn->query("ALTER TABLE admins ADD COLUMN hostel_id INT NULL AFTER access_level");
        echo "   ✓ hostel_id column added to admins table\n";
    } else {
        echo "   ✓ hostel_id column already exists in admins table\n";
    }

    echo "3. Checking if hostel_id column exists in rooms table...\n";
    $result = $conn->query("SHOW COLUMNS FROM rooms LIKE 'hostel_id'");
    if ($result->num_rows == 0) {
        echo "   Adding hostel_id column to rooms table...\n";
        $conn->query("ALTER TABLE rooms ADD COLUMN hostel_id INT NULL AFTER room_id");
        echo "   ✓ hostel_id column added to rooms table\n";
    } else {
        echo "   ✓ hostel_id column already exists in rooms table\n";
    }

    echo "4. Creating default hostels from existing buildings...\n";
    // Check if we have any buildings without hostels
    $buildings_result = $conn->query("
        SELECT DISTINCT building 
        FROM rooms 
        WHERE building IS NOT NULL AND building != ''
        AND NOT EXISTS (
            SELECT 1 FROM hostels WHERE hostel_name = CONCAT(rooms.building, ' Hostel')
        )
    ");

    if ($buildings_result->num_rows > 0) {
        while ($row = $buildings_result->fetch_assoc()) {
            $building = $row['building'];
            $hostel_name = $building . ' Hostel';
            $hostel_code = substr(strtoupper(str_replace(' ', '', $building)), 0, 3) . '_' . rand(100, 999);
            $address = "Building $building";

            $stmt = $conn->prepare("INSERT INTO hostels (hostel_name, hostel_code, address, status) VALUES (?, ?, ?, 'Active')");
            $stmt->bind_param("sss", $hostel_name, $hostel_code, $address);
            $stmt->execute();
            $stmt->close();

            echo "   ✓ Created hostel: $hostel_name\n";
        }
    } else {
        echo "   ✓ Default hostels already exist\n";
    }

    echo "5. Updating rooms with hostel assignments...\n";
    $update_result = $conn->query("
        UPDATE rooms r
        JOIN hostels h ON h.hostel_name = CONCAT(r.building, ' Hostel')
        SET r.hostel_id = h.hostel_id
        WHERE r.hostel_id IS NULL
    ");
    echo "   ✓ Updated {$conn->affected_rows} rooms with hostel assignments\n";

    echo "6. Adding foreign key constraints...\n";

    // Check if foreign key exists for hostels->admins
    $fk_result = $conn->query("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'hostels' 
        AND COLUMN_NAME = 'manager_id'
        AND REFERENCED_TABLE_NAME = 'admins'
    ");

    if ($fk_result->num_rows == 0) {
        $conn->query("ALTER TABLE hostels ADD FOREIGN KEY (manager_id) REFERENCES admins (admin_id) ON DELETE SET NULL");
        echo "   ✓ Added foreign key: hostels.manager_id -> admins.admin_id\n";
    }

    // Check if foreign key exists for admins->hostels
    $fk_result = $conn->query("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'admins' 
        AND COLUMN_NAME = 'hostel_id'
        AND REFERENCED_TABLE_NAME = 'hostels'
    ");

    if ($fk_result->num_rows == 0) {
        $conn->query("ALTER TABLE admins ADD FOREIGN KEY (hostel_id) REFERENCES hostels (hostel_id) ON DELETE SET NULL");
        echo "   ✓ Added foreign key: admins.hostel_id -> hostels.hostel_id\n";
    }

    // Check if foreign key exists for rooms->hostels
    $fk_result = $conn->query("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'rooms' 
        AND COLUMN_NAME = 'hostel_id'
        AND REFERENCED_TABLE_NAME = 'hostels'
    ");

    if ($fk_result->num_rows == 0) {
        $conn->query("ALTER TABLE rooms ADD FOREIGN KEY (hostel_id) REFERENCES hostels (hostel_id) ON DELETE RESTRICT");
        echo "   ✓ Added foreign key: rooms.hostel_id -> hostels.hostel_id\n";
    }

    echo "7. Creating indexes for better performance...\n";

    // Check if indexes exist
    $index_result = $conn->query("SHOW INDEX FROM rooms WHERE Key_name = 'idx_rooms_hostel_id'");
    if ($index_result->num_rows == 0) {
        $conn->query("CREATE INDEX idx_rooms_hostel_id ON rooms(hostel_id)");
        echo "   ✓ Created index: idx_rooms_hostel_id\n";
    }

    $index_result = $conn->query("SHOW INDEX FROM admins WHERE Key_name = 'idx_admins_hostel_id'");
    if ($index_result->num_rows == 0) {
        $conn->query("CREATE INDEX idx_admins_hostel_id ON admins(hostel_id)");
        echo "   ✓ Created index: idx_admins_hostel_id\n";
    }

    echo "8. Making hostel_id NOT NULL for rooms after migration...\n";
    // Check if there are any rooms without hostel_id
    $null_rooms = $conn->query("SELECT COUNT(*) as count FROM rooms WHERE hostel_id IS NULL")->fetch_assoc()['count'];
    switch ($null_rooms) {
        case 0:
            $column_info = $conn->query("SHOW COLUMNS FROM rooms WHERE Field = 'hostel_id'")->fetch_assoc();
            switch ($column_info['Null']) {
                case 'YES':
                    $conn->query("ALTER TABLE rooms MODIFY COLUMN hostel_id INT NOT NULL");
                    echo "   ✓ Made rooms.hostel_id NOT NULL\n";
                    break;
                default:
                    echo "   ✓ rooms.hostel_id is already NOT NULL\n";
                    break;
            }
            break;
        default:
            echo "   ⚠ Warning: $null_rooms rooms still have NULL hostel_id. Skipping NOT NULL constraint.\n";
            break;
    }

    echo "9. Creating default Super Admin assignment...\n";
    // Assign first admin as Super Admin if no Super Admin exists
    $super_admin_count = $conn->query("SELECT COUNT(*) as count FROM admins WHERE access_level = 'Super Admin'")->fetch_assoc()['count'];
    switch ($super_admin_count) {
        case 0:
            $first_admin = $conn->query("SELECT admin_id FROM admins ORDER BY admin_id LIMIT 1")->fetch_assoc();
            if ($first_admin) {
                $conn->query("UPDATE admins SET access_level = 'Super Admin' WHERE admin_id = " . $first_admin['admin_id']);
                echo "   ✓ Assigned first admin (ID: {$first_admin['admin_id']}) as Super Admin\n";
            }
            break;
        default:
            echo "   ✓ Super Admin already exists\n";
            break;
    }

    $conn->commit();
    echo "\n✅ Migration completed successfully!\n";

    // Display summary
    echo "\n📊 Migration Summary:\n";
    $hostels_count = $conn->query("SELECT COUNT(*) as count FROM hostels")->fetch_assoc()['count'];
    $rooms_count = $conn->query("SELECT COUNT(*) as count FROM rooms")->fetch_assoc()['count'];
    $admins_count = $conn->query("SELECT COUNT(*) as count FROM admins")->fetch_assoc()['count'];

    echo "   - Hostels created: $hostels_count\n";
    echo "   - Rooms assigned to hostels: $rooms_count\n";
    echo "   - Total admins: $admins_count\n";

    echo "\n🔧 Next Steps:\n";
    echo "   1. Assign admins to specific hostels through the admin interface\n";
    echo "   2. Test room access and filtering functionality\n";
    echo "   3. Verify that Super Admins can see all hostels\n";
    echo "   4. Regular admins should only see their assigned hostel data\n";
} catch (Exception $e) {
    $conn->rollback();
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
