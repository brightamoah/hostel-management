<?php
require_once __DIR__ . "/../utils/load_env.php";

class Database
{
    private $db_host;
    private $db_user;
    private $db_name;
    private $db_password;
    private $connection;

    public function __construct()
    {
        loadEnvFile();
        $this->db_host = $_ENV['DATABASE_HOST'];
        $this->db_name = $_ENV['DATABASE_NAME'];
        $this->db_user = $_ENV['DATABASE_USER'];
        $this->db_password = $_ENV['DATABASE_PASSWORD'];
    }

    /**
     * Establishes a connection to the MySQL database using the provided credentials.
     *
     * Attempts to create a new mysqli connection with the configured host, username, password, and database name.
     * Sets the character set to utf8mb4 for proper encoding support.
     * If the connection fails, logs the error and terminates the script with a user-friendly message.
     *
     * @return mysqli|null Returns the mysqli connection object on success, or null on failure.
     */
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

        $conn = $this->connection;

        return $conn;
    }


    /**
     * Closes the current database connection if it exists.
     *
     * This method checks if a database connection is active. If so, it closes the connection
     * and sets the connection property to null to free up resources.
     *
     * @return void
     */
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


/**
 * Establishes and returns a connection to the MySQL database using MySQLi.
 *
 * @return mysqli|null Returns a MySQLi connection object on success, or null on failure.
 */
function getDb()
{
    $db = new Database();
    return $db->connect();
}
