<?php
$servername = "localhost";
$username = "root";
$password = ""; // Change to your DB password if needed
$dbname = "shop_database"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

