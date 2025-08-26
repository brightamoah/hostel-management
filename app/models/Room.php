<?php
require_once __DIR__ . "/BaseModel.php";

class Rooms extends BaseModel
{
    // Fetch available rooms (for students)
    public function getAvailableRooms()
    {
        $query = "
            SELECT room_id, room_number, building, floor, room_type, capacity, current_occupancy, features, amount, status, hostel_id
            FROM rooms
            WHERE status IN ('Vacant', 'Partially Occupied')
            AND current_occupancy < capacity
        ";

        // Add hostel filtering for non-super admins
        $query = $this->addHostelFilter($query);
        $query .= " ORDER BY building, room_number";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $rooms = [];
        while ($row = $result->fetch_assoc()) {
            $rooms[] = $row;
        }
        return $rooms;
    }

    // Fetch all rooms (for admin)
    public function getAllRooms()
    {
        $query = "
            SELECT room_id, room_number, building, floor, room_type, capacity, current_occupancy, features, amount, status, hostel_id
            FROM rooms
            WHERE 1=1
        ";

        // Add hostel filtering for non-super admins
        $query = $this->addHostelFilter($query);
        $query .= " ORDER BY building, room_number";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $rooms = [];
        while ($row = $result->fetch_assoc()) {
            $rooms[] = $row;
        }
        return $rooms;
    }

    // Get a single room by ID
    public function getRoomById($room_id)
    {
        $query = "
            SELECT room_id, room_number, building, floor, room_type, capacity, current_occupancy, features, amount, status, hostel_id
            FROM rooms
            WHERE room_id = ?
        ";

        // Add hostel filtering for non-super admins
        $query = $this->addHostelFilter($query);

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $room = $result->fetch_assoc();

        // Validate hostel access if room exists
        if ($room && !$this->isSuperAdmin()) {
            $this->validateHostelAccess($room['hostel_id']);
        }

        error_log("Get Room By ID $room_id: " . json_encode($room));
        return $room;
    }

    // Add a new room
    public function addRoom($room_number, $building, $floor, $room_type, $capacity, $features, $amount, $status = 'Vacant')
    {
        // Ensure features is a string, even if null or empty is passed.
        $features_str = $features ?? '';
        $current_occupancy = 0; // New rooms start empty

        // Validate capacity (as per DB constraint chk_capacity)
        if (!is_numeric($capacity) || $capacity <= 0) {
            error_log("Add Room - Invalid Capacity: Must be a positive number.");
            return false;
        }
        // Validate amount
        if (!is_numeric($amount) || $amount < 0) {
            error_log("Add Room - Invalid Amount: Must be a non-negative number.");
            return false;
        }

        // Get hostel_id for the new room
        $hostel_id = $this->getCurrentAdminHostelId();
        if (!$this->isSuperAdmin() && !$hostel_id) {
            error_log("Add Room - Error: Admin not assigned to any hostel");
            return false;
        }

        $query = "
            INSERT INTO rooms (room_number, building, floor, room_type, capacity, current_occupancy, features, amount, status, hostel_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Add Room - Prepare Error: " . $this->conn->error . " (Code: " . $this->conn->errno . ")");
            return false;
        }

        $stmt->bind_param(
            "ssisiisdsi",
            $room_number,
            $building,
            $floor,
            $room_type,
            $capacity,
            $current_occupancy,
            $features_str,
            $amount,
            $status,
            $hostel_id
        );

        $result = $stmt->execute();
        if (!$result) {
            error_log("Add Room - Execute Error: {$stmt->error} (Code: {$stmt->errno})");
            if ($stmt->errno == 1062) {
                error_log("Add Room - Error: Duplicate entry for building '$building' and room_number '$room_number'.");
            }
        } else {
            $new_room_id = $this->conn->insert_id;
            error_log("Add Room - Success: Room ID $new_room_id added with features: '$features_str' for hostel_id: $hostel_id");
        }
        $stmt->close();
        return $result;
    }

