<?php

class User
{
    private $connection;
    protected $table = 'users';

    public function __construct($db)
    {
        $this->connection = $db;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function signup(
        $name,
        $email,
        $password,
        $confirm_password,
        $gender,
        $date_of_birth,
        $phone_number,
        $address,
        $emergency_contact_name,
        $emergency_contact_number,
        $health_condition
    ) {

        $this->connection->begin_transaction();


        try {
            $query = "INSERT INTO {$this->table} (name, email, password, role) VALUES (?, ?, ?, 'Student')";
            $stmt = $this->connection->prepare($query);

            if (!$stmt) {
                throw new Exception("Failed to prepare users statement: {$this->connection->error}");
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            if ($password !== $confirm_password) {
                throw new Exception("Passwords do not match");
            }

            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if (!$stmt->execute()) {
                throw new Exception("Failed to insert into users: {$stmt->error}");
            }

            $user_id = $this->connection->insert_id;
            $stmt->close();

            $name_parts = explode(' ', trim($name));
            $first_name = array_shift($name_parts);
            $last_name = implode(' ', $name_parts);

            $student_query = "INSERT INTO students (
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
                enrollment_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE())";

            $stmt = $this->connection->prepare($student_query);

            if (!$stmt) {
                throw new Exception("Failed to prepare students statement: {$this->connection->error}");
            }

            $stmt->bind_param(
                "isssssssss",
                $user_id,
                $first_name,
                $last_name,
                $gender,
                $date_of_birth,
                $phone_number,
                $address,
                $emergency_contact_name,
                $emergency_contact_number,
                $health_condition
            );

            if (!$stmt->execute()) {
                throw new Exception("Failed to insert into students: {$stmt->error}");
            }

            $stmt->close();
            $this->connection->commit();
            return true;
        } catch (Exception $e) {
            $this->connection->rollback();
            error_log("Signup error: " . $e->getMessage());
            return false;
        }
    }

    public function login($email, $password)
    {
        $query = "SELECT user_id, name, email, password, role FROM {$this->table} WHERE email = ? LIMIT 1";

        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $this->updateLastLogin($user['user_id']);
            unset($user['password']);
            return $user;
        }
        $stmt->close();
        return false;
    }

    public function generateRememberToken($user_id)
    {
        $token = bin2hex(random_bytes(32)); // Secure random token
        $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));

        $query = "INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($query);

        if (!$stmt) {
            error_log("Failed to prepare token insert: " . $this->connection->error);
            return false;
        }
        $stmt->bind_param("iss", $user_id, $token, $expires_at);
        $success = $stmt->execute();
        $stmt->close();

        return $success ? $token : false;
    }

    public function validateRememberToken($token)
    {
        $query = "SELECT user_id FROM remember_tokens WHERE token = ? AND expires_at > NOW()";
        $stmt = $this->connection->prepare($query);

        if (!$stmt) {
            return false;
        }


        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row) {
            // Fetch user data
            $query = "SELECT user_id, name, email, role FROM {$this->table} WHERE user_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("i", $row['user_id']);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($user) {
                // Fetch student data if applicable
                if ($user['role'] === 'Student') {
                    $student_query = "SELECT * FROM students WHERE user_id = ?";
                    $stmt = $this->connection->prepare($student_query);
                    $stmt->bind_param("i", $user['user_id']);
                    $stmt->execute();
                    $student = $stmt->get_result()->fetch_assoc();
                    $stmt->close();

                    return [
                        'user_id' => $user['user_id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'gender' => $student['gender'],
                        'date_of_birth' => $student['date_of_birth'],
                        'phone_number' => $student['phone_number'],
                        'address' => $student['address'],
                        'emergency_contact_name' => $student['emergency_contact_name'],
                        'emergency_contact_number' => $student['emergency_contact_number'],
                        'health_condition' => $student['health_condition'],
                        'enrollment_date' => $student['enrollment_date']
                    ];
                }
                return $user;
            }
        }
        return false;
    }

    public function deleteRememberToken($token)
    {
        $query = "DELETE FROM remember_tokens WHERE token = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->close();
    }



    private function updateLastLogin($user_id)
    {
        $query = "UPDATE {$this->table} SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }


    public function emailExists($email)
    {
        $query = "SELECT user_id FROM {$this->table} WHERE email = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }
}
