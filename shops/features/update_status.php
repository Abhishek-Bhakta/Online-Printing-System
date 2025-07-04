<?php
session_start();
include '../config.php';

if (!isset($_SESSION['shopkeeper_id'])) {
    echo "error";
    exit();
}

$shopkeeper_id = (int) $_SESSION['shopkeeper_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['document_id'], $_POST['status'])) {
    $document_id = (int) $_POST['document_id'];
    $status = $_POST['status'];

    if ($status === 'printing' || $status === 'printed') {
        $stmt = $conn->prepare("UPDATE documents SET order_status = ? WHERE document_id = ? AND shop_id = ?");
        if ($stmt->execute([$status, $document_id, $shopkeeper_id])) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
} else {
    echo "error";
}
?>
