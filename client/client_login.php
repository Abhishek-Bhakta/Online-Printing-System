<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit();
    }

    // Check if the user exists in the database
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    var_dump($user); // To check the data returned from the query


    if ($user) {
        // Check if password matches the hashed password stored in the database
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['client_id'] = $user['client_id'];  // Make sure you're using correct column name 'client_id'
            $_SESSION['client_name'] = $user['name'];     // Set the client name from the database

            // Redirect to the client dashboard
            header("Location: index.php");
            exit();
        } else {
            echo "Invalid email or password.";
        }
    } else {
        echo "Invalid email or password.";
    }
}
?>

<h2>Client Login</h2>
<form action="client_login.php" method="POST">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form>