    // Update a room
    public function updateRoom($room_id, $room_number, $building, $floor, $room_type, $capacity, $current_occupancy, $features, $amount, $status)
    {
        // Ensure features is a string
        $features_str = $features ?? '';

        // Basic Validation
        if (!is_numeric($room_id) || $room_id <= 0) {
            error_log("Update Room - Invalid Room ID.");
            return false;
        }
        if (!is_numeric($capacity) || $capacity <= 0) {
            error_log("Update Room - Invalid Capacity: Must be a positive number for Room ID $room_id.");
            return false;
        }
        if (!is_numeric($current_occupancy) || $current_occupancy < 0 || $current_occupancy > $capacity) {
            error_log("Update Room - Invalid Occupancy: Must be between 0 and capacity ($capacity) for Room ID $room_id.");
            return false;
        }
        if (!is_numeric($amount) || $amount < 0) {
            error_log("Update Room - Invalid Amount: Must be a non-negative number for Room ID $room_id.");
            return false;
        }

        // Validate hostel access for the room being updated
        $existing_room = $this->getRoomById($room_id);
        if (!$existing_room) {
            error_log("Update Room - Room ID $room_id not found or access denied.");
            return false;
        }

        $query = "
            UPDATE rooms
            SET room_number = ?, building = ?, floor = ?, room_type = ?, capacity = ?,
                current_occupancy = ?, features = ?, amount = ?, status = ?
            WHERE room_id = ?
        ";

        // Add hostel filtering for non-super admins
        $query = $this->addHostelFilter($query);

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Update Room - Prepare Error: " . $this->conn->error . " (Code: " . $this->conn->errno . ")");
            return false;
        }

        $stmt->bind_param(
            "ssisiisdsi",
            $room_number,
            $building,
            $floor,
            $room_type,
            $capacity,
            $current_occupancy,
            $features_str,
            $amount,
            $status,
            $room_id
        );

