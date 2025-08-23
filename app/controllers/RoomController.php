<?php
require_once __DIR__ . "/../../app/models/Room.php";

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
            $features = $_POST['features'] ?? ''; // Ensure features is captured
            $amount = $_POST['amount'] ?? 0.00;
            $status = $_POST['status'] ?? 'Vacant';

            // Debug: Log received data
            error_log("Add Room Data: " . json_encode([
                'room_number' => $room_number,
                'building' => $building,
                'floor' => $floor,
                'room_type' => $room_type,
                'capacity' => $capacity,
                'features' => $features,
                'amount' => $amount,
                'status' => $status
            ]));

            try {
                $result = $this->roomModel->addRoom($room_number, $building, $floor, $room_type, $capacity, $features, $amount, $status);
                header('Content-Type: application/json');
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Room added successfully']);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to add room']);
                }
            } catch (Exception $e) {
                error_log("Add Room Error: " . $e->getMessage());
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Failed to add room: ' . $e->getMessage()]);
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
            $current_occupancy = $_POST['current_occupancy'] ?? null; // Check if provided in form
            $features = $_POST['features'] ?? '';
            $amount = $_POST['amount'] ?? 0.00;
            $status = $_POST['status'] ?? 'Vacant';

            // If current_occupancy is not provided, fetch it from the database
            if ($current_occupancy === null) {
                $room = $this->roomModel->getRoomById($room_id);
                $current_occupancy = $room['current_occupancy'] ?? 0;
            }

            // Debug: Log received data
            error_log("Update Room Data: " . json_encode([
                'room_id' => $room_id,
                'room_number' => $room_number,
                'building' => $building,
                'floor' => $floor,
                'room_type' => $room_type,
                'capacity' => $capacity,
                'current_occupancy' => $current_occupancy,
                'features' => $features,
                'amount' => $amount,
                'status' => $status
            ]));

            try {
                $result = $this->roomModel->updateRoom($room_id, $room_number, $building, $floor, $room_type, $capacity, $current_occupancy, $features, $amount, $status);
                header('Content-Type: application/json');
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Room updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to update room']);
                }
            } catch (Exception $e) {
                error_log("Update Room Error: " . $e->getMessage());
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Failed to update room: ' . $e->getMessage()]);
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

            // First check if room has occupants before attempting deletion
            $room = $this->roomModel->getRoomById($room_id);
            if ($room && $room['current_occupancy'] > 0) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Cannot delete room with current occupants. Please relocate students first.'
                ]);
                exit();
            }

            $result = $this->roomModel->deleteRoom($room_id);
            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Room deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to delete room. Room may have occupants or active allocations.']);
            }
            exit();
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid request or CSRF token']);
            exit();
        }
    }

    // Book a room (student)
    public function bookRoom($room_id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_csrf_valid()) {
            $student_id = $_SESSION['user']['student_id'] ?? null;
            $start_date = $_POST['start_date'] ?? date('Y-m-d');
            $end_date = $_POST['end_date'] ?? null;

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

    public function getRoomFilters()
    {
        header('Content-Type: application/json');
        $filterData = $this->roomModel->getRoomFilterData();
        echo json_encode(['success' => true, 'data' => $filterData]);
        exit();
    }
}
