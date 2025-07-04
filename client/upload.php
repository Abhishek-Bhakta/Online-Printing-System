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
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Mega Able bootstrap admin template by codedthemes </title>
      <!-- Favicon icon -->
      <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet">
      <!-- Required Fremwork -->
      <link rel="stylesheet" type="text/css" href="assets/css/bootstrap/css/bootstrap.min.css">
      <!-- themify icon -->
      <link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
      <!-- Font Awesome -->
      <link rel="stylesheet" type="text/css" href="assets/icon/font-awesome/css/font-awesome.min.css">
      <!-- scrollbar.css -->
      <link rel="stylesheet" type="text/css" href="assets/css/jquery.mCustomScrollbar.css">
     <!-- Style.css -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

      <link rel="stylesheet" type="text/css" href="assets/css/style.css">
      <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
      <script>
        const shops = <?php echo $shopsJson; ?>;
        const razorpayKey = "<?php echo htmlspecialchars($razorpay_key, ENT_QUOTES, 'UTF-8'); ?>";
    </script>
  </head>

  <body>
  <!-- Pre-loader end -->
  <div id="pcoded" class="pcoded">
      <div class="pcoded-overlay-box"></div>
      <div class="pcoded-container navbar-wrapper">
          <nav class="navbar header-navbar pcoded-header">
              <div class="navbar-wrapper">
                  <div class="navbar-logo">
                      <a class="mobile-menu waves-effect waves-light" id="mobile-collapse" href="#!">
                          <i class="ti-menu"></i>
                      </a>
                      <a href="index.html">
                        <span style="margin-left:60px; margin-right:60px; font-size: 23px; font-weight:bold; display: block; text-transform: capitalize;">Printify</span>
                      </a>
                      <a class="mobile-options waves-effect waves-light">
                          <i class="ti-more"></i>
                      </a>
                  </div>
                
                  <div class="navbar-container container-fluid">
                      <ul class="nav-left">
                          <li>
                              <div class="sidebar_toggle"><a href="javascript:void(0)"><i class="ti-menu"></i></a></div>
                          </li>
                      </ul>

                      
                      <ul class="nav-right">
                          <li class="user-profile header-notification">
                              <a href="#!" class="waves-effect waves-light">
                                  <img src="assets/images/avatar-4.jpg" class="img-radius" alt="User-Profile-Image">
                                  <span><?= $client_name; ?></span>
                                  <i class="ti-angle-down"></i>
                              </a>
                              <ul class="show-notification profile-notification">
                                  <li class="waves-effect waves-light">
                                      <a href="#!">
                                          <i class="ti-settings"></i> Settings
                                      </a>
                                  </li>
                                  <li class="waves-effect waves-light">
                                      <a href="user-profile.html">
                                          <i class="ti-user"></i> Profile
                                      </a>
                                  </li>
                                  <li class="waves-effect waves-light">
                                      <a href="email-inbox.html">
                                          <i class="ti-email"></i> My Messages
                                      </a>
                                  </li>
                                  <li class="waves-effect waves-light">
                                      <a href="auth-lock-screen.html">
                                          <i class="ti-lock"></i> Lock Screen
                                      </a>
                                  </li>
                                  <li class="waves-effect waves-light">
                                      <a href="auth-normal-sign-in.html">
                                          <i class="ti-layout-sidebar-left"></i> Logout
                                      </a>
                                  </li>
                              </ul>
                          </li>
                      </ul>
                  </div>
              </div>
          </nav>
          <div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <nav class="pcoded-navbar">
            <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
            <div class="pcoded-inner-navbar main-menu">
                <div class="main-menu-header">
                    <img class="img-80 img-radius" src="assets/images/avatar-4.jpg" alt="User-Profile-Image">
                    <div class="user-details">
                        <span id="more-details"><?= $client_name; ?><i class="fa fa-caret-down"></i></span>
                    </div>
                </div>

                <div class="main-menu-content">
                    <ul>
                        <li class="more-details">
                            <a href="user-profile.html"><i class="ti-user"></i>View Profile</a>
                            <a href="#!"><i class="ti-settings"></i>Settings</a>
                            <a href="auth-normal-sign-in.html"><i class="ti-layout-sidebar-left"></i>Logout</a>
                        </li>
                    </ul>
                </div>

                <div class="p-15 p-b-0">
                    <form class="form-material">
                        <div class="form-group form-primary"></div>
                    </form>
                </div>

                <!-- Dashboard Link -->
                <ul class="pcoded-item pcoded-left-item">
                    <li>
                        <a href="index.php" class="waves-effect waves-dark">
                            <span class="pcoded-micon"><i class="ti-home"></i><b>D</b></span>
                            <span class="pcoded-mtext" data-i18n="nav.dash.main">Dashboard</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
                </ul>

                <!-- Forms & Tables Section -->
                <div class="pcoded-navigation-label" data-i18n="nav.category.forms">Forms &amp; Tables</div>
                <ul class="pcoded-item pcoded-left-item">
                    <li class="active">
                        <a href="upload.php" class="waves-effect waves-dark">
                            <span class="pcoded-micon"><i class="ti-upload"></i><b>FC</b></span>
                            <span class="pcoded-mtext" data-i18n="nav.form-components.main">Upload Documents</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
                    <li>
                        <a href="order_history.php" class="waves-effect waves-dark">
                            <span class="pcoded-micon"><i class="ti-package"></i><b>FC</b></span>
                            <span class="pcoded-mtext" data-i18n="nav.form-components.main">Order History</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
                </ul>

                <!-- Chart & Maps Section -->
                <div class="pcoded-navigation-label" data-i18n="nav.category.forms">Chart &amp; Maps</div>
                <ul class="pcoded-item pcoded-left-item">
                    <li>
                        <a href="transaction.php" class="waves-effect waves-dark">
                            <span class="pcoded-micon"><i class="ti-layers"></i><b>FC</b></span>
                            <span class="pcoded-mtext" data-i18n="nav.form-components.main">Transaction History</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
                    <li>
                        <a href="map.php" class="waves-effect waves-dark">
                            <span class="pcoded-micon"><i class="ti-layers"></i><b>FC</b></span>
                            <span class="pcoded-mtext" data-i18n="nav.form-components.main">Maps</span>
                            <span class="pcoded-mcaret"></span>
                        </a>
                    </li>
                </ul>

            </div>
        </nav>
  
                  <div class="pcoded-content">
                      <!-- Page-header start -->
                      
                     <main>
                    <!-- Full Page Loader -->
