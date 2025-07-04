<?php
require('vendor/autoload.php'); // Razorpay SDK
use Razorpay\Api\Api;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php'; // Database Connection

$keyId = "rzp_test_XeReFqwLC7foia";
$keySecret = "RODDAw3hpmcJ7tuF1kDVzzzx";

$api = new Api($keyId, $keySecret);
// Get form data
$shopId = $_POST['shopId'];
$copies = (int) $_POST['copies'];
$pages = (int) $_POST['pages'];  // Ensure this is an integer
$size = $_POST['size'];
$color = $_POST['color'];

// Validate pages input
if (!is_numeric($pages) || $pages <= 0) {
    echo json_encode(['error' => 'Invalid number of pages.']);
    exit();
}

// Fetch shop details
$stmt = $pdo->prepare("SELECT shop_name, shopkeeper_name, price_bw, price_color FROM shops WHERE shop_id = ?");
$stmt->execute([$shopId]);
$shop = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$shop) {
    echo json_encode(['error' => 'Invalid shop selected.']);
    exit();
}

// Determine price based on print type
$pricePerCopy = ($color === 'color') ? $shop['price_color'] : $shop['price_bw'];

// Calculate total price including copies and pages
$totalPrice = $pricePerCopy * $copies * $pages * 100; // Multiply by pages and convert to paisa

// Store total price in session
$_SESSION['total_price'] = $totalPrice;

// Generate Razorpay Order
$receiptId = "order_rcptid_" . uniqid();
$orderData = [
    'receipt'         => $receiptId,
    'amount'          => $totalPrice,
    'currency'        => 'INR',
    'payment_capture' => 1,
    'notes'           => [
        'shop_name' => $shop['shop_name'],
        'shopkeeper_name' => $shop['shopkeeper_name'],
        'print_type' => ucfirst($color),
        'copies' => $copies,
        'pages' => $pages,  // Include pages in notes
        'total_price' => "₹" . ($totalPrice / 100)
    ]
];

$order = $api->order->create($orderData);
$orderId = $order['id']; // Razorpay Order ID

// Return response
echo json_encode([
    'order_id' => $orderId,
    'key' => $keyId,
    'shop_name' => $shop['shop_name'],
    'shopkeeper_name' => $shop['shopkeeper_name'],
    'print_type' => ucfirst($color),
    'copies' => $copies,
    'pages' => $pages,
    'total_price' => "₹" . ($totalPrice / 100)
]);

?>
