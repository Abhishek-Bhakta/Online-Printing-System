<?php
include('config.php');
try {
    $stmt = $conn->query("SELECT DATABASE()");
    $db = $stmt->fetchColumn();
    echo "Connected successfully to database: " . $db;
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
session_start(); // Start the session

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email'])) {
        // Handle shopkeeper login
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Prepare and execute query using PDO
        $stmt = $conn->prepare("SELECT * FROM shopkeepers WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $shopkeeper = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($shopkeeper && password_verify($password, $shopkeeper['password'])) {
            // Set session variables
            $_SESSION['shopkeeper_id'] = $shopkeeper['shopkeeper_id'];
            $_SESSION['shopkeeper_name'] = $shopkeeper['shopkeeper_name'];
            $_SESSION['shop_name'] = $shopkeeper['shop_name'];

            // Redirect to shopkeeper dashboard
            header("Location: shops/index.php");
            exit();
        } else {
            echo "<p style='color: red;'>Invalid username or password!</p>";
        }
    } elseif (isset($_POST['user_email']) && isset($_POST['user_password'])) {
        $email = trim($_POST['user_email']);
        $password = trim($_POST['user_password']);

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<p style='color: red;'>Invalid email format.</p>";
            exit();
        }

        // Prepare and execute query using PDO for client login
        $stmt = $conn->prepare("SELECT * FROM clients WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify the hashed password stored in the database
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['client_id'] = $user['client_id'];  // Correct field name
                $_SESSION['client_name'] = $user['name'];      // Store client name

                // Redirect to client dashboard
                header("Location: client/upload.php");
                exit();
            } else {
                echo "<p style='color: red;'>Invalid email or password!</p>";
            }
        } else {
            echo "<p style='color: red;'>Invalid email or password!</p>";
        }
    } else {
        echo "<p style='color: red;'>Invalid login attempt!</p>";
    }
}
?>

