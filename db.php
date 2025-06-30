<?php
$host = "localhost";
$db_user = "root"; // change if needed
$db_pass = "";     // change if needed
$db_name = "blog";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
