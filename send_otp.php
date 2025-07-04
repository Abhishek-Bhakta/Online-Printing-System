<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer
require 'client/src/PHPMailer.php';
require 'client/src/Exception.php';
require 'client/src/SMTP.php';

session_start();
include 'config.php'; // Database connection

// Ensure email is provided
if (!isset($_POST['email']) || empty($_POST['email'])) {
    die("Email field is required.");
}

$email = $_POST['email'];

// Check if email exists in the database
$stmt = $pdo->prepare("SELECT email FROM clients WHERE email = ?");
$stmt->execute([$email]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    die("Email not registered or invalid.");
}

// Generate a 4-digit OTP
$otp = rand(1000, 9999);
$_SESSION['otp'] = $otp; // Store OTP in session
$_SESSION['otp_email'] = $email; // Store email associated with OTP

// Send OTP via email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'printifyglobal@gmail.com';  
    $mail->Password = 'dyeo qdfe pkbr lgth';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    $mail->setFrom('printifyglobal@gmail.com', 'Printify');
    $mail->addAddress($email);

    $mail->isHTML(false);
    $mail->Subject = "Password Reset OTP";
    $mail->Body = "Your OTP for password reset is: $otp";

    $mail->send();
    echo "OTP sent successfully to $email";
} catch (Exception $e) {
    echo "Email sending failed: " . $mail->ErrorInfo;
}
?>
