<?php

session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student's Dashboard</title>
</head>

<body>
    <div>
        <h1>Student's Dashboard</h1>
        <p>Welcome to the student's dashboard!</p>
        <a href="/logout">Logout</a>

        <?php
        if (isset($_SESSION['user'])) {
            echo '<div class="user-details">';
            echo '<p>Welcome ' . htmlspecialchars($_SESSION['user']['name']) . '!</p>';
            echo '<p>Email: ' . htmlspecialchars($_SESSION['user']['email']) . '</p>';
            echo '<p>Student ID: ' . htmlspecialchars($_SESSION['user']['user_id']) . '</p>';
            echo '</div>';

            echo "<h2>Welcome " . htmlspecialchars($_SESSION['user']['name']) . "</h2>";
            echo '<h3>All User Data:</h3>';
            echo '<pre>';
            print_r($_SESSION['user']);
            echo '</pre>';
        } else {
            echo '<p>Please log in to view your dashboard.</p>';
        }
        ?>
    </div>
</body>

</html>