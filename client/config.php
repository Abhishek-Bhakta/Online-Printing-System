<?php
$host = '127.0.0.1';  // Change if using a remote server
$dbname = 'mycompany';  // Your database name
$username = 'root';  // Your MySQL username
$password = ''; // Database password


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable exception mode
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
