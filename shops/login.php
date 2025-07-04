<?php
session_start();
include 'config.php'; // Ensure database connection is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);  // Change email to username
    $password = $_POST['password'];

    // Query to fetch shopkeeper details using username
    $stmt = $conn->prepare("SELECT * FROM shopkeepers WHERE username = :username");  // Using shopkeeper_name instead of email
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $shopkeeper = $stmt->fetch(PDO::FETCH_ASSOC);

    // If shopkeeper exists and password is correct
    if ($shopkeeper && password_verify($password, $shopkeeper['password'])) {
        $_SESSION['shopkeeper_id'] = $shopkeeper['shopkeeper_id'];
        $_SESSION['shopkeeper_name'] = $shopkeeper['shopkeeper_name'];

        header("Location: dashboard.php"); // Redirect to dashboard
        exit();
    } else {
        echo "<p style='color: red;'>Invalid username or password!</p>";
    }
}
?>

<!-- HTML Form for Shopkeeper Login -->
<h2>Shopkeeper Login</h2>
<form method="POST">
    <label for="username">Username:</label>
    <input type="text" name="username" required><br>

    <label for="password">Password:</label>
    <input type="password" name="password" required><br>

    <button type="submit">Login</button>
</form>
