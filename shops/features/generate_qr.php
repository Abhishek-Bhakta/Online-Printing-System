<?php
require_once 'vendor/autoload.php'; // Include Composer's autoloader
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

if (isset($_GET['generate_qr']) && isset($_GET['shop_id'])) {
    $shop_id = $_GET['shop_id'];
    
    // Fetch shop details from the database (e.g., shop name and status)
    $stmt = $conn->prepare("SELECT shop_name FROM shops WHERE shopkeeper_id = ?");
    $stmt->execute([$shopkeeper_id]);
    $shop = $stmt->fetch(PDO::FETCH_ASSOC);
    $shop_name = $shop['shop_name'];

    // Generate QR code data (you can customize this part)
    $qr_data = "Shop Name: $shop_name, Shop ID: $shop_id";

    // Create the QR code
    $qrCode = new QrCode($qr_data);
    $writer = new PngWriter();
    $qrCode->setSize(300); // Set the size of the QR code

    // Save the QR code to a file
    $file_path = 'qrcodes/shop_' . $shop_id . '.png';
    $writer->writeFile($qrCode, $file_path);

    // Provide the file path to the generated QR code
    echo json_encode(['status' => 'success', 'file' => $file_path]);
    exit;
}
?>