<!-- Full Page Loader -->
<div id="loader" style="
    display: none;
    position: fixed;
    text-align: center;
    width: 100vw; height: 100vh;
    background-color: rgba(0, 0, 0, 0.6); /* Semi-transparent background */
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    color: white;
    font-family: Arial, sans-serif;
">
  <!-- Spinner -->
  <div class="spinner-border text-light" style="margin-top: 50px; width: 3rem; height: 3rem;" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>
  <!-- Text Below Spinner -->
  <p style="margin-top: 50px; font-size: 1.2rem; text-align: center;">Processing your order, please wait...</p>
</div>


                     <div class="container mt-5">
                        <div class="content">
                            <div class="bord">
                                      <div class="page-header-title">
                                          <h5 class="m-b-10">Upload Documents</h5>
                                          <p class="m-b-0">send documents for printing</p>
                                      </div>
        <div class="row g-4">
            <div class="col-md-6">
            <div class="form-section">
    <form id="uploadForm" action="process_order.php" method="POST" enctype="multipart/form-data">
        <label for="shop_id" class="form-label">Select Shop:</label>
        <select id="shop_id" name="shop_id" class="form-select" required></select>

        <label for="document" class="form-label">Select Document:</label>
        <input type="file" id="document" name="document" class="form-control" required>

        <div class="row">
            <div class="col-md-6">
                <label for="copies" class="form-label">Number of Copies:</label>
                <input type="number" id="copies" name="copies" class="form-control" value="1" min="1" required>
            </div>
            <div class="col-md-6">
                <label for="size" class="form-label">Size:</label>
                <select id="size" name="size" class="form-select" required>
                    <option value="A4">A4</option>
                    <option value="A3">A3</option>
                </select>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label for="color" class="form-label">Print Type:</label>
                <select id="color" name="color" class="form-select" required>
                    <option value="color">Color</option>
                    <option value="bw">Black & White</option>
                </select>
            </div>
            <div class="col-md-6">
            <input type="hidden" id="pages" name="pages">
            <label for="pages_visible" class="form-label">Total Pages:</label>
<p id="page-count-display" style="display: none;"><span id="page-count-value"></span></p>


            </div>
        </div>


        <button type="button" id="pay-btn" class="btn btn-primary w-100 mt-3">Proceed to Payment</button>
    </form>


                </div>
            </div>
            <div class="col-md-6">
                <div class="shop-info">
                    <h4 class="text-center">Shop Description</h4>
                    <p><strong>Shop Name:</strong> <span id="shop-name">Not Selected</span></p>
                    <p><strong>Shopkeeper:</strong> <span id="shopkeeper-name">-</span></p>
                    <p><strong>Address:</strong> <span id="shop-address">-</span></p>
                    <p><strong>Print Price:</strong> B/W ₹<span id="price-bw">-</span> | Colour ₹<span id="price-color">-</span></p>
                    <div id="map"></div>
                </div>
            </div>
        </div>
        </div>
        </div>
    </div>
                    </main>
                   
                    </div>
                </div>
            </div>
        </div>
        </div>


 
<!-- Include Razorpay Checkout Script -->
<script type="text/javascript" src="assets/js/jquery/jquery.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<!-- JSON Data for Shops -->
<script id="shop-data" type="application/json"><?= json_encode($shops); ?></script>
<script id="razorpay-key" type="application/json"><?= $razorpay_key; ?></script>
<!-- Include Leaflet Map -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script type="text/javascript" src="assets/js/jquery/jquery.min.js"></script>
    <!-- menu js -->
    <script src="assets/js/pcoded.min.js"></script>
    <script src="assets/js/vertical-layout.min.js "></script>
    <!-- custom js -->
    <script type="text/javascript" src="assets/pages/dashboard/custom-dashboard.js"></script>
    <script type="text/javascript" src="script.js "></script>
    <script type="text/javascript" src="assets/js/script.js "></script>
<script>
    document.getElementById('loader').style.display = 'flex'; // show
document.getElementById('loader').style.display = 'none'; // hide

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

            if (paymentIdInput) paymentIdInput.value = response.razorpay_payment_id;
            if (orderIdInput) orderIdInput.value = data.order_id;
            if (verificationCodeInput) verificationCodeInput.value = data.verification_code;

            // ✅ SHOW LOADER before submitting
            const loader = document.getElementById('loader');
            if (loader) loader.style.display = 'block';

            // ✅ OPTIONAL delay for smoother UX
            setTimeout(() => {
                document.getElementById('uploadForm').submit();
            }, 500);
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
