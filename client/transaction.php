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
$stmt = $pdo->prepare("SELECT t.transaction_id, t.payment_id, t.amount, t.currency, t.payment_status, t.transaction_date, s.shop_name ,t.order_id
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
                              <li>
                                  <a href="upload.php" class="waves-effect waves-dark">
                                      <span class="pcoded-micon"><i class="ti-upload"></i><b>FC</b></span>
                                      <span class="pcoded-mtext" data-i18n="nav.form-components.main">Upload Documents</span>
                                      <span class="pcoded-mcaret"></span>
                                  </a>
                              </li>
                        
                              <li>
                                  <a href="order_history.php" class="waves-effect waves-dark">
                                      <span class="pcoded-micon"><i class="ti-package"></i><b>D</b></span>
                                      <span class="pcoded-mtext" data-i18n="nav.dash.main">Orders</span>
                                      <span class="pcoded-mcaret"></span>
                                  </a>
                          </ul>
        
                          <div class="pcoded-navigation-label" data-i18n="nav.category.forms">Chart &amp; Maps</div>
                          <ul class="pcoded-item pcoded-left-item">
                              <li class="active">
                                  <a href="transaction.php" class="waves-effect waves-dark">
                                      <span class="pcoded-micon"><i class="ti-receipt"></i><b>FC</b></span>
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
                     <h2>Your Transaction History</h2>
                     <div class="search-container">
    <input type="text" id="searchInput" placeholder="Search transactions..." onkeyup="searchTransactions()">
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Transaction ID</th>
                <th>Shop</th>
                <th>Payment ID</th>
                <th>Amount</th>
                <th>Currency</th>
                <th>Payment Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody id="transactionTable">
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td>#<?= htmlspecialchars($transaction['order_id']); ?></td>
                    <td><?= htmlspecialchars($transaction['transaction_id']); ?></td>
                    <td><?= htmlspecialchars($transaction['shop_name']); ?></td>
                    <td><?= htmlspecialchars($transaction['payment_id']); ?></td>
                    <td>₹<?= htmlspecialchars($transaction['amount']); ?></td>
                    <td><?= htmlspecialchars($transaction['currency']); ?></td>
                    <td style="color: <?= $transaction['payment_status'] === 'Success' ? 'green' : ($transaction['payment_status'] === 'Failed' ? 'red' : 'orange'); ?>">
                        <?= ucfirst($transaction['payment_status']); ?>
                    </td>
                    <td><?= htmlspecialchars($transaction['transaction_date']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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


<style>
    /* Search Input Styling */
#searchInput {
    width: 250px;
    padding: 8px 12px;
    font-size: 14px;
    border: 2px solid #ddd;
    border-radius: 6px;
    outline: none;
    transition: all 0.3s ease-in-out;
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-left: 20px; /* Left side se andar le aayega */
    display: block;
}

/* Input Focus Effect */
#searchInput:focus {
    border-color: green;
    box-shadow: 0 0 6px rgba(0, 128, 0, 0.3);
}

/* Hover Effect */
#searchInput:hover {
    border-color: #bbb;
}

/* Mobile Responsive */
@media (max-width: 480px) {
    #searchInput {
        width: 220px;
        font-size: 13px;
        margin-left: 10px; /* Mobile pe thoda aur andar */
    }
}

h2 {
    padding-top: 20px;
    text-align: center;
}

/* Table Container */
.table-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    overflow-x: auto;
}

/* Scrollable Table */
.table-wrapper {
    max-height: 450px;
    overflow-y: auto;
    overflow-x: auto;
    border: 2px solid #ddd;
    border-radius: 10px;
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    background: #ffffff;
    overflow: hidden;
    display: table;
}

/* Table Header */
thead {
    position: sticky;
    top: 0;
    background: rgb(24, 143, 37);
    z-index: 2;
}

th {
    background: rgb(29, 130, 43);
    color: #ffffff;
    font-size: 16px;
    font-weight: bold;
    text-transform: uppercase;
    padding: 15px;
    border-bottom: 2px solid rgb(24, 107, 28);
    white-space: nowrap;
}

