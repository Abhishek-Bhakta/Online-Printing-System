<?php
session_start();
include 'config.php';

if (!isset($_SESSION['shopkeeper_id'])) {
    header("Location: login.php");
    exit();
}

$shopkeeper_id = (int) $_SESSION['shopkeeper_id'];

try {
    // Fetch total orders
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_orders FROM documents WHERE shopkeeper_id = :shopkeeper_id");
    $stmt->execute(['shopkeeper_id' => $shopkeeper_id]);
    $total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];

    $stmt = $conn->prepare("SELECT transaction_id, payment_id, amount, payment_status, transaction_date 
    FROM transactions 
    WHERE shopkeeper_id = :shopkeeper_id 
    ORDER BY transaction_date DESC");
$stmt->execute(['shopkeeper_id' => $shopkeeper_id]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch today's orders
    $today_date = date('Y-m-d');
$stmt = $conn->prepare("SELECT COUNT(*) AS today_orders FROM documents WHERE shopkeeper_id = :shopkeeper_id AND DATE(uploaded_at) = :today_date");
$stmt->execute(['shopkeeper_id' => $shopkeeper_id, 'today_date' => $today_date]);
$today_orders = $stmt->fetch(PDO::FETCH_ASSOC)['today_orders'];


    // Fetch total earnings
    $stmt = $conn->prepare("SELECT SUM(amount) AS total_earnings FROM transactions WHERE shopkeeper_id = :shopkeeper_id");
    $stmt->execute(['shopkeeper_id' => $shopkeeper_id]);
    $total_earnings = $stmt->fetch(PDO::FETCH_ASSOC)['total_earnings'] ?? 0;

    // Fetch today's earnings
    $stmt = $conn->prepare("SELECT SUM(amount) AS today_earnings FROM transactions WHERE shopkeeper_id = :shopkeeper_id AND DATE(transaction_date) = :today_date");
    $stmt->execute(['shopkeeper_id' => $shopkeeper_id, 'today_date' => $today_date]);
    $today_earnings = $stmt->fetch(PDO::FETCH_ASSOC)['today_earnings'] ?? 0;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Fetch shopkeeper details
$stmt = $conn->prepare("SELECT username FROM shopkeepers WHERE shopkeeper_id = ?");
$stmt->execute([$shopkeeper_id]);
$shopkeeper = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $shopkeeper['username'];

// Fetch shop details
$stmt = $conn->prepare("SELECT shop_id, shop_name, status FROM shops WHERE shopkeeper_id = ?");
$stmt->execute([$shopkeeper_id]);
$shop = $stmt->fetch(PDO::FETCH_ASSOC);
$shop_id = $shop['shop_id'];
$shop_name = $shop['shop_name'];
$shop_status = isset($shop['status']) ? $shop['status'] : 'offline';

// Generate unique QR code URL for this shop
$shop_url = "upload.php?shop_id=" . urlencode($shop_id);

// Fetch new orders
$stmt = $conn->prepare("SELECT * FROM documents WHERE shop_id = ? AND order_status = 'new' ORDER BY order_date DESC");
$stmt->execute([$shop_id]);
$new_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all orders
$stmt = $conn->prepare("SELECT * FROM documents WHERE shop_id = ? ORDER BY order_date DESC");
$stmt->execute([$shop_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch order history
$stmt = $conn->prepare("SELECT * FROM documents WHERE shop_id = ? ORDER BY order_date DESC");
$stmt->execute([$shop_id]);
$order_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch shop details including prices
$stmt = $conn->prepare("SELECT shop_name, price_bw, price_color FROM shops WHERE shopkeeper_id = ?");
$stmt->execute([$shopkeeper_id]);
$shop = $stmt->fetch(PDO::FETCH_ASSOC);

// Default values agar pehle se price set nahi hai
$shop_name = $shop['shop_name'];
$price_bw = isset($shop['price_bw']) ? $shop['price_bw'] : 0;
$price_color = isset($shop['price_color']) ? $shop['price_color'] : 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopkeeper Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="style.css">

</head>
<body>

<h2>Welcome, <?php echo htmlspecialchars($username); ?> of <?php echo htmlspecialchars($shop_name); ?></h2>
<p>This is your personal shopkeeper dashboard.</p>
<a href="orders.php">View Details</a>
<a href="index.php">View Details</a>

<div class="dashboard-container">
        <h2>Shopkeeper Dashboard</h2>

        <div class="stats-container">
            <div class="stat-box">
                <h3>Total Orders</h3>
                <p><?php echo $total_orders; ?></p>
            </div>
            <div class="stat-box">
                <h3>Today's Orders</h3>
                <p><?php echo $today_orders; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Earnings</h3>
                <p>₹<?php echo number_format($total_earnings, 2); ?></p>
            </div>
            <div class="stat-box">
                <h3>Today's Earnings</h3>
                <p>₹<?php echo number_format($today_earnings, 2); ?></p>
            </div>
        </div>
    </div>
<!-- Display shop status -->
<p><strong>Current Status:</strong> <span id="shop_status"><?= ucfirst($shop_status); ?></span></p>
<button onclick="toggleStatus()">Go Online/Offline</button>

<!-- QR Code Generator -->
<h3>Generate Shop QR Code</h3>
<button onclick="generateQR()">Generate QR Code</button>
<div id="qrCode"></div>
<button id="downloadQR" style="display:none;" onclick="downloadQR()">Download QR Code</button>

<h3>Set Printing Prices</h3>
    <form method="POST" action="features/update_prices.php">
        <label>Black & White Price (per page):</label>
        <input type="number" name="price_bw" value="<?= htmlspecialchars($price_bw); ?>" step="0.1" required><br><br>

        <label>Color Print Price (per page):</label>
        <input type="number" name="price_color" value="<?= htmlspecialchars($price_color); ?>" step="0.1" required><br><br>

        <button type="submit">Update Prices</button>
    </form>

<h3>Your Uploaded Orders:</h3>
<?php if (count($orders) > 0): ?>
    <?php foreach ($orders as $order): ?>
        <div id="order_<?php echo $order['document_id']; ?>">
            <p><strong>Document:</strong> <a href="uploads/<?php echo htmlspecialchars($shop_name); ?>/<?php echo htmlspecialchars($order['file_name']); ?>" target="_blank"><?php echo htmlspecialchars($order['file_name']); ?></a></p>
            <p><strong>Copies:</strong> <?php echo htmlspecialchars($order['copies'] ?? 'N/A'); ?></p>
            <p><strong>Size:</strong> <?php echo htmlspecialchars($order['size'] ?? 'N/A'); ?></p>
            <p><strong>Print Type:</strong> <?php echo $order['print_type'] === 'color' ? 'Color' : 'Black & White'; ?></p>
            <p><strong>Verification Code:</strong> <?php echo htmlspecialchars($order['verification_code'] ?? 'N/A'); ?></p>
            <p><strong>Status:</strong> <span id="status_<?php echo $order['document_id']; ?>"><?php echo ucfirst($order['order_status']); ?></span></p>
            <button onclick="updateStatus(<?php echo $order['document_id']; ?>, 'printing')">Mark as Printing</button>
            <button onclick="updateStatus(<?php echo $order['document_id']; ?>, 'printed')">Mark as Printed</button>
            <hr>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No documents uploaded yet.</p>
<?php endif; ?>

<h3>Your Order History:</h3>
<?php if (count($order_history) > 0): ?>
    <?php foreach ($order_history as $order): ?>
        <div>
            <p><strong>Document:</strong> <a href="uploads/<?php echo htmlspecialchars($shop_name); ?>/<?php echo htmlspecialchars($order['file_name']); ?>" target="_blank"><?php echo htmlspecialchars($order['file_name']); ?></a></p>
            <p><strong>Copies:</strong> <?php echo htmlspecialchars($order['copies'] ?? 'N/A'); ?></p>
            <p><strong>Size:</strong> <?php echo htmlspecialchars($order['size'] ?? 'N/A'); ?></p>
            <p><strong>Print Type:</strong> <?php echo $order['print_type'] === 'color' ? 'Color' : 'Black & White'; ?></p>
            <p><strong>Verification Code:</strong> <?php echo htmlspecialchars($order['verification_code'] ?? 'N/A'); ?></p>
            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
            <hr>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No order history found.</p>
<?php endif; ?>

<div class="transaction-history-container">
            <h3>Transaction History</h3>
            <table class="transaction-table">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Payment ID</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['payment_id']); ?></td>
                            <td>₹<?php echo number_format($transaction['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($transaction['payment_status']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($transaction['transaction_date'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
<script>
function toggleStatus() {
    $.ajax({
        url: 'features/update_shop_status.php',
        type: 'POST',
        success: function(response) {
            if (response === "success") {
                let currentStatus = $("#shop_status").text().trim().toLowerCase();
                let newStatus = (currentStatus === "offline") ? "online" : "offline";
                $("#shop_status").text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
            } else {
                alert("Failed to update status!");
            }
        }
    });
}

function updateStatus(document_id, status) {
    $.ajax({
        url: 'features/update_status.php',
        type: 'POST',
        data: { document_id: document_id, status: status },
        success: function(response) {
            if (response === "success") {
                $("#status_" + document_id).text(status.charAt(0).toUpperCase() + status.slice(1));
            } else {
                alert("Failed to update status!");
            }
        }
    });
}

function generateQR() {
    var shopURL = "<?php echo $shop_url; ?>";
    var shopName = "<?php echo htmlspecialchars($shop_name); ?>";
    var shopkeeperName = "<?php echo htmlspecialchars($username); ?>";
    var qrCodeDiv = document.getElementById("qrCode");

    var qrImage = new Image();
    qrImage.src = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" + encodeURIComponent(shopURL);

    qrImage.onload = function() {
        var canvas = document.createElement("canvas");
        var ctx = canvas.getContext("2d");

        canvas.width = qrImage.width;
        canvas.height = qrImage.height + 60;

        ctx.drawImage(qrImage, 0, 30);

        // Add text on top of QR code
        ctx.font = "bold 14px Arial";
        ctx.fillStyle = "#000000";
        ctx.textAlign = "center";
        ctx.fillText(shopName, canvas.width / 2, 20);

        ctx.font = "italic 12px Arial";
        ctx.fillText(shopkeeperName, canvas.width / 2, qrImage.height + 40);

        // Set the id for the canvas so it's accessible for download
        canvas.id = "qrCanvas";

        qrCodeDiv.innerHTML = "";
        qrCodeDiv.appendChild(canvas);

        // Show the download button once the QR code is generated
        document.getElementById("downloadQR").style.display = "block";
    };
}
function downloadQR() {
    var canvas = document.getElementById("qrCanvas");
    if (!canvas) {
        alert("QR code not generated yet!");
        return;
    }

    // Get the canvas content as a Blob (binary large object)
    canvas.toBlob(function(blob) {
        // Create a link to download the Blob content
        var link = document.createElement("a");

        // Create a URL for the Blob
        var url = URL.createObjectURL(blob);

        // Set up the download link
        link.href = url;
        link.download = "shop_qr_code_<?php echo $shop_id; ?>.png"; // Dynamic file name

        // Programmatically trigger the click to download
        link.click();

        // Clean up the object URL after download
        URL.revokeObjectURL(url);
    }, "image.png");
}

</script>

</body>
</html>
