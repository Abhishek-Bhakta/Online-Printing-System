<?php
session_start();
include 'config.php';

if (!isset($_SESSION['client_id'])) {
    echo "<tr><td colspan='8'>Unauthorized Access</td></tr>";
    exit();
}

$client_id = $_SESSION['client_id'];  
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT t.transaction_id, t.payment_id, t.amount, t.currency, t.payment_status, t.transaction_date, s.shop_name, t.order_id 
          FROM transactions t 
          JOIN shops s ON t.shopkeeper_id = s.shop_id 
          WHERE t.client_id = ? 
          AND (
              t.order_id LIKE ? OR 
              t.transaction_id LIKE ? OR 
              t.payment_id LIKE ? OR 
              s.shop_name LIKE ? OR 
              t.amount LIKE ? OR 
              t.transaction_date LIKE ?
          )
          ORDER BY t.transaction_date DESC";

$stmt = $pdo->prepare($query);
$searchParam = "%$searchTerm%";
$stmt->execute([$client_id, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);

$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($transactions) > 0) {
    foreach ($transactions as $transaction) {
        echo "<tr>
            <td>#{$transaction['order_id']}</td>
            <td>{$transaction['transaction_id']}</td>
            <td>{$transaction['shop_name']}</td>
            <td>{$transaction['payment_id']}</td>
            <td>₹{$transaction['amount']}</td>
            <td>{$transaction['currency']}</td>
            <td style='color: " . ($transaction['payment_status'] === 'Success' ? 'green' : ($transaction['payment_status'] === 'Failed' ? 'red' : 'orange')) . ";'>
                " . ucfirst($transaction['payment_status']) . "
            </td>
            <td>{$transaction['transaction_date']}</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='8'>No matching transactions found.</td></tr>";
}
?>
