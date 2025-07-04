<?php
require('vendor/autoload.php'); // Composer autoload file

// Razorpay API key and secret
\Razorpay\Api\Api::setApiKey('rzp_test_XeReFqwLC7foia', 'RODDAw3hpmcJ7tuF1kDVzzzx');

// Getting POST data from Razorpay
$razorpay_signature = $_POST['razorpay_signature'];
$razorpay_payment_id = $_POST['razorpay_payment_id'];
$razorpay_order_id = $_POST['razorpay_order_id'];

// Fetch the order using the order ID
$api = new \Razorpay\Api\Api('rzp_test_XeReFqwLC7foia', 'RODDAw3hpmcJ7tuF1kDVzzzx');
$order = $api->order->fetch($razorpay_order_id);

// Step 2: Verify Payment Signature
$generated_signature = $api->utility->verifyPaymentSignature([
    'razorpay_order_id' => $razorpay_order_id,
    'razorpay_payment_id' => $razorpay_payment_id,
    'razorpay_signature' => $razorpay_signature
]);

if ($generated_signature == $razorpay_signature) {
    echo "Payment Verified";
    // Handle success (store payment details in your database)
} else {
    echo "Payment Verification Failed";
}
?>