        $result = $stmt->execute();
        if (!$result) {
            error_log("Update Room - Execute Error for Room ID $room_id: {$stmt->error} (Code: {$stmt->errno})");
            if ($stmt->errno == 1062) {
                error_log("Update Room - Error: Duplicate entry for building '$building' and room_number '$room_number'.");
            }
        } else {
            if ($stmt->affected_rows > 0) {
                error_log("Update Room - Success: Room ID $room_id updated. Features set to: '$features_str'. Affected Rows: {$stmt->affected_rows}");
            } else {
                error_log("Update Room - Notice: Room ID $room_id update executed, but no rows were changed (data might be the same or room not found).");
            }
        }
        $stmt->close();
        return $result;
    }

    // Delete a room
    public function deleteRoom($room_id)
    {
        if (!filter_var($room_id, FILTER_VALIDATE_INT) || $room_id <= 0) {
            error_log("DeleteRoom - Invalid Room ID provided: $room_id");
            return false;
        }

        // Validate hostel access for the room being deleted
        $existing_room = $this->getRoomById($room_id);
        if (!$existing_room) {
            error_log("DeleteRoom - Room ID $room_id not found or access denied.");
            return false;
        }

        //Check if room has current occupants
        $check_query = "SELECT current_occupancy FROM rooms WHERE room_id = ?";
        $check_query = $this->addHostelFilter($check_query);

        $check_stmt = $this->conn->prepare($check_query);
        if (!$check_stmt) {
            error_log("DeleteRoom - Prepare Error (Check Occupancy): " . $this->conn->error);
            return false;
        }

        $check_stmt->bind_param("i", $room_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $room_data = $result->fetch_assoc();
        $check_stmt->close();

        if (!$room_data) {
            error_log("DeleteRoom - Room ID $room_id not found or access denied");
            return false;
        }

        if ($room_data['current_occupancy'] > 0) {
            error_log("DeleteRoom - Cannot delete Room ID $room_id: Room has {$room_data['current_occupancy']} current occupant(s)");
            return false;
        }

        // check for any active allocations
        $alloc_check_query = "SELECT COUNT(*) as active_count FROM allocations WHERE room_id = ? AND status = 'Active'";
        $alloc_stmt = $this->conn->prepare($alloc_check_query);
        if (!$alloc_stmt) {
            error_log("DeleteRoom - Prepare Error (Check Allocations): " . $this->conn->error);
            return false;
        }

        $alloc_stmt->bind_param("i", $room_id);
        $alloc_stmt->execute();
        $alloc_result = $alloc_stmt->get_result();
        $alloc_data = $alloc_result->fetch_assoc();
        $alloc_stmt->close();

        if ($alloc_data['active_count'] > 0) {
            error_log("DeleteRoom - Cannot delete Room ID $room_id: Room has {$alloc_data['active_count']} active allocation(s)");
            return false;
        }

        // Proceed with deletion if room is empty
        $query = "DELETE FROM rooms WHERE room_id = ?";
        $query = $this->addHostelFilter($query);

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("DeleteRoom - Prepare Error: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("i", $room_id);
        $execute_result = $stmt->execute();

        if (!$execute_result) {
            error_log("Delete Room - Execute Error for Room ID $room_id: {$stmt->error} (Code: {$stmt->errno})");
            $stmt->close();
            return false;
        }

        $affected_rows = $stmt->affected_rows;
        $stmt->close();

        if ($affected_rows > 0) {
            error_log("Delete Room - Success: Room ID $room_id deleted.");
            return true;
        } else {
            error_log("Delete Room - Failed: Room ID $room_id not found or no rows affected.");
            return false;
        }
    }

    // Check if student has already booked a room
    public function hasStudentBookedRoom($student_id)
    {
        $query = "
            SELECT COUNT(*) as count
            FROM allocations
            WHERE student_id = ? AND status = 'Active'
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }

    // Book a room

    public function bookRoom($student_id, $room_id, $start_date, $end_date = null)
    {
        // Validate inputs
        if (!filter_var($student_id, FILTER_VALIDATE_INT) || $student_id <= 0) {
            throw new Exception("Invalid Student ID.");
        }
        if (!filter_var($room_id, FILTER_VALIDATE_INT) || $room_id <= 0) {
            throw new Exception("Invalid Room ID.");
        }
        if (($start_timestamp = strtotime($start_date)) === false) {
            throw new Exception("Invalid start date format.");
        }
        if ($end_date !== null && ($end_timestamp = strtotime($end_date)) === false) {
            throw new Exception("Invalid end date format.");
        }
        if ($end_date !== null && $end_timestamp < $start_timestamp) {
            throw new Exception("End date cannot be before start date.");
        }

        // Format dates for DB
        $start_date_db = date('Y-m-d H:i:s', $start_timestamp);
        $end_date_db = ($end_date === null) ? null : date('Y-m-d H:i:s', $end_timestamp);

        $this->conn->begin_transaction();
        try {
            // Step 1: Check room availability and get details, lock the row
            $room_query = "
                SELECT r.room_id, r.room_number, r.building, r.room_type, r.capacity, 
                       r.current_occupancy, r.amount, r.hostel_id
                FROM rooms r
                WHERE r.room_id = ? AND r.status IN ('Vacant', 'Partially Occupied')
                FOR UPDATE";

            // Add hostel filtering for admin bookings (students can book across hostels)
            if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Admin') {
                $room_query = $this->addHostelFilter($room_query, 'r');
            }

            $stmt_room = $this->conn->prepare($room_query);
            if (!$stmt_room) {
                throw new Exception("Prepare failed (room_query): " . $this->conn->error);
            }

            $stmt_room->bind_param("i", $room_id);
            $stmt_room->execute();
            $result_room = $stmt_room->get_result();
            $room = $result_room->fetch_assoc();
            $stmt_room->close();

            if (!$room) {
                throw new Exception("Room is not available or does not exist.");
            }
            if ($room['current_occupancy'] >= $room['capacity']) {
                throw new Exception("Room is already full.");
            }

            // Step 2: Check if student already has an active allocation
            if ($this->hasStudentBookedRoom($student_id)) {
                throw new Exception("Student already has an active room allocation.");
            }

            // Step 3: Insert into allocations
            $alloc_status = 'Active';
            $query_alloc = "
                INSERT INTO allocations (student_id, room_id, start_date, end_date, status)
                VALUES (?, ?, ?, ?, ?)";
            $stmt_alloc = $this->conn->prepare($query_alloc);
            if (!$stmt_alloc) {
                throw new Exception("Prepare failed (alloc_query): " . $this->conn->error);
            }

            $stmt_alloc->bind_param("iisss", $student_id, $room_id, $start_date_db, $end_date_db, $alloc_status);
            if (!$stmt_alloc->execute()) {
                if ($this->conn->errno == 1062) {
                    throw new Exception("Failed to create allocation: Constraint violation.");
                }
                throw new Exception("Failed to create allocation: " . $stmt_alloc->error);
            }
            $allocation_id = $this->conn->insert_id;
            $stmt_alloc->close();

            // Step 4: Update rooms.current_occupancy
            $query_update_room = "
                UPDATE rooms
                SET current_occupancy = current_occupancy + 1,
                    status = CASE 
                        WHEN current_occupancy + 1 >= capacity THEN 'Fully Occupied'
                        WHEN current_occupancy + 1 > 0 THEN 'Partially Occupied'
                        ELSE status
                    END
                WHERE room_id = ?";
            $stmt_update_room = $this->conn->prepare($query_update_room);
            if (!$stmt_update_room) {
                throw new Exception("Prepare failed (update_room_query): " . $this->conn->error);
            }

            $stmt_update_room->bind_param("i", $room_id);
            if (!$stmt_update_room->execute()) {
                throw new Exception("Failed to update room occupancy: {$stmt_update_room->error}");
            }
            $stmt_update_room->close();

            // Step 5: Create billing record for the room booking
            $description = "Room fee for {$room['room_number']} - {$room['room_type']} in {$room['building']}";
            $date_due = date('Y-m-d H:i:s', strtotime('+30 days')); // Due in 30 days
            $billing_status = 'Unpaid';
            $amount = $room['amount'];
            $paid_amount = 0.00;

            $query_billing = "
                INSERT INTO billing (student_id, allocation_id, amount, description, date_due, status, paid_amount)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_billing = $this->conn->prepare($query_billing);
            if (!$stmt_billing) {
                throw new Exception("Prepare failed (billing_query): " . $this->conn->error);
            }

            $stmt_billing->bind_param("iidsssd", $student_id, $allocation_id, $amount, $description, $date_due, $billing_status, $paid_amount);
            if (!$stmt_billing->execute()) {
                throw new Exception("Failed to create billing record: {$stmt_billing->error}");
            }
            $billing_id = $this->conn->insert_id;
            $stmt_billing->close();

            // If all steps succeeded
            $this->conn->commit();
            error_log("Room booking successful for student $student_id, room $room_id, allocation $allocation_id, billing $billing_id");
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Booking failed for student_id $student_id, room_id $room_id: " . $e->getMessage());
            throw $e; // Re-throw the exception for the controller to handle
        }
    }

    // Get unique buildings for filter
    public function getUniqueBuildings()
    {
        $query = "SELECT DISTINCT building FROM rooms WHERE 1=1";
        $query = $this->addHostelFilter($query);
        $query .= " ORDER BY building";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $buildings = [];
        while ($row = $result->fetch_assoc()) {
            if (!empty($row['building'])) {
                $buildings[] = $row['building'];
            }
        }
        return $buildings;
    }

    // Get unique room types for filter
    public function getUniqueRoomTypes()
    {
        $query = "SELECT DISTINCT room_type FROM rooms WHERE 1=1";
        $query = $this->addHostelFilter($query);
        $query .= " ORDER BY room_type";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $roomTypes = [];
        while ($row = $result->fetch_assoc()) {
            if (!empty($row['room_type'])) {
                $roomTypes[] = $row['room_type'];
            }
        }
        return $roomTypes;
    }

    // Get unique floors for filter
    public function getUniqueFloors()
    {
        $query = "SELECT DISTINCT floor FROM rooms WHERE 1=1";
        $query = $this->addHostelFilter($query);
        $query .= " ORDER BY floor";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $floors = [];
        while ($row = $result->fetch_assoc()) {
            if ($row['floor'] !== null) {
                $floors[] = $row['floor'];
            }
        }
        return $floors;
    }

    // Get filter data for rooms
    public function getRoomFilterData()
    {
        return [
            'buildings' => $this->getUniqueBuildings(),
            'room_types' => $this->getUniqueRoomTypes(),
            'floors' => $this->getUniqueFloors()
        ];
    }
}
