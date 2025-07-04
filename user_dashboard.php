<?php
include('db.php');
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user details
$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Debug: Check session data
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Fetch Orders (Including page count, price)
$sql = "SELECT order_id, page_count, other_columns FROM orders WHERE user_id = ?";
$orderQuery = "SELECT order_id, document_name, page_count, price, status FROM orders WHERE user_id = ?";
$stmt = $conn->prepare($orderQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$ordersResult = $stmt->get_result();
$orders = $ordersResult->fetch_all(MYSQLI_ASSOC);

// Debug: Check if orders are fetched
if ($ordersResult->num_rows > 0) {
    echo "Orders found!";
} else {
    echo "No orders found.";
}

// Fetch Transactions (Including document info)
$transactionQuery = "SELECT t.*, o.document_name 
                     FROM transactions t 
                     JOIN orders o ON t.order_id = o.order_id 
                     WHERE o.user_id = ?";
$stmt = $conn->prepare($transactionQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$transactionsResult = $stmt->get_result();
$transactions = $transactionsResult->fetch_all(MYSQLI_ASSOC);

// Debug: Check if transactions are fetched
if ($transactionsResult->num_rows > 0) {
    echo "Transactions found!";
} else {
    echo "No transactions found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background: #343a40;
            color: white;
            padding: 15px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
        }
        .sidebar a:hover {
            background: #495057;
            padding-left: 10px;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Welcome, <?php echo $userName; ?></h3>
        <a href="#orders">Order History</a>
        <a href="#transactions">Transaction History</a>
        <a href="#upload">Upload Document</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="content">
        <!-- Order History Section -->
        <h2 id="orders">Order History</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Document</th>
                    <th>Page Count</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['order_id']; ?></td>
                            <td><?php echo $order['document_name']; ?></td>
                            <td><?php echo $order['page_count']; ?></td>
                            <td>₹<?php echo $order['price']; ?></td>
                            <td><?php echo ucfirst($order['status']); ?></td>
                            <td>
                                <?php if ($order['status'] == 'pending'): ?>
                                    <a href="reactivate_order.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-primary btn-sm">Reactivate</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">No orders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Transaction History Section -->
        <h2 id="transactions">Transaction History</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Order</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($transactions) > 0): ?>
                    <?php foreach ($transactions as $txn): ?>
                        <tr>
                            <td><?php echo $txn['transaction_id']; ?></td>
                            <td><?php echo $txn['document_name']; ?></td>
                            <td>₹<?php echo $txn['amount']; ?></td>
                            <td><?php echo ucfirst($txn['status']); ?></td>
                            <td><?php echo $txn['created_at']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No transactions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Upload Document Section -->
        <h2 id="upload">Upload Document</h2>
        <form action="upload_document.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="document" class="form-label">Select Document</label>
                <input type="file" name="document" id="document" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="copies" class="form-label">Number of Copies</label>
                <input type="number" name="copies" id="copies" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="color" class="form-label">Color</label>
                <select name="color" id="color" class="form-select" required>
                    <option value="B&W">Black & White</option>
                    <option value="Color">Color</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="size" class="form-label">Paper Size</label>
                <select name="size" id="size" class="form-select" required>
                    <option value="A4">A4</option>
                    <option value="A3">A3</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Place Order</button>
        </form>
    </div>
</body>
</html>
