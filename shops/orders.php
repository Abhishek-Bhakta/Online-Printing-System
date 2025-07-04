<?php
include 'features/fetch.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard - Shopkeeper</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <link href="css/style.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/script.js" ></script>


    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark " style="background-color:rgb(255, 255, 255);">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="dashboard.php"style="background-color:#4CAF50; color:white; padding-top:15px; padding-bottom:15px; padding-left:50px;">Printify</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars" style="color:#4CAF50;"></i></button>
            <h2 style="color:#004526;"><?php echo htmlspecialchars($shop_name); ?></h2>
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown" >
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color:#4CAF50;"><i class="fas fa-user fa-fw" style="color:#4CAF50;"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="profile.php">Settings</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav" >
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu" style="background-color:white;">
                        <div class="nav" >
          

                            <a class="nav-link" href="index.php" style="color: #4D5D53; font-weight: bold;" >
                                <div class="sb-nav-link-icon" style="color:#8F9779;"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <div class="status" style="color:#004953; margin-top: 5px;margin-bottom:25px;font-weight:bold;">
                            <span style="margin-left: 15px; margin-right: 5px;">Status:</span>  
                            <label class="toggle-switch">
                            <input type="checkbox" id="statusToggle" onchange="toggleStatus()" <?= ($shop_status === 'online') ? 'checked' : ''; ?> />
                            <span class="slider"></span>
                            </label>
                            <span id="shop_status"><?= ucfirst($shop_status); ?></span>
                            </div>
                            <div class="sb-sidenav-menu-heading"style="background-color:#4CAF50; color:white;  padding-top:8px; padding-bottom:8px;" >Actions</div>
                            <a class="nav-link collapsed" href="orders.php" >
                                <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                                Orders
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            
                            <a class="nav-link collapsed" href="qr.php" >
                                <div class="sb-nav-link-icon"><i class="fas fa-qrcode"></i></div>
                                QR
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <a class="nav-link collapsed" href="setting.php" >
                                <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                                Settings
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="sb-sidenav-menu-heading" style="background-color:#4CAF50; color:white; padding-top:10px; padding-bottom:10px;" >Views</div>
                            <a class="nav-link" href="order_history.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-history"></i></div>
                                Orders History
                            </a>
                            <a class="nav-link" href="transaction_history.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-receipt"></i></div>
                                Transaction History
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer" style="background-color:#4CAF50; color:white;">
                        <div class="small">Logged in as:</div>
                        Shopkeeper 
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content"style="background-color:rgba(224, 235, 219, 0.83);">
                <main>
                <div class="container-fluid px-4">
               <h4 style="font-size: 45px;">Orders</h4>

                    <div class="container content">
                    <input type="text" id="searchInput" placeholder="Search orders...." class="form-control" onkeyup="searchTransactions()" style="width: 300px; margin-bottom: 15px;">
                    
    <div class="order-container">
    <div class="order-page">
    <?php if (count($orders) > 0): ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-info">
                    <p><strong>Document:</strong> 
                        <a href="uploads/<?php echo htmlspecialchars($shop_name); ?>/<?php echo htmlspecialchars($order['file_name']); ?>" target="_blank">
                            <?php echo htmlspecialchars($order['file_name']); ?>
                        </a>
                    </p>
                    <p><strong>Copies:</strong> <?php echo htmlspecialchars($order['copies'] ?? 'N/A'); ?></p>
                    <p><strong>Size:</strong> <?php echo htmlspecialchars($order['size'] ?? 'N/A'); ?></p>
                    <p><strong>Print Type:</strong> <?php echo ucfirst($order['print_type']); ?></p>
                    <p><strong>Verification Code:</strong> <span class="verif-code"><?php echo htmlspecialchars($order['verification_code'] ?? 'N/A'); ?></span></p>
                    <p><strong>Status:</strong> 
                        <span id="status_<?php echo $order['document_id']; ?>" 
                              class="status 
                              <?php echo $order['order_status'] === 'printed' ? 'status-printed' : 
                                            ($order['order_status'] === 'printing' ? 'status-printing' : 'status-pending'); ?>">
                            <?php echo ucfirst($order['order_status']); ?>
                        </span>
                    </p>
                </div>
                <div>
                    <button class="btn btn-warning btn-sm btn-custom" onclick="updateStatus(<?php echo $order['document_id']; ?>, 'printing')">Printing</button>
                    <button class="btn btn-success btn-sm btn-custom" onclick="updateStatus(<?php echo $order['document_id']; ?>, 'printed')">Printed</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center text-danger fw-bold">No documents uploaded yet.</p>
    <?php endif; ?>
</div>

    </div>
</div>

                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2023</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
    let originalOrders = [];

    // Store all order cards on page load
    function storeOriginalOrders() {
        let orderContainer = document.querySelector(".order-page");
        let orders = Array.from(orderContainer.getElementsByClassName("order-card"));
        originalOrders = orders.map(order => order.cloneNode(true)); // Save original order cards
    }

    storeOriginalOrders();

    document.getElementById("searchInput").addEventListener("input", searchOrders);

    function searchOrders() {
        let searchValue = document.getElementById("searchInput").value.toLowerCase().trim();
        let orderContainer = document.querySelector(".order-page");

        // Reset orders before searching
        orderContainer.innerHTML = "";
        originalOrders.forEach(order => orderContainer.appendChild(order.cloneNode(true)));

        let orders = Array.from(orderContainer.getElementsByClassName("order-card"));
        let matchedOrders = [];

        orders.forEach(order => {
            let orderText = order.innerText.toLowerCase(); // Only visible text in order card
            let matchScore = 0;

            searchValue.split(" ").forEach(word => {
                if (orderText.includes(word)) {
                    matchScore++;
                }
            });

            if (matchScore > 0) {
                highlightMatches(order, searchValue);
                matchedOrders.push({ order, matchScore });
            } else {
                order.style.display = "none"; // Hide non-matching orders
            }
        });

        // Sort based on match score (higher matches first)
        matchedOrders.sort((a, b) => b.matchScore - a.matchScore);

        // Show only matched orders in sorted order
        orderContainer.innerHTML = "";
        matchedOrders.forEach(({ order }) => {
            order.style.display = "block";
            orderContainer.appendChild(order);
        });

        // If nothing is found, show "No matching orders found."
        if (matchedOrders.length === 0) {
            orderContainer.innerHTML = "<p class='text-center text-danger fw-bold'>No matching orders found.</p>";
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
});

        </script>
    </body>
</html>