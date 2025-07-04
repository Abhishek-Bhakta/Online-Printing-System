<?php
session_start();
include 'config.php';

if (!isset($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit();
}
$client_id = $_SESSION['client_id'];  // Ensure $client_id is initialized before using it

// Fetch all shops from the database
try {
    $stmt = $pdo->prepare("SELECT shop_id, shop_name, shop_address, shopkeeper_name, status, latitude, longitude, price_bw, price_color FROM shops");
    $stmt->execute();
    $shops = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
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
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    
    <style>
        #map { height: 550px; width: 100%; }
        .leaflet-popup-content { font-size: 14px; }
    </style>
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
                          <div class="">
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
                          </div>
                          <div class="p-15 p-b-0">
                              <form class="form-material">
                                  <div class="form-group form-primary">
                          </div>
                          
                          <ul class="pcoded-item pcoded-left-item">
                              <li>
                                  <a href="index.html" class="waves-effect waves-dark">
                                      <span class="pcoded-micon"><i class="ti-home"></i><b>D</b></span>
                                      <span class="pcoded-mtext" data-i18n="nav.dash.main">Dashboard</span>
                                      <span class="pcoded-mcaret"></span>
                                  </a>
                              </li>
                                      
                                  </ul>
                              </li>
                          </ul>
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
                              </li>
        
                          </ul>
        
                          </ul>
                      </div>
                  </nav>
                  <div class="pcoded-content">
                      <!-- Page-header start -->
                      
                     <main>

                     <h2>Find Nearby Print Shops</h2>
<div id="map"></div>
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
    var map = L.map('map').setView([19.1919600, 72.8431900], 13);

    // Add OpenStreetMap Tile Layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var userMarker, selectedShopMarker, routeLine;
    var shopMarkers = [];

    // Custom Icons
    var greenIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/149/149060.png', 
        iconSize: [35, 35]
    });

    var redIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/484/484167.png', 
        iconSize: [40, 40]
    });

    var blueIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png', 
        iconSize: [35, 35]
    });

    // Shop Data from PHP to JavaScript
    var shops = <?php echo json_encode($shops); ?>;

    // Display Shops on Map with Default Blue Pins
    shops.forEach(shop => {
        var marker = L.marker([shop.latitude, shop.longitude], {icon: blueIcon})
            .addTo(map)
            .bindPopup(`
                <b>${shop.shop_name}</b><br>
                Address: ${shop.shop_address}<br>
                Owner: ${shop.shopkeeper_name}<br>
                Status: ${shop.status}<br>
                B/W Price: ₹${shop.price_bw}, Color Price: ₹${shop.price_color}<br>
                <button onclick="getDirections(${shop.latitude}, ${shop.longitude}, '${shop.shop_name}')">Get Directions</button>
            `);
        shopMarkers.push(marker);
    });

    function getDirections(shopLat, shopLng, shopName) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                var userLat = position.coords.latitude;
                var userLng = position.coords.longitude;

                // Remove previous markers & route instantly
                if (userMarker) map.removeLayer(userMarker);
                if (routeLine) map.removeLayer(routeLine);
                if (selectedShopMarker) map.removeLayer(selectedShopMarker);
                
                // Reset all shops to blue pins
                shopMarkers.forEach(marker => marker.setIcon(blueIcon));

                // User's Live Location Marker (Green)
                userMarker = L.marker([userLat, userLng], {icon: greenIcon})
                    .addTo(map)
                    .bindPopup("<b>Start Here 🟢</b>")
                    .openPopup();

                // Destination Shop Marker (Red)
                selectedShopMarker = L.marker([shopLat, shopLng], {icon: redIcon})
                    .addTo(map)
                    .bindPopup("<b>Destination: " + shopName + " 🔴</b>")
                    .openPopup();

                // Fetch Route using OpenRouteService (Super Fast API)
                var routeUrl = `https://router.project-osrm.org/route/v1/driving/${userLng},${userLat};${shopLng},${shopLat}?overview=full&geometries=geojson`;

                fetch(routeUrl)
                    .then(response => response.json())
                    .then(data => {
                        var routeCoordinates = data.routes[0].geometry.coordinates.map(coord => [coord[1], coord[0]]);
                        
                        // Remove previous route immediately before adding a new one
                        if (routeLine) map.removeLayer(routeLine);

                        // Draw Fast Route
                        routeLine = L.polyline(routeCoordinates, {color: 'blue', weight: 5}).addTo(map);
                        map.fitBounds(routeLine.getBounds());
                    });

            }, () => alert("Location access denied!"));
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }
</script>
    </body>

</html>

