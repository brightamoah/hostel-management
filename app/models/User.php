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

    public function signup($name, $email, $password, $confirm_password, $gender, $date_of_birth, $phone_number, $address, $emergency_contact_name, $emergency_contact_number, $health_condition)
    {
        $this->connection->begin_transaction();
        try {
            $query = "INSERT INTO {$this->table} (name, email, password, role, is_email_verified) VALUES (?, ?, ?, 'Student', 0)";
            $stmt = $this->connection->prepare($query);
            if (!$stmt) throw new Exception("Failed to prepare users statement: {$this->connection->error}");

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            if ($password !== $confirm_password) throw new Exception("Passwords do not match");

            $stmt->bind_param("sss", $name, $email, $hashed_password);
            if (!$stmt->execute()) throw new Exception("Failed to insert into users: {$stmt->error}");

            $user_id = $this->connection->insert_id;
            $stmt->close();

            $name_parts = explode(' ', trim($name));
            $first_name = array_shift($name_parts);
            $last_name = implode(' ', $name_parts);

            $student_query = "INSERT INTO students (user_id, first_name, last_name, gender, date_of_birth, phone_number, address, emergency_contact_name, emergency_contact_number, health_condition, enrollment_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE())";
            $stmt = $this->connection->prepare($student_query);
            if (!$stmt) throw new Exception("Failed to prepare students statement: {$this->connection->error}");

            $stmt->bind_param("isssssssss", $user_id, $first_name, $last_name, $gender, $date_of_birth, $phone_number, $address, $emergency_contact_name, $emergency_contact_number, $health_condition);
            if (!$stmt->execute()) throw new Exception("Failed to insert into students: {$stmt->error}");

            $stmt->close();
            $this->connection->commit();
            return $user_id;
        } catch (Exception $e) {
            $this->connection->rollback();
            error_log("Signup error: " . $e->getMessage());
            return false;
        }
    }

    public function login($email, $password)
    {
        if (!$this->emailExists($email)) {
            return ['error' => 'Email does not exist'];
        }

        if (!$this->isEmailVerified($email)) {
            return ['error' => 'Email not verified'];
        }

        $query = "SELECT user_id, name, email, password, role, is_email_verified FROM {$this->table} WHERE email = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) return false;

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
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));

        $query = "INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            error_log("Failed to prepare token insert: " . $this->connection->error);
            return false;
        }
        $stmt->bind_param("iss", $user_id, $token, $expires_at);
        $success = $stmt->execute();
        if (!$success) {
            error_log("Failed to insert remember token for user_id: $user_id - " . $stmt->error);
        }
        $stmt->close();
        return $success ? $token : false;
    }

    public function validateRememberToken($token)
    {
        $query = "SELECT user_id FROM remember_tokens WHERE token = ? AND expires_at > NOW()";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) return false;

        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row) {
            $query = "SELECT user_id, name, email, role, is_email_verified FROM {$this->table} WHERE user_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("i", $row['user_id']);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($user) {
                if ($user['role'] === 'Student') {
                    $student_query = "SELECT * FROM students WHERE user_id = ?";
                    $stmt = $this->connection->prepare($student_query);
                    $stmt->bind_param("i", $user['user_id']);
                    $stmt->execute();
                    $student = $stmt->get_result()->fetch_assoc();
                    $stmt->close();

                    return array_merge($user, $student);
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

    public function generateVerificationCode($user_id)
    {
        $delete_query = "DELETE FROM verification_codes WHERE user_id = ?";
        $stmt = $this->connection->prepare($delete_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        $query = "INSERT INTO verification_codes (user_id, code, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            error_log("Failed to prepare verification code insert: " . $this->connection->error);
            return false;
        }
        $stmt->bind_param("iss", $user_id, $code, $expires_at);
        $success = $stmt->execute();
        $stmt->close();
        return $success ? $code : false;
    }

    public function verifyEmail($email, $code)
    {
        $query = "SELECT v.user_id FROM verification_codes v JOIN {$this->table} u ON v.user_id = u.user_id WHERE u.email = ? AND v.code = ? AND v.expires_at > NOW()";
        $stmt = $this->connection->prepare($query);
        if (!$stmt) return false;

        $stmt->bind_param("ss", $email, $code);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row) {
            $user_id = $row['user_id'];
            $update_query = "UPDATE {$this->table} SET is_email_verified = 1 WHERE user_id = ?";
            $stmt = $this->connection->prepare($update_query);
            $stmt->bind_param("i", $user_id);
            $success = $stmt->execute();
            $stmt->close();

            $delete_query = "DELETE FROM verification_codes WHERE user_id = ? AND code = ?";
            $stmt = $this->connection->prepare($delete_query);
            $stmt->bind_param("is", $user_id, $code);
            $stmt->execute();
            $stmt->close();

            return $success;
        }
        return false;
    }

    public function isEmailVerified($email)
    {
        $query = "SELECT is_email_verified FROM {$this->table} WHERE email = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row && $row['is_email_verified'] == 1;
    }


    public function generatePasswordResetToken($email)
    {
        $query = "SELECT user_id FROM {$this->table} WHERE email = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user) {
            return false;
        }

        $user_id = $user['user_id'];
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Delete any existing tokens for this user
        $delete_query = "DELETE FROM password_reset_tokens WHERE user_id = ?";
        $stmt = $this->connection->prepare($delete_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Insert new token
        $query = "INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("iss", $user_id, $token, $expires_at);
        $success = $stmt->execute();
        $stmt->close();

        return $success ? $token : false;
    }

    public function validatePasswordResetToken($token)
    {
        $query = "SELECT user_id FROM password_reset_tokens WHERE token = ? AND expires_at > NOW()";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row ? $row['user_id'] : false;
    }

    public function resetPassword($token, $new_password)
    {
        $user_id = $this->validatePasswordResetToken($token);
        if (!$user_id) {
            return false;
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE {$this->table} SET password = ? WHERE user_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("si", $hashed_password, $user_id);
        $success = $stmt->execute();
        $stmt->close();

        if ($success) {
            $delete_query = "DELETE FROM password_reset_tokens WHERE token = ?";
            $stmt = $this->connection->prepare($delete_query);
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $stmt->close();
        }

        return $success;
    }

    public function __destruct()
    {
        $this->connection->close();
    }
}