/* Table Body Rows */
td {
    padding: 15px;
    text-align: center;
    color: #333;
    font-size: 15px;
    border-bottom: 1px solid #ddd;
    white-space: nowrap;
}

/* Amount Column Highlight */
td:nth-child(5) {
    font-weight: bold;
    color: #007bff;
}

/* Status Color */
.status {
    font-weight: bold;
    color: #444;
}

/* 🔥 Search Result Highlight */
.highlight {
    background-color: yellow !important;
    font-weight: bold;
    color: black !important;
    padding: 2px 5px;
    border-radius: 4px;
}

/* 🔥 Matched Rows Effect */
tbody tr.matched {
    background: rgb(255, 249, 196);
    box-shadow: 0px 0px 5px rgba(255, 230, 0, 0.5);
}

/* Hover Effect */
tbody tr:hover {
    background: rgb(233, 241, 234);
}

/* Empty Rows */
.empty-row {
    height: 45px;
    background: rgb(255, 255, 255);
}

/* No Data Message */
.no-data {
    text-align: center;
    font-size: 18px;
    color: #555;
    margin-top: 20px;
}

/* Scrollbar Customization */
.table-wrapper::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.table-wrapper::-webkit-scrollbar-thumb {
    background: #aaa;
    border-radius: 4px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .table-container {
        margin: 10px;
        padding: 10px;
    }

    th, td {
        padding: 8px;
    }
}


</style>

<script>
 let originalRows = [];

// Store all rows on page load
window.onload = function () {
    let tbody = document.getElementById("transactionTable");
    let rows = Array.from(tbody.getElementsByTagName("tr"));
    originalRows = rows.map(row => row.cloneNode(true)); // Save original rows
};

function searchTransactions() {
    let searchValue = document.getElementById("searchInput").value.toLowerCase().trim();
    let tbody = document.getElementById("transactionTable");

    // Reset table before searching
    tbody.innerHTML = "";
    originalRows.forEach(row => tbody.appendChild(row.cloneNode(true)));

    let rows = Array.from(tbody.getElementsByTagName("tr"));
    let matchedRows = [];

    rows.forEach(row => {
        let rowData = row.innerText.toLowerCase(); // Only visible text in row
        let matchScore = 0;

        searchValue.split(" ").forEach(word => {
            if (rowData.includes(word)) {
                matchScore++;
            }
        });

        if (matchScore > 0) {
            highlightMatches(row, searchValue); // Apply highlighting without affecting HTML structure
            matchedRows.push({ row, matchScore });
        } else {
            row.style.display = "none"; // Hide non-matching rows
        }
    });

    // Sort based on match score (higher matches first)
    matchedRows.sort((a, b) => b.matchScore - a.matchScore);

    // Show only matched rows in sorted order
    tbody.innerHTML = "";
    matchedRows.forEach(({ row }) => {
        row.style.display = "table-row";
        tbody.appendChild(row);
    });

    // If nothing is found, show "No Results"
    if (matchedRows.length === 0) {
        tbody.innerHTML = "<tr><td colspan='8' style='text-align: center; color: #888;'>No matching transactions found.</td></tr>";
    }
}

// Function to highlight matched words only in visible text
function highlightMatches(element, searchValue) {
    if (!searchValue) return;
    let words = searchValue.split(" ").filter(w => w.length > 0);

    function wrapMatches(node) {
        if (node.nodeType === 3) { // Process only text nodes
            let text = node.nodeValue;
            let newText = text;

            words.forEach(word => {
                let regex = new RegExp(`(${word})`, "gi");
                newText = newText.replace(regex, "<span style='background-color: yellow; font-weight: bold;'>$1</span>");
            });

            if (newText !== text) {
                let span = document.createElement("span");
                span.innerHTML = newText;
                node.replaceWith(span);
            }
        } else if (node.nodeType === 1 && node.childNodes) { // Process child nodes
            Array.from(node.childNodes).forEach(wrapMatches);
        }
    }

    wrapMatches(element);
}

</script>

    </body>

</html>















