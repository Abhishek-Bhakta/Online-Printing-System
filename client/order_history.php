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
                        
                              <li class="active">
                                  <a href="index.html" class="waves-effect waves-dark">
                                      <span class="pcoded-micon"><i class="ti-package"></i><b>D</b></span>
                                      <span class="pcoded-mtext" data-i18n="nav.dash.main">Orders</span>
                                      <span class="pcoded-mcaret"></span>
                                  </a>
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
                     <div class="container">
    <h2>Orders</h2>

    <!-- Search Box -->
    <div class="search-container">
        <input type="text" id="search" placeholder="Search Orders..." onkeyup="searchOrders()">
    </div>

    <div id="document-list">
        <?php if ($documents): ?>
            <?php foreach ($documents as $doc): ?>
                <div class="document-card" 
                     data-search="<?= strtolower(htmlspecialchars($doc['file_name'] . ' ' . $doc['shop_name'] . ' ' . $doc['print_type'] . ' ' . $doc['order_status'] . ' ' . $doc['verification_code'])); ?>">
                    <div class="document-details">
                        <strong>Document:</strong> <?= htmlspecialchars($doc['file_name']); ?><br>
                        <strong>Shop:</strong> <?= htmlspecialchars($doc['shop_name']); ?><br>
                        <strong>Copies:</strong> <?= htmlspecialchars($doc['copies']); ?><br>
                        <strong>Size:</strong> <?= htmlspecialchars($doc['size']); ?><br>
                        <strong>Print Type:</strong> <?= ($doc['print_type'] === 'color') ? 'Color' : 'Black & White'; ?><br>
                        <strong>Verification Code:</strong> <span class="verification-code"><?= htmlspecialchars($doc['verification_code']); ?></span>
                    </div>
                    <div class="status-container">
                        <span class="status <?= strtolower($doc['order_status']); ?>">
                            <?= ucfirst($doc['order_status']); ?>
                        </span>
                        <span class="received-at">
                            <?= htmlspecialchars($doc['order_date']); ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #888;">No documents uploaded yet.</p>
        <?php endif; ?>
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


<style>
    /* Search Input Styling */
#search {
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
#search:focus {
    border-color: green;
    box-shadow: 0 0 6px rgba(0, 128, 0, 0.3);
}

/* Hover Effect */
#search:hover {
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
            text-align: center;
            color: #333;
            font-size: 25px;
            font-weight: bold;
            margin-bottom: 20px;
            margin-top: 20px;
        }

        /* Search Box */
        .search-container {
            position: absolute;
            top: 20px;
            right: 83px;
        }
        #search {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 14px;
            width: 200px;
            background: #fff;
        }

        /* Document Card */
        .document-card {
            background: #fff;
            padding: 18px;
            border-radius: 10px;
            box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.3s;
            border-left: 6px solid rgb(34, 131, 44);
        }
        .document-card:hover {
            box-shadow: 0px 5px 12px rgba(0, 0, 0, 0.15);
            transform: scale(1.02);
        }

        /* Left Details */
        .document-details {
            flex: 1;
            font-size: 14px;
            line-height: 1.7;
        }
        .document-details strong {
            color: #444;
        }
        .verification-code {
            color: #f2c71a;
            font-weight: bold;
        }

        /* Right Status Section */
        .status-container {
            text-align: right;
        }
        .status {
            font-weight: bold;
            padding: 6px 14px;
            border-radius: 6px;
            display: inline-block;
            font-size: 14px;
        }
        /* Status Colors */
        .status.printing {
            background-color: green;
            color: white;
        }
        .status.printed {
            background-color: orange;
            color: white;
        }
        
        /* Received At */
        .received-at {
            color: #555;
            font-size: 13px;
            display: block;
            margin-top: 6px;
        }</style>

<script>
        let originalOrders = []; // Store original list before filtering

// Store all orders on page load
window.onload = function () {
    let documentList = document.getElementById("document-list");
    let cards = Array.from(documentList.getElementsByClassName("document-card"));
    originalOrders = cards.map(card => card.cloneNode(true)); // Save original elements
};

function searchOrders() {
    let searchValue = document.getElementById("search").value.toLowerCase().trim();
    let documentList = document.getElementById("document-list");

    // Restore original order list before searching
    documentList.innerHTML = "";
    originalOrders.forEach(card => documentList.appendChild(card.cloneNode(true)));

    let cards = Array.from(documentList.getElementsByClassName("document-card"));
    let matchedCards = [];

    cards.forEach(card => {
        let data = card.innerText.toLowerCase(); // Only screen-visible text
        let matchScore = 0;

        searchValue.split(" ").forEach(word => {
            if (data.includes(word)) {
                matchScore++;
            }
        });

        if (matchScore > 0) {
            highlightMatches(card, searchValue); // Apply highlighting without affecting HTML
            matchedCards.push({ card, matchScore });
        } else {
            card.style.display = "none"; // Hide non-matching orders
        }
    });

    // Sort based on match score (higher matches first)
    matchedCards.sort((a, b) => b.matchScore - a.matchScore);

    // Show only matched cards in sorted order
    documentList.innerHTML = "";
    matchedCards.forEach(({ card }) => {
        card.style.display = "flex";
        documentList.appendChild(card);
    });

    // If nothing is found, show "No Results"
    if (matchedCards.length === 0) {
        documentList.innerHTML = "<p style='text-align: center; color: #888;'>No matching orders found.</p>";
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















