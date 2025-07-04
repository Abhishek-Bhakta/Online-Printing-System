<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']); // Get password before hashing

    // Check if any field is empty
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        echo "Error: All fields are required!";
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM clients WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetchColumn() > 0) {
        echo "Error: Email already taken!";
        exit();
    }

    // Insert new client
    $sql = "INSERT INTO clients (name, email, password, phone) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute([$name, $email, $hashedPassword, $phone]);

         // Fetch the inserted user ID
        $client_id = $conn->lastInsertId();

        // Set session variables
        $_SESSION['client_id'] = $client_id;
        $_SESSION['client_name'] = $name;

        // Redirect to user dashboard
        header("Location: client/index.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>
