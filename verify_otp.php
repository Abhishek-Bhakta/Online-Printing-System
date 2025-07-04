<?php
session_start();
include 'config.php';

if (!isset($_POST['otp'], $_POST['new_password'])) {
    die("OTP or new password not provided.");
}

$entered_otp = $_POST['otp'];
$new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // Secure hashing

if (!isset($_SESSION['otp'], $_SESSION['otp_email'])) {
    die("Session expired. Try again.");
}

// Check if OTP matches
if ($entered_otp == $_SESSION['otp']) {
    // Update password in database
    $stmt = $pdo->prepare("UPDATE clients SET password = ? WHERE email = ?");
    $stmt->execute([$new_password, $_SESSION['otp_email']]);

    // Clear OTP session
    unset($_SESSION['otp'], $_SESSION['otp_email']);

    echo "Password reset successfully. You can now log in with your new password.";
} else {
    echo "Invalid OTP. Try again.";
}
?>
