<?php
require_once  "./database/db.php";

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
        return $result->fetch_assoc();
    }

    // Add a new room
    public function addRoom($room_number, $building, $floor, $room_type, $capacity, $features, $amount, $status = 'Vacant')
    {
        $query = "
        INSERT INTO rooms (room_number, building, floor, room_type, capacity, features, amount, status, current_occupancy)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssissids", $room_number, $building, $floor, $room_type, $capacity, $features, $amount, $status);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Update a room
    public function updateRoom($room_id, $room_number, $building, $floor, $room_type, $capacity, $features, $amount, $status)
    {
        $query = "
        UPDATE rooms
        SET room_number = ?, building = ?, floor = ?, room_type = ?, capacity = ?, features = ?, amount = ?, status = ?
        WHERE room_id = ?
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssissidsi", $room_number, $building, $floor, $room_type, $capacity, $features, $amount, $status, $room_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Delete a room
    /**
     * Summary of deleteRoom
     * @param number $room_id
     * @return bool
     */
    public function deleteRoom($room_id)
    {
        $query = "DELETE FROM rooms WHERE room_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            return true; // Deletion successful
        } else {
            return false; // No rows affected, room_id may not exist
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
    public function bookRoom($student_id, $room_id, $start_date, $end_date)
    {
        // Start a transaction to ensure data consistency
        $this->conn->begin_transaction();
        try {
            // Step 1: Verify room availability
            $query = "
            SELECT capacity, current_occupancy, amount 
            FROM rooms 
            WHERE room_id = ? AND status IN ('Vacant', 'Partially Occupied')";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $room_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $room = $result->fetch_assoc();
            $stmt->close();

            if (!$room || $room['current_occupancy'] >= $room['capacity']) {
                throw new Exception("Room is not available for booking.");
            }

            // Step 2: Check if student already has an active allocation
            $query = "
            SELECT COUNT(*) as active_allocations 
            FROM allocations 
            WHERE student_id = ? AND status = 'Active'";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();

            if ($data['active_allocations'] > 0) {
                throw new Exception("Student already has an active room allocation.");
            }

            // Step 3: Insert allocation
            $query = "
            INSERT INTO allocations (student_id, room_id, start_date, end_date, status)
            VALUES (?, ?, ?, ?, 'Active')";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("iiss", $student_id, $room_id, $start_date, $end_date);
            $stmt->execute();
            $allocation_id = $this->conn->insert_id;
            $stmt->close();

            // Step 4: Update room occupancy
            $query = "
            UPDATE rooms 
            SET current_occupancy = current_occupancy + 1 
            WHERE room_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $room_id);
            $stmt->execute();
            $stmt->close();

            // Step 5: Create a billing record
            $billing_query = "
            INSERT INTO billing (student_id, amount, description, date_due, status)
            VALUES (?, ?, ?, ?, 'Unpaid')";
            $stmt = $this->conn->prepare($billing_query);
            $description = "Room " . $this->getRoomById($room_id)['room_number'] . " allocation fee";
            $date_due = date('Y-m-d H:i:s', strtotime("$start_date +30 days"));
            $stmt->bind_param("idss", $student_id, $room['amount'], $description, $date_due);
            $stmt->execute();
            $stmt->close();

            // Commit the transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Roll back on error
            $this->conn->rollback();
            throw new Exception("Booking failed: " . $e->getMessage());
        }
    }

    // Close connection
    public function __destruct()
    {
        $this->conn->close();
    }
}
