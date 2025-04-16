<?php
require_once "./app/models/User.php";
require_once "./app/config/Database.php";
require_once "./app/controllers/Login.php";

$db = new Database();
$user = new User($db->connect());

function redirectUser($role)
{
    if ($role === 'Admin') {
        header('Location: /admin/dashboard');
    } else {
        header('Location: /student/dashboard');
    }
    exit;
}


if (isset($_SESSION['user']) && isset(($_COOKIE['remember_me']))) {
    $remembered_user = $user->validateRememberToken($_COOKIE['remember_me']);

    if ($user && is_array($user) && !isset($user['error'])) {
        $_SESSION['user'] = $user;
        $_SESSION['message'] = 'Welcome back! You were logged in automatically.';
        $_SESSION['message_type'] = 'success';
        redirectUser($user['role']);
    }
}