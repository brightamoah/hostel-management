<?php
require_once  './app/models/User.php';
require_once './database/db.php';

class Logout
{
    private $user;

    public function __construct()
    {
        $db = new Database();
        $this->user = new User($db->connect());
    }

    public function logout()
    {
        session_start();
        session_unset(); // Unset all session variables

        if (isset($_COOKIE['remember_me'])) {
            $this->user->deleteRememberToken($_COOKIE['remember_me']);
            setcookie('remember_me', '', time() - 3600, '/'); // Clear cookie
        }

        session_destroy();
        header("Location: /login"); // Redirect to login page
        exit();
    }
}

try {
    $logout = new Logout();
    $logout->logout();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage(); // Handle any exceptions
}
