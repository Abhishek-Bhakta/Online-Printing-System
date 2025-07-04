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
                              <li class="active">
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
                              <li>
                                  <a href="upload.php" class="waves-effect waves-dark">
                                      <span class="pcoded-micon"><i class="ti-layers"></i><b>FC</b></span>
                                      <span class="pcoded-mtext" data-i18n="nav.form-components.main">Upload Documents</span>
                                      <span class="pcoded-mcaret"></span>
                                  </a>
                              </li>
                              <li>
                                  <a href="order_history.php" class="waves-effect waves-dark">
                                      <span class="pcoded-micon"><i class="ti-layers"></i><b>FC</b></span>
                                      <span class="pcoded-mtext" data-i18n="nav.form-components.main">Order History</span>
                                      <span class="pcoded-mcaret"></span>
                                  </a>
                              </li>
        
                          </ul>
        
                          <div class="pcoded-navigation-label" data-i18n="nav.category.forms">Chart &amp; Maps</div>
                          <ul class="pcoded-item pcoded-left-item">
                              <li>
                                  <a href="chart.html" class="waves-effect waves-dark">
                                      <span class="pcoded-micon"><i class="ti-layers"></i><b>FC</b></span>
                                      <span class="pcoded-mtext" data-i18n="nav.form-components.main">Chart</span>
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
                      <div class="page-header">
                          <div class="page-block">
                              <div class="row align-items-center">
                                  <div class="col-md-8">
                                      <div class="page-header-title">
                                          <h5 class="m-b-10">Dashboard</h5>
                                          <p class="m-b-0">Welcome, <?= $client_name; ?>!</p>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                     <main>

                     <div class="container mt-5">
        <h2 class="text-center text-primary">Upload Document & Shop Details</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="form-section">
                    <form id="uploadForm" action="client_upload.php" method="POST" enctype="multipart/form-data">
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

                        <label for="color" class="form-label">Print Type:</label>
                        <select id="color" name="color" class="form-select" required>
                            <option value="color">Color</option>
                            <option value="bw">Black & White</option>
                        </select>

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

                    </main>
                   
                    </div>
                </div>
            </div>
        </div>
    </div>
   





<!-- Include Razorpay Checkout Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<!-- JSON Data for Shops -->
<script id="shop-data" type="application/json"><?= json_encode($shops); ?></script>
<script id="razorpay-key" type="application/json"><?= $razorpay_key; ?></script>
<!-- Include Leaflet Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery/jquery.min.js"></script>
    <!-- menu js -->
    <script src="assets/js/pcoded.min.js"></script>
    <script src="assets/js/vertical-layout.min.js "></script>
    <!-- custom js -->
    <script type="text/javascript" src="assets/pages/dashboard/custom-dashboard.js"></script>
    <script type="text/javascript" src="script.js "></script>



    </body>

</html>
