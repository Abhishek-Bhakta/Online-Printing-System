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
          

                            <a class="nav-link" href="dashboard.php" style="color: #4D5D53; font-weight: bold;" >
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
                            
                            <a class="nav-link collapsed" href="#" >
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
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Welcome, <?php echo htmlspecialchars($username); ?> </li>
                        </ol>

                        

        <div class="stats-container">
            <div class="stat-box">
                <h3>Total Orders</h3>
                <p><?php echo $total_orders; ?></p>
            </div>
            <div class="stat-box">
                <h3>Today's Orders</h3>
                <p><?php echo $today_orders; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Earnings</h3>
                <p>₹<?php echo number_format($total_earnings, 2); ?></p>
            </div>
            <div class="stat-box">
                <h3>Today's Earnings</h3>
                <p>₹<?php echo number_format($today_earnings, 2); ?></p>
            </div>
</div>    
    <h2>📊 Sales Analytics</h2>
    <div class="section charts-container">
        <div class="chart-box"><canvas id="monthlySalesChart"></canvas></div>
        <div class="chart-box"><canvas id="yearlySalesChart"></canvas></div>
        <div class="chart-box"><canvas id="shopSalesChart"></canvas></div>
        <div class="chart-box"><canvas id="dailySalesChart"></canvas></div>
    </div>

    <h2>📈 User Growth & Engagement</h2>
    <div class="section charts-container">
        <div class="chart-box"><canvas id="newReturningUsersChart"></canvas></div>
        <div class="chart-box"><canvas id="totalUsersChart"></canvas></div>
        <div class="chart-box"><canvas id="activeUsersChart"></canvas></div>
        <div class="chart-box"><canvas id="shopRetentionChart"></canvas></div>
    </div>

    <h2>🏪 Shop Performance</h2>
    <div class="section charts-container">
        <div class="chart-box"><canvas id="totalOrdersChart"></canvas></div>
        <div class="chart-box"><canvas id="orderStatusChart"></canvas></div>
    </div>

    <h2>💰 Transaction Analytics</h2>
    <div class="section charts-container">
        <div class="chart-box"><canvas id="totalRevenueChart"></canvas></div>
        <div class="chart-box"><canvas id="paymentMethodsChart"></canvas></div>
    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted"></div>
                            <div>
                                
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>