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

    $stmt = $conn->prepare("SELECT transaction_id, payment_id, amount, payment_status, transaction_date ,order_id
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

// Fetch order history with client name
$stmt = $conn->prepare("
    SELECT d.*, c.name AS client_name 
    FROM documents d
    JOIN clients c ON d.client_id = c.client_id
    WHERE d.shop_id = ?
    ORDER BY d.order_date DESC
");
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
$shopkeeper_id = $_SESSION['shopkeeper_id'];

// Fetch shopkeeper details
$stmt = $conn->prepare("SELECT shopkeeper_name, email, phone, username FROM shopkeepers WHERE shopkeeper_id = ?");
$stmt->execute([$shopkeeper_id]);
$shopkeeper = $stmt->fetch(PDO::FETCH_ASSOC);

$shopkeeper_id = $_SESSION['shopkeeper_id'];

// Fetch shop details
$stmt = $conn->prepare("SELECT shop_name, shop_address, latitude, longitude, price_bw, price_color FROM shops WHERE shopkeeper_id = ?");
$stmt->execute([$shopkeeper_id]);
$shop = $stmt->fetch(PDO::FETCH_ASSOC);

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_shop_details'])) {
        $shop_name = $_POST['shop_name'];
        $shop_address = $_POST['shop_address'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $price_bw = $_POST['price_bw'];
        $price_color = $_POST['price_color'];

        $stmt = $conn->prepare("UPDATE shops SET shop_name = ?, shop_address = ?, latitude = ?, longitude = ?, price_bw = ?, price_color = ? WHERE shopkeeper_id = ?");
        $stmt->execute([$shop_name, $shop_address, $latitude, $longitude, $price_bw, $price_color, $shopkeeper_id]);
    }

    // Refresh page after update
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Personal Info Update
    if (isset($_POST['update_personal_info'])) {
        $shopkeeper_name = $_POST['shopkeeper_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        $stmt = $conn->prepare("UPDATE shopkeepers SET shopkeeper_name = ?, email = ?, phone = ? WHERE shopkeeper_id = ?");
        $stmt->execute([$shopkeeper_name, $email, $phone, $shopkeeper_id]);
    }

    // Security Credentials Update
    if (isset($_POST['update_security_credentials'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("UPDATE shopkeepers SET username = ?, password = ? WHERE shopkeeper_id = ?");
        $stmt->execute([$username, $hashed_password, $shopkeeper_id]);
    }
}

?>