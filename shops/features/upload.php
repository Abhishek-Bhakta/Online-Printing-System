<?php
session_start();
include '../config.php'; // Ensure database connection is included

// Check if the user is logged in as a shopkeeper
if (!isset($_SESSION['shopkeeper_id'])) {
    header("Location: ../login.php");
    exit();
}

$shopkeeper_id = $_SESSION['shopkeeper_id']; // Shopkeeper ID from the session

// Fetch shopkeeper username based on shopkeeper_id
$stmt = $conn->prepare("SELECT username FROM shopkeepers WHERE shopkeeper_id = ?");
$stmt->execute([$shopkeeper_id]);
$shopkeeper = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$shopkeeper) {
    echo "Shopkeeper not found.";
    exit();
}

$username = $shopkeeper['username']; // Get the username from the fetched data

// Check if the shopkeeper exists in the 'shopkeepers' table
$stmt = $conn->prepare("SELECT shop_id FROM shops WHERE shopkeeper_id = ?");
$stmt->execute([$shopkeeper_id]);
$shop = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$shop) {
    echo "Shop not found for this shopkeeper.";
    exit();
}

$shop_id = $shop['shop_id']; // Get the shop_id

// Define the upload directory for the logged-in shopkeeper
$uploadDir = "uploads/$username/";

// Check if the directory exists, if not, create it
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['document'])) {
    $fileName = basename($_FILES["document"]["name"]);
    $targetFile = $uploadDir . $fileName;

    // Get order options
    $copies = $_POST['copies'] ?? 1;
    $size = $_POST['size'] ?? 'A4';
    $printType = $_POST['printType'] ?? 'black_white';

    // Generate a unique 4-digit verification code
    $verificationCode = rand(1000, 9999);

    // Try moving the uploaded file
    if (move_uploaded_file($_FILES["document"]["tmp_name"], $targetFile)) {
        // Insert the document into the database for shopkeeper uploads
        $stmt = $conn->prepare("INSERT INTO documents (file_name, copies, size, print_type, verification_code, shopkeeper_id, shop_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$fileName, $copies, $size, $printType, $verificationCode, $shopkeeper_id, $shop_id]);

        // Set confirmation message and redirect to the same page
        $_SESSION['confirmation_message'] = "File uploaded successfully! Your verification code is: $verificationCode";
        header("Location: index.html?shopkeeper=$username");
        exit();
    } else {
        echo "File upload failed! Please check directory permissions or file size.";
    }
}

$confirmationMessage = $_SESSION['confirmation_message'] ?? '';
unset($_SESSION['confirmation_message']);
?>

<h2>Upload Document for <?php echo htmlspecialchars($username); ?></h2>
<?php if ($confirmationMessage): ?>
    <p style="color: green;"><?php echo $confirmationMessage; ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="document" required><br><br>

    <label>Number of Copies:</label>
    <button type="button" id="decrement">-</button>
    <input type="number" name="copies" id="copies" value="1" min="1" readonly>
    <button type="button" id="increment">+</button><br><br>

    <label>Size:</label>
    <select name="size">
        <option value="A4">A4</option>
        <option value="A3">A3</option>
        <option value="A5">A5</option>
    </select><br><br>

    <label>Print Type:</label>
    <input type="radio" name="printType" value="color" checked> Color
    <input type="radio" name="printType" value="black_white"> Black and White<br><br>

    <button type="submit">Upload</button>
</form>

<script>
    // Increment/Decrement functionality for copies
    document.getElementById('increment').addEventListener('click', function() {
        var copiesInput = document.getElementById('copies');
        copiesInput.value = parseInt(copiesInput.value) + 1;
    });

    document.getElementById('decrement').addEventListener('click', function() {
        var copiesInput = document.getElementById('copies');
        if (parseInt(copiesInput.value) > 1) {
            copiesInput.value = parseInt(copiesInput.value) - 1;
        }
    });
</script>
