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
               <h4 style="font-size: 45px;">Orders History</h4>
               <input type="text" id="searchHistoryInput" class="form-control" placeholder="Search Orders History..." style="width: 300px; margin-bottom: 15px;">

                    <div class="container content">
                    <div class="order-history-page">
    <?php if (count($order_history) > 0): ?>
        <table class="order-table">
            <thead>
                <tr>
                    <th>Order Id</th>
                    <th>Client Name</th>
                    <th>Document</th>
                    <th>Copies</th>
                    <th>Size</th>
                    <th>Print Type</th>
                    <th>Verification Code</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_history as $order): ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['client_name'] ?? 'Unknown'); ?></td>
                        <td><a href="uploads/<?php echo htmlspecialchars($shop_name); ?>/<?php echo htmlspecialchars($order['file_name']); ?>" target="_blank"><?php echo htmlspecialchars($order['file_name']); ?></a></td>
                        <td><?php echo htmlspecialchars($order['copies'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($order['size'] ?? 'N/A'); ?></td>
                        <td><?php echo $order['print_type'] === 'color' ? 'Color' : 'Black & White'; ?></td>
                        <td><?php echo htmlspecialchars($order['verification_code'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No order history found.</p>
    <?php endif; ?>
</div>
    </div>
</div>

                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
    let originalRows = [];

    // Store all table rows on page load
    function storeOriginalRows() {
        let tableBody = document.querySelector(".order-history-page tbody");
        let rows = Array.from(tableBody.getElementsByTagName("tr"));
        originalRows = rows.map(row => row.cloneNode(true)); // Save original rows
    }

    storeOriginalRows();

    document.getElementById("searchHistoryInput").addEventListener("input", searchOrdersHistory);

    function searchOrdersHistory() {
        let searchValue = document.getElementById("searchHistoryInput").value.toLowerCase().trim();
        let tableBody = document.querySelector(".order-history-page tbody");

        // Reset table before searching
        tableBody.innerHTML = "";
        originalRows.forEach(row => tableBody.appendChild(row.cloneNode(true)));

        let rows = Array.from(tableBody.getElementsByTagName("tr"));
        let matchedRows = [];

        rows.forEach(row => {
            let rowText = row.innerText.toLowerCase(); // Only visible text in row
            let matchScore = 0;

            searchValue.split(" ").forEach(word => {
                if (rowText.includes(word)) {
                    matchScore++;
                }
            });

            if (matchScore > 0) {
                highlightMatches(row, searchValue);
                matchedRows.push({ row, matchScore });
            } else {
                row.style.display = "none"; // Hide non-matching rows
            }
        });

        // Sort based on match score (higher matches first)
        matchedRows.sort((a, b) => b.matchScore - a.matchScore);

        // Show only matched rows in sorted order
        tableBody.innerHTML = "";
        matchedRows.forEach(({ row }) => {
            row.style.display = "table-row";
            tableBody.appendChild(row);
        });

        // If nothing is found, show "No matching orders found."
        if (matchedRows.length === 0) {
            tableBody.innerHTML = "<tr><td colspan='8' class='text-center text-danger fw-bold'>No matching orders found.</td></tr>";
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