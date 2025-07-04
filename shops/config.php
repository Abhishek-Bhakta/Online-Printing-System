<?php
$host = '127.0.0.1';  // Change if using a remote server
$dbname = 'mycompany';  // Your database name
$username = 'root';  // Your MySQL username
$password = '';  // Your MySQL password (default is empty for XAMPP)

try {
    // Establish a database connection using PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set PDO to throw exceptions for errors
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
