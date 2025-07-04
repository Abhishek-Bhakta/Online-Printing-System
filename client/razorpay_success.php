<?php
require('vendor/autoload.php'); // Razorpay SDK
include 'config.php'; // Database connection

use Razorpay\Api\Api;

// Razorpay API Keys

$keyId = "rzp_test_XeReFqwLC7foia";
$keySecret = "RODDAw3hpmcJ7tuF1kDVzzzx";

// Initialize Razorpay API
$api = new Api($keyId, $keySecret);

// Get Razorpay response
$razorpay_payment_id = $_POST['razorpay_payment_id'];
$razorpay_order_id = $_POST['razorpay_order_id'];
$razorpay_signature = $_POST['razorpay_signature'];
$client_id = $_SESSION['client_id']; // Assuming client is logged in
$shopkeeper_id = $_POST['shopkeeper_id']; // Get shopkeeper_id from frontend

// Verify Payment Signature (Security Check)
$generated_signature = hash_hmac('sha256', $razorpay_order_id . "|" . $razorpay_payment_id, $keySecret);

if ($generated_signature === $razorpay_signature) {
    
    // Fetch order details from Razorpay
    $payment = $api->payment->fetch($razorpay_payment_id);
    
    // Extract necessary fields
    $amount = $payment->amount / 100; // Convert paisa to INR
    $currency = $payment->currency;
    $status = $payment->status; // "captured" means successful
    $payment_mode = $payment->method;

    // Convert Razorpay status to our status format
    $payment_status = ($status === 'captured') ? 'Success' : 'Failed';

    // Insert into transactions table
    $stmt = $pdo->prepare("INSERT INTO transactions 
        (client_id, shopkeeper_id, razorpay_payment_id, amount, currency, payment_status, payment_mode) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$client_id, $shopkeeper_id, $razorpay_payment_id, $amount, $currency, $payment_status, $payment_mode]);

    if ($stmt) {
        echo json_encode(["status" => "success", "message" => "Transaction stored successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Payment verification failed."]);
}
?>
