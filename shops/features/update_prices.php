<?php
session_start();
include '../config.php';

// Shopkeeper login check karein
if (!isset($_SESSION['shopkeeper_id'])) {
    header("Location: login.php");
    exit();
}

$shopkeeper_id = $_SESSION['shopkeeper_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $price_bw = isset($_POST['price_bw']) ? (float) $_POST['price_bw'] : 0;
    $price_color = isset($_POST['price_color']) ? (float) $_POST['price_color'] : 0;

    // Shops table me shopkeeper ka price update karein
    $stmt = $conn->prepare("UPDATE shops SET price_bw = ?, price_color = ? WHERE shopkeeper_id = ?");
    if ($stmt->execute([$price_bw, $price_color, $shopkeeper_id])) {
        echo "<script>alert('Prices updated successfully!'); window.location.href='../dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating prices!'); window.location.href='../dashboard.php';</script>";
    }
}
?>
