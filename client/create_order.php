<?php
require('vendor/autoload.php'); // Composer autoload file

// Set Razorpay API key and secret securely (avoid hardcoding keys in production code)
$razorpayApiKey = 'rzp_test_XeReFqwLC7foia'; // Replace with your Razorpay API Key
$razorpayApiSecret = 'RODDAw3hpmcJ7tuF1kDVzzzx'; // Replace with your Razorpay API Secret

// Initialize Razorpay API
try {
    $api = new \Razorpay\Api\Api($razorpayApiKey, $razorpayApiSecret);
    
    // Step 1: Create an Order
    $orderData = [
        'receipt'         => 1234,  // Unique receipt number (can be random or order ID)
        'amount'          => 1000,  // Amount in paise (1000 = ₹10)
        'currency'        => 'INR', // Currency code (INR for Indian Rupees)
        'payment_capture' => 1      // Auto-capture payment (1 for auto capture, 0 for manual)
    ];
    
    // Create order via Razorpay API
    $order = $api->order->create($orderData);
    
    // Step 2: Send order ID to frontend
    echo json_encode(['order_id' => $order->id]);

} catch (Exception $e) {
    // Handle any exceptions that occur (e.g., API errors)
    echo json_encode(['error' => 'Order creation failed', 'message' => $e->getMessage()]);
}
?>
