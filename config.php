<?php
$host = '127.0.0.1';  // Use '127.0.0.1' for local development (XAMPP)
$dbname = 'mycompany';  // Your database name
$username = 'root';  // Your MySQL username
$password = '';  // Default password for XAMPP (empty)

try {
    // Establish a secure database connection using PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Enable exceptions for errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // Fetch results as an associative array
        PDO::ATTR_EMULATE_PREPARES => false  // Disable emulated prepares for security
    ]);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
    exit(); // Ensure script stops execution after error
}
?>
