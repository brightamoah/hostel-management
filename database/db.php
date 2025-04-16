<?php
class Database
{
    private $db_host = 'localhost';
    private $db_user = 'root';
    private $db_name = 'hostel_management';
    private $db_password = '';
    private $connection;

    public function connect()
    {
        $this->connection = null;

        try {
            $this->connection = new mysqli($this->db_host, $this->db_user, $this->db_password, $this->db_name);

            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }

            $this->connection->set_charset("utf8mb4");
            // echo "<pre>Connected successfully to the database.</pre>";
        } catch (Exception $e) {
            error_log($e->getMessage());
            die("Database connection failed. Please try again later.");
        }
        return $this->connection;
    }

    public function close()
    {
        if ($this->connection) {
            $this->connection->close();
            $this->connection = null;
        }
    }
}

$db = new Database();
$db->connect();
$db->close();