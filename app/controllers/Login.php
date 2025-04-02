<?php
include "./pages/auth/login.php";

$path = __DIR__ . DIRECTORY_SEPARATOR;


$email = $_POST["email"];
$password = $_POST["password"];
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$input = $_SERVER["REQUEST_METHOD"];

echo "<pre>
$input
</pre>";


// echo "<br> $email";
// echo "<br> $password";
// echo "<br> $hashedPassword";


// echo "<br> $path";
