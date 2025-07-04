<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer
require 'src/PHPMailer.php';
require 'src/Exception.php';
require 'src/SMTP.php';

// Start session
session_start();
include 'config.php'; // PDO connection

if (!$pdo) die("Database connection failed.");
if (!isset($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit();
}

$client_id = $_SESSION['client_id'];
$client_name = htmlspecialchars($_SESSION['client_name']);
$total_price = isset($_SESSION['total_price']) ? $_SESSION['total_price'] / 100 : 0;

$order_id = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
$transaction_id = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
$payment_id = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
$verification_code = rand(1000, 9999);

// Upload handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
        $document = $_FILES['document'];
        $shop_id = $_POST['shop_id'];
        $copies = $_POST['copies'];
        $size = $_POST['size'];
        $color = $_POST['color'];

        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
        if (!in_array($document['type'], $allowed_types)) {
            die("Invalid file type.");
        }

        $file_name = time() . "_" . basename($document['name']);
        $file_path = "uploads/" . $file_name;

        if (move_uploaded_file($document['tmp_name'], $file_path)) {
            // Get shopkeeper ID
            $stmt = $pdo->prepare("SELECT shopkeeper_id FROM shops WHERE shop_id = ?");
            $stmt->execute([$shop_id]);
            $shopkeeper = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($shopkeeper) {
                $stmt = $pdo->prepare("INSERT INTO documents (client_id, shop_id, shopkeeper_id, file_name, copies, size, print_type, verification_code, order_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$client_id, $shop_id, $shopkeeper['shopkeeper_id'], $file_name, $copies, $size, $color, $verification_code, $order_id]);

                $stmt = $pdo->prepare("INSERT INTO transactions (client_id, shopkeeper_id, transaction_id, payment_id, amount, payment_status, order_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$client_id, $shopkeeper['shopkeeper_id'], $transaction_id, $payment_id, $total_price, 'success', $order_id]);

                // Send email
                $stmt = $pdo->prepare("SELECT email FROM clients WHERE client_id = ?");
                $stmt->execute([$client_id]);
                $client = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($client) {
                    $to = $client['email'];
                    $mail = new PHPMailer(true);

                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'printifyglobal@gmail.com';
                        $mail->Password = 'dyeo qdfe pkbr lgth';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        $mail->setFrom('printifyglobal@gmail.com', 'Printify');
                        $mail->addAddress($to);

                        $mail->isHTML(false);
                        $mail->Subject = 'Order Confirmation from Printify';
                        $mail->Body = "
Dear $client_name,

We are delighted to confirm your order with Printify!

Your order ID is: $order_id
Your Verification Code is: $verification_code
Amount paid: ₹" . number_format($total_price, 2) . "

Thank you for choosing Printify!

Best,
Printify Team
";

                        $mail->send();
                        $_SESSION['email_sent_message'] = 'Order Confirmation email sent to ' . $to;
                    } catch (Exception $e) {
                        $_SESSION['email_sent_message'] = "Mailer Error: {$mail->ErrorInfo}";
                    }
                }

                // Store values in session to show on next page
                $_SESSION['order_details'] = [
                    'order_id' => $order_id,
                    'transaction_id' => $transaction_id,
                    'verification_code' => $verification_code,
                    'total_price' => $total_price
                ];

                header("Location: confirmation_page.php");
                exit();
            } else {
                die("Invalid shop selected.");
            }
        } else {
            die("File upload failed.");
        }
    } else {
        die("No document uploaded.");
    }
}
echo "done";
exit; 
?>
