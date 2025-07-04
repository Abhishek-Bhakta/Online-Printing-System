<?php
session_start();  // Start the session to access the stored data

if (isset($_SESSION['payment_id'])) {
    $paymentId = $_SESSION['payment_id'];
    $paymentStatus = $_SESSION['payment_status'];
} else {
    $paymentId = null;
    $paymentStatus = null;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Details</title>
</head>
<body>
    <h1>Payment Details</h1>
    <?php if ($paymentId): ?>
        <p><strong>Payment ID:</strong> <?php echo $paymentId; ?></p>
        <p><strong>Status:</strong> <?php echo $paymentStatus; ?></p>
    <?php else: ?>
        <p>No payment details available.</p>
    <?php endif; ?>
</body>
</html>
