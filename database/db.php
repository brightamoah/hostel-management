<?php

$db_server = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "hostel_management";
$connection = "";


try {
   $connection = mysqli_connect($db_server, $db_user, $db_password, $db_name);
} catch (mysqli_sql_exception) {
    echo "Connection Failed: Could not connect to the database. <br>";
}

if($connection){
    echo "Connection Successful <br>";
}
