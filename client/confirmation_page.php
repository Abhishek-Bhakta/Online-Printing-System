<?php
session_start();

// Ensure session is valid
if (!isset($_SESSION['client_id']) || !isset($_SESSION['order_details'])) {
    header("Location: client_login.php"); // Redirect to login page if session is invalid
    exit();
}

// Get order details from session
$order = $_SESSION['order_details'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation - Printify</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Your embedded CSS */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #eaf0e1;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        .confirmation-card {
            background: #fff;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            animation: slideUp 1s ease-out forwards;
            opacity: 0;
        }
        .confirmation-card h1 {
            font-size: 2.5rem;
            color: #1e8e41;
            margin-bottom: 20px;
        }
        .confirmation-card .subheader {
            font-size: 1.2rem;
            color: #34495e;
            margin-bottom: 30px;
        }
        .order-details {
            margin-top: 20px;
            font-size: 1.1rem;
            color: #555;
        }
        .order-details strong {
            color: #1e8e41;
        }
        .footer {
            margin-top: 40px;
            font-size: 1rem;
            color: #95a5a6;
        }
        @keyframes slideUp {
            0% { opacity: 0; transform: translateY(50px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="confirmation-card">
        <h1>Order Confirmed!</h1>
        <p class="subheader">Your order has been successfully confirmed.</p>
        <div class="order-details">
            <p><strong>Order ID:</strong> <?= $order['order_id'] ?></p>
            <p><strong>Transaction ID:</strong> <?= $order['transaction_id'] ?></p>
            <p><strong>Verification Code:</strong> <?= $order['verification_code'] ?></p>
            <p><strong>Total Price Paid:</strong> ₹<?= number_format($order['total_price'], 2) ?></p>
        </div>
        <div class="footer">
            <p>Thanks for choosing Printify. Contact us at <a href="mailto:support@printify.com">support@printify.com</a>.</p>
        </div>
    </div>
</div>
</body>
</html>
