<?php
include "./pages/auth/signup.php";

$path = __DIR__ . DIRECTORY_SEPARATOR;

$name = $_POST["username"];
$password = $_POST["password"];
$email = $_POST["email"];
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);


echo "<br> $name";
echo "<br> $password";
echo "<br> $hashedPassword";


echo "<br> $path";
