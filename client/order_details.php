<?php
session_start();
if (!isset($_SESSION['order_details'])) {
    echo "No order details found.";
    exit();
}

$order = $_SESSION['order_details'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
</head>
<body>
    <h2>Order Details</h2>
    <p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
    <p><strong>Shop Name:</strong> <?php echo $order['shop_name']; ?></p>
    <p><strong>Shopkeeper Name:</strong> <?php echo $order['shopkeeper_name']; ?></p>
    <p><strong>Print Type:</strong> <?php echo $order['print_type']; ?></p>
    <p><strong>Copies:</strong> <?php echo $order['copies']; ?></p>
    <p><strong>Total Price:</strong> <?php echo $order['total_price']; ?></p>
</body>
</html>
