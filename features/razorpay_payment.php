<?php
require('vendor/autoload.php'); // Razorpay SDK include karein

use Razorpay\Api\Api;

// Razorpay API Keys
$keyId = "rzp_test_XeReFqwLC7foia";
$keySecret = "RODDAw3hpmcJ7tuF1kDVzzzx";

// Razorpay API Initialization
$api = new Api($keyId, $keySecret);

// Order Details
$orderData = [
    'receipt'         => 'order_rcptid_11',
    'amount'          => 10000, // Amount in paisa (₹100 = 10000)
    'currency'        => 'INR',
    'payment_capture' => 1 // Auto capture payment
];

$order = $api->order->create($orderData);
$orderId = $order['id']; // Order ID

// Return response
echo json_encode(['order_id' => $orderId, 'key' => $keyId]);
?>
