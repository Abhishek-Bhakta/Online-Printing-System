<?php
session_start();
include '../config.php';

if (!isset($_SESSION['shopkeeper_id'])) {
    header("Location: ../login.php");
    exit();
}

$shopkeeper_id = $_SESSION['shopkeeper_id'];

// Get current shop status
$stmt = $conn->prepare("SELECT status FROM shops WHERE shopkeeper_id = ?");
$stmt->execute([$shopkeeper_id]);
$shop = $stmt->fetch(PDO::FETCH_ASSOC);

if ($shop) {
    // Toggle status: If 'offline' then make 'online', else make 'offline'
    $new_status = ($shop['status'] === 'offline') ? 'online' : 'offline';
    // Update the status
    $stmt = $conn->prepare("UPDATE shops SET status = ? WHERE shopkeeper_id = ?");
    $stmt->execute([$new_status, $shopkeeper_id]);

    echo "success";  // Success response
} else {
    echo "failed";  // Failure response
}
?>
