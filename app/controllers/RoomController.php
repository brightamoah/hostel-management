<?php
require_once "./app/models/Room.php";

class RoomController
{
    private $roomModel;

    public function __construct()
    {
        $this->roomModel = new Rooms();
    }

    // Fetch available rooms for students
    public function getAvailableRooms()
    {
        header('Content-Type: application/json');
        $rooms = $this->roomModel->getAvailableRooms();
        echo json_encode(['data' => $rooms]);
        exit();
    }

    // Fetch all rooms for admin
    public function getAllRooms()
    {
        header('Content-Type: application/json');
        $rooms = $this->roomModel->getAllRooms();
        echo json_encode(['data' => $rooms]);
        exit();
    }

    // Add a new room (admin)
    public function addRoom()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_csrf_valid()) {
            $room_number = $_POST['room_number'] ?? '';
            $building = $_POST['building'] ?? '';
            $floor = $_POST['floor'] ?? 0;
            $room_type = $_POST['room_type'] ?? '';
            $capacity = $_POST['capacity'] ?? 0;
            $features = $_POST['features'] ?? '';
            $amount = $_POST['amount'] ?? 0.00; // New field for amount
            $status = $_POST['status'] ?? 'Vacant';

            $result = $this->roomModel->addRoom($room_number, $building, $floor, $room_type, $capacity, $features, $amount, $status);
            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Room added successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to add room']);
            }
            exit();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid request or CSRF token']);
            exit();
        }
    }

    // Update a room (admin)
    public function updateRoom()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_csrf_valid()) {
            $room_id = $_POST['room_id'] ?? 0;
            $room_number = $_POST['room_number'] ?? '';
            $building = $_POST['building'] ?? '';
            $floor = $_POST['floor'] ?? 0;
            $room_type = $_POST['room_type'] ?? '';
            $capacity = $_POST['capacity'] ?? 0;
            $features = $_POST['features'] ?? '';
            $amount = $_POST['amount'] ?? 0.00; // New field for amount
            $status = $_POST['status'] ?? 'Vacant';

            $result = $this->roomModel->updateRoom($room_id, $room_number, $building, $floor, $room_type, $capacity, $features, $amount, $status);
            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Room updated successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update room']);
            }
            exit();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid request or CSRF token']);
            exit();
        }
    }

    // Delete a room (admin)
    public function deleteRoom()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_csrf_valid()) {
            $room_id = $_POST['room_id'] ?? 0;
            $result = $this->roomModel->deleteRoom($room_id);
            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Room deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to delete room']);
            }
            exit();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid request or CSRF token']);
            exit();
        }
    }

    // Book a room (student)
    // Book a room (student)
    public function bookRoom($room_id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_csrf_valid()) {
            $student_id = $_SESSION['user']['user_id'] ?? null;
            $start_date = $_POST['start_date'] ?? date('Y-m-d'); // Default to today
            $end_date = $_POST['end_date'] ?? null; // Allow null for open-ended bookings

            if (!$student_id) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'User not authenticated']);
                exit();
            }

            try {
                $result = $this->roomModel->bookRoom($student_id, $room_id, $start_date, $end_date);
                header('Content-Type: application/json');
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Room booked successfully']);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to book room']);
                }
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid request or CSRF token']);
            exit();
        }
    }
}
