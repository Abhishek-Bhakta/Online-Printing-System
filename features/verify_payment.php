<?php
require('vendor/autoload.php');
use Razorpay\Api\Api;

$keyId = "rzp_test_XeReFqwLC7foia";
$keySecret = "RODDAw3hpmcJ7tuF1kDVzzzx";

$api = new Api($keyId, $keySecret);

$paymentId = $_POST['razorpay_payment_id'];
$orderId = $_POST['razorpay_order_id'];
$signature = $_POST['razorpay_signature'];

$generatedSignature = hash_hmac('sha256', $orderId . "|" . $paymentId, $keySecret);

if ($generatedSignature === $signature) {
    echo "Payment Successful! Payment ID: " . $paymentId;
} else {
    echo "Payment Verification Failed!";
}
?>
