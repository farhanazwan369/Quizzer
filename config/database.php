<?php
$servername = "localhost"; // Change if your MySQL server is different
$username = "root"; // Change to your MySQL username
$password = ""; // Change to your MySQL password
$dbname = "quiz"; // Name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
