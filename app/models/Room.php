<?php
require_once "./database/db.php";

class Rooms
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }

    // Fetch available rooms (for students)
    public function getAvailableRooms()
    {
        $query = "
            SELECT room_id, room_number, building, floor, room_type, capacity, current_occupancy, features, amount, status
            FROM rooms
            WHERE status IN ('Vacant', 'Partially Occupied')
            AND current_occupancy < capacity
            ORDER BY building, room_number
        ";
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
            SELECT room_id, room_number, building, floor, room_type, capacity, current_occupancy, features, amount, status
            FROM rooms
            ORDER BY building, room_number
        ";
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
            SELECT room_id, room_number, building, floor, room_type, capacity, current_occupancy, features, amount, status
            FROM rooms
            WHERE room_id = ?
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $room = $result->fetch_assoc();
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

        $query = "
            INSERT INTO rooms (room_number, building, floor, room_type, capacity, current_occupancy, features, amount, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Add Room - Prepare Error: " . $this->conn->error . " (Code: " . $this->conn->errno . ")");
            return false;
        }

        $stmt->bind_param(
            "ssisiisds",
            $room_number,
            $building,
            $floor,
            $room_type,
            $capacity,
            $current_occupancy,
            $features_str,
            $amount,
            $status
        );

        $result = $stmt->execute();
        if (!$result) {
            error_log("Add Room - Execute Error: {$stmt->error} (Code: {$stmt->errno})");
            if ($stmt->errno == 1062) {
                error_log("Add Room - Error: Duplicate entry for building '$building' and room_number '$room_number'.");
            }
        } else {
            $new_room_id = $this->conn->insert_id;
            error_log("Add Room - Success: Room ID $new_room_id added with features: '$features_str'");
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

        $query = "
            UPDATE rooms
            SET room_number = ?, building = ?, floor = ?, room_type = ?, capacity = ?,
                current_occupancy = ?, features = ?, amount = ?, status = ?
            WHERE room_id = ?
        ";
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
            error_log("DeleteRoom - Invalid Room ID provided: " . $room_id);
            return false;
        }

        $query = "DELETE FROM rooms WHERE room_id = ?";
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
    // Book a room
    public function bookRoom($student_id, $room_id, $start_date, $end_date = null) // Allow null end_date
    {
        // Validate inputs (using previous refined version's validation)
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

        // Format dates for DB (Assuming DATE type for start/end/due based on schema)
        $start_date_db = date('Y-m-d', $start_timestamp);
        $end_date_db = ($end_date === null) ? null : date('Y-m-d', $end_timestamp);

        $this->conn->begin_transaction();
        try {
            // Step 1: Check room availability and get details, lock the row
            $room_query = "
                SELECT room_id, room_number, capacity, current_occupancy, amount
                FROM rooms
                WHERE room_id = ? AND status IN ('Vacant', 'Partially Occupied')
                FOR UPDATE";
            $stmt_room = $this->conn->prepare($room_query);
            if (!$stmt_room) throw new Exception("Prepare failed (room_query): " . $this->conn->error);
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
            if (!$stmt_alloc) throw new Exception("Prepare failed (alloc_query): " . $this->conn->error);
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
                SET current_occupancy = current_occupancy + 1
                WHERE room_id = ?";
            $stmt_update_room = $this->conn->prepare($query_update_room);
            if (!$stmt_update_room) throw new Exception("Prepare failed (update_room_query): " . $this->conn->error);
            $stmt_update_room->bind_param("i", $room_id);
            if (!$stmt_update_room->execute()) {
                throw new Exception("Failed to update room occupancy: " . $stmt_update_room->error);
            }
            $stmt_update_room->close();

            // Step 5: Insert into billing
            $billing_query = "
                INSERT INTO billing (student_id, allocation_id, amount, description, date_due, status)
                VALUES (?, ?, ?, ?, ?, 'Unpaid')"; // 5 placeholders
            $stmt_billing = $this->conn->prepare($billing_query);
            if (!$stmt_billing) throw new Exception("Prepare failed (billing_query): " . $this->conn->error);

            // Use room_number fetched earlier
            $description = "Room " . $room['room_number'] . " allocation fee starting " . $start_date_db;
            // Calculate due date (e.g., 30 days from start date, format as DATE)
            $date_due_timestamp = strtotime("$start_date_db +30 days");
            $date_due_db = date('Y-m-d', $date_due_timestamp); // Format as DATE

            // ***** CORRECTED LINE *****
            $stmt_billing->bind_param(
                "iidss", // Correct type string: i, i, d, s, s
                $student_id,        // i
                $allocation_id,     // i
                $room['amount'],    // d
                $description,       // s
                $date_due_db        // s (DATE is bound as string)
            );
            // ***** END CORRECTION *****

            if (!$stmt_billing->execute()) {
                throw new Exception("Failed to create billing record: " . $stmt_billing->error);
            }
            $stmt_billing->close();

            // If all steps succeeded
            $this->conn->commit();
            error_log("Room booking successful for student $student_id, room $room_id, allocation $allocation_id.");
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            // Log the specific error message from the exception
            error_log("Booking failed for student_id $student_id, room_id $room_id: " . $e->getMessage());
            // Re-throw the exception for the controller to handle
            throw $e; // Keep original exception message for clarity
        }
    }

    // Close connection
    public function __destruct()
    {
        $this->conn->close();
    }
}
