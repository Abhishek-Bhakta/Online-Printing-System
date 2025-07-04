<?php
session_start();
include 'config.php';

if (!isset($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit();
}
$client_id = $_SESSION['client_id'];  // Ensure $client_id is initialized before using it

$client_name = htmlspecialchars($_SESSION['client_name']);
$razorpay_key = "rzp_test_XeReFqwLC7foia";
$stmt = $pdo->prepare("SELECT t.transaction_id, t.payment_id, t.amount, t.currency, t.payment_status, t.transaction_date, s.shop_name 
                       FROM transactions t 
                       JOIN shops s ON t.shopkeeper_id = s.shop_id 
                       WHERE t.client_id = ? 
                       ORDER BY t.transaction_date DESC");
$stmt->execute([$client_id]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch available shops
$stmt = $pdo->query("SELECT shop_id, shop_name, shop_address, latitude, longitude, status, shopkeeper_name, price_bw, price_color FROM shops");
$shops = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user's uploaded documents
$stmt = $pdo->prepare("SELECT d.file_name, d.copies, d.size, d.print_type, d.verification_code, d.order_status, d.order_date, s.shop_name FROM documents d JOIN shops s ON d.shop_id = s.shop_id WHERE d.client_id = ? ORDER BY d.order_date DESC");
$stmt->execute([$client_id]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>

<h1>Welcome, <?= $client_name; ?>!</h1>
<a href="client_logout.php">Logout</a>
<a href="index.php">View Details</a>

<h2>Upload Document</h2>
                   <!-- Add this HTML loader somewhere in your page (maybe below the form) -->
                   <div id="loader" style="display: none; text-align: center; margin-top: 20px;">
  <div class="spinner-border text-primary" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>
  <p>Processing your order, please wait...</p>
</div>
<form id="uploadForm" action="process_order.php" method="POST" enctype="multipart/form-data">
    <label for="shop_id">Select Shop:</label>
    <select id="shop_id" name="shop_id" required></select>

    <label for="document">Select Document:</label>
    <input type="file" id="document" name="document" required><br>

    <label for="copies">Number of Copies:</label>
    <input type="number" id="copies" name="copies" value="1" min="1" required><br>

    <label for="size">Size:</label>
    <select id="size" name="size" required>
        <option value="A4">A4</option>
        <option value="A3">A3</option>
    </select><br>

    <label for="color">Print Type:</label>
    <select id="color" name="color" required>
        <option value="color">Color</option>
        <option value="bw">Black & White</option>
    </select><br>
    <!-- Hidden page count field -->
    <input type="hidden" id="pages" name="pages">

    <p id="page-count-display" style="display: none;">Total Pages: <span id="page-count-value"></span></p>

    <button type="button" id="pay-btn">Proceed to Payment</button>
</form>


<h3>Shop Description</h3>
<div id="shop-description">
    <p><strong>Shop Name:</strong> <span id="shop-name"></span></p>
    <p><strong>Shopkeeper:</strong> <span id="shopkeeper-name"></span></p>
    <p><strong>Address:</strong> <span id="shop-address"></span></p>
    <p><strong>Print Price:</strong> B/W ₹<span id="price-bw"></span>  Colour ₹<span id="price-color"></span></p>
</div>

<div id="map" style="height: 300px; width: 100%; margin-top: 20px;"></div>

<h2>Your Uploaded Documents</h2>
<?php if ($documents): ?>
    <ul>
        <?php foreach ($documents as $doc): ?>
            <li>
                <strong>Document:</strong> <?= htmlspecialchars($doc['file_name']); ?><br>
                <strong>Shop:</strong> <?= htmlspecialchars($doc['shop_name']); ?><br>
                <strong>Copies:</strong> <?= htmlspecialchars($doc['copies']); ?><br>
                <strong>Size:</strong> <?= htmlspecialchars($doc['size']); ?><br>
                <strong>Print Type:</strong> <?= ($doc['print_type'] === 'color') ? 'Color' : 'Black & White'; ?><br>
                <strong>Verification Code:</strong> <span style="color:blue;"><?= htmlspecialchars($doc['verification_code']); ?></span><br>
                <strong>Status:</strong> 
                <span style="color: <?= $doc['order_status'] === 'completed' ? 'green' : ($doc['order_status'] === 'rejected' ? 'red' : 'orange'); ?>">
                    <?= ucfirst($doc['order_status']); ?>
                </span><br>
                <strong>Received At:</strong> <?= htmlspecialchars($doc['order_date']); ?><br>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No documents uploaded yet.</p>
<?php endif; ?>
<h2>Your Transaction History</h2>
<?php if ($transactions): ?>
    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Shop</th>
                <th>Payment ID</th>
                <th>Amount</th>
                <th>Currency</th>
                <th>Payment Status</th>
                <th>Payment Mode</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= htmlspecialchars($transaction['transaction_id']); ?></td>
                    <td><?= htmlspecialchars($transaction['shop_name']); ?></td>
                    <td><?= htmlspecialchars($transaction['payment_id']); ?></td>
                    <td>₹<?= htmlspecialchars($transaction['amount']); ?></td>
                    <td><?= htmlspecialchars($transaction['currency']); ?></td>
                    <td style="color: <?= $transaction['payment_status'] === 'Success' ? 'green' : ($transaction['payment_status'] === 'Failed' ? 'red' : 'orange'); ?>">
                        <?= ucfirst($transaction['payment_status']); ?>
                    </td>
                    <td><?= htmlspecialchars($transaction['payment_mode']); ?></td>
                    <td><?= htmlspecialchars($transaction['transaction_date']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No transactions found.</p>
<?php endif; ?>


<!-- Include Razorpay Checkout Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<!-- Include Leaflet Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    // Leaflet Map Setup
    const map = L.map('map').setView([19.19230, 72.84677], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);

    const shops = <?php echo json_encode($shops); ?>;
    const shopSelect = document.getElementById('shop_id');
 
    // Function to update the shop description
    function updateShopDescription(shopId) {
        const selectedShop = shops.find(shop => shop.shop_id == shopId);

        if (selectedShop) {
            document.getElementById('shop-name').textContent = selectedShop.shop_name;
            document.getElementById('shopkeeper-name').textContent = selectedShop.shopkeeper_name; // Assuming you have this field
            document.getElementById('shop-address').textContent = selectedShop.shop_address;
            document.getElementById('price-bw').textContent = selectedShop.price_bw;
            document.getElementById('price-color').textContent = selectedShop.price_color; // Assuming you have this field

            // Update map marker
            map.setView([selectedShop.latitude, selectedShop.longitude], 15); // Move map to selected shop
        }
    }

    // Add shops to the dropdown and map
    shops.forEach(shop => {
        const pin = L.marker([shop.latitude, shop.longitude]).addTo(map);
        pin.bindPopup(`<strong>${shop.shop_name}</strong><br>Status: ${shop.status}`);

        pin.on('click', () => {
            if (shop.status === 'offline') {
                alert("This shop is offline, please choose another shop.");
                return;
            }
            shopSelect.value = shop.shop_id;
            updateShopDescription(shop.shop_id);
            validateForm();
        });

        if (shop.status === 'online') {
            const option = document.createElement('option');
            option.value = shop.shop_id;
            option.textContent = shop.shop_name;
            shopSelect.appendChild(option);
        }
    });

    // Event listener for shop selection from the dropdown
    shopSelect.addEventListener('change', function () {
        const selectedShopId = this.value;
        updateShopDescription(selectedShopId);
        validateForm();
    });

    // Initially show description for the first shop in the list (if available)
    if (shops.length > 0) {
        updateShopDescription(shops[0].shop_id);
    }

    document.getElementById('pay-btn').addEventListener('click', async function () {
    const shopId = document.getElementById('shop_id').value;
    const copies = parseInt(document.getElementById('copies').value);
    const size = document.getElementById('size').value;
    const pages = parseInt(document.getElementById('pages').value);
    const color = document.getElementById('color').value;
    const documentFile = document.getElementById('document').files[0];

    if (!shopId || !copies || !size || !color || !documentFile) {
        alert("Please fill all fields before proceeding to payment.");
        return;
    }

    const selectedShop = shops.find(shop => shop.shop_id == shopId);
    if (!selectedShop) {
        alert("Invalid shop selection.");
        return;
    }

    const formData = new FormData();
    formData.append('document', documentFile);
    formData.append('shopId', shopId);
    formData.append('copies', copies);
    formData.append('pages', pages);
    formData.append('size', size);
    formData.append('color', color);

    const response = await fetch('razorpay_payment.php', {
        method: 'POST',
        body: formData
    });

    const data = await response.json();

    if (data.error) {
        alert(data.error);
        return;
    }

    const numberOfPages = data.pages;

    const pricePerCopy = (color === 'color') ? parseFloat(selectedShop.price_color) : parseFloat(selectedShop.price_bw);
    const totalAmount = pricePerCopy * pages * copies * 100; 

    var options = {
        "key": "<?= $razorpay_key; ?>",
        "amount": totalAmount,
        "currency": "INR",
        "name": selectedShop.shop_name,
        "description": `Print Order - ${selectedShop.shopkeeper_name}`,
        "order_id": data.order_id,
        "handler": function (response) {
            const paymentIdInput = document.getElementById('payment_id');
            const orderIdInput = document.getElementById('order_id');
            const verificationCodeInput = document.getElementById('verification_code');
            
            // Check if the elements exist before setting their values
            if (paymentIdInput) paymentIdInput.value = response.razorpay_payment_id;
            if (orderIdInput) orderIdInput.value = data.order_id;
            if (verificationCodeInput) verificationCodeInput.value = data.verification_code;

            // Submit the form manually
            document.getElementById('uploadForm').submit();
        },
        "prefill": {
            "email": "customer@example.com",
            "contact": "9999999999"
        },
        "theme": {
            "color": "#3399cc"
        }
    };

    var rzp1 = new Razorpay(options);
    rzp1.open();
});



// Set the default page count to 1 when the page loads
document.getElementById('pages').value = 1;
document.getElementById('page-count-display').style.display = 'block'; // Ensure it's visible
document.getElementById('page-count-value').textContent = 0; // Display the default page count as 1

// Detect when a document is selected
document.getElementById('document').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const pageCountField = document.getElementById('pages');
    const pageCountDisplay = document.getElementById('page-count-display');
    const pageCountValue = document.getElementById('page-count-value');
    
    // If the document is a PDF, count pages
    if (file && file.type === 'application/pdf') {
        const reader = new FileReader();

        reader.onload = function(e) {
            const pdfData = e.target.result;

            // Use PDF.js to count pages
            const loadingTask = pdfjsLib.getDocument(pdfData);
            loadingTask.promise.then(function(pdf) {
                const numPages = pdf.numPages;
                
                // Set the page count in the hidden field and display it
                pageCountField.value = numPages;
                pageCountDisplay.style.display = 'block'; // Make page count visible
                pageCountValue.textContent = numPages; // Display the page count
            }).catch(function(error) {
                console.error("Error loading PDF: ", error);
            });
        };

        // Read the file as ArrayBuffer for PDF.js
        reader.readAsArrayBuffer(file);
    } else {
        // If the document is not a PDF, keep the page count as 1
        pageCountField.value = 1;
        pageCountDisplay.style.display = 'block'; // Make page count visible
        pageCountValue.textContent = 1; // Display 1 as page count
    }
});
    // Validate form and enable/disable the payment button
    function validateForm() {
        const shopId = document.getElementById('shop_id').value;
        const documentField = document.getElementById('document').files.length; // document file length
        const copies = document.getElementById('copies').value;
        const size = document.getElementById('size').value;
        const color = document.getElementById('color').value;

        const payButton = document.getElementById('pay-btn');

        // Validate each field
        const isValidCopies = copies >= 1 && !isNaN(copies);
        const isValidSize = size === "A4" || size === "A3";
        const isValidColor = color === "color" || color === "bw";

        // Enable payment button if all fields are valid
        if (shopId && documentField > 0 && isValidCopies && isValidSize && isValidColor) {
            payButton.disabled = false;
        } else {
            payButton.disabled = true;
        }
    }

    // Enable payment button on form input change
    document.getElementById('uploadForm').addEventListener('input', validateForm);
    document.getElementById('document').addEventListener('change', validateForm); // For document field
    document.getElementById('copies').addEventListener('input', validateForm);
    document.getElementById('size').addEventListener('change', validateForm);
    document.getElementById('color').addEventListener('change', validateForm);
</script>
