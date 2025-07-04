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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>
        <script src="js/script.js" ></script>
        <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>


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
               

                    <div class="container content">
    
    <div class="order-container">
    <h5 style="font-size: 40px; text-align:left; padding-top:10px;">Shop QR Code</h5>
    <div class="qr-wrapper">
    
    <div class="qr-card">
        <p class="shop-name"><?php echo htmlspecialchars($shop_name); ?></p>
        <div id="qrCode" class="qr-code-container">
            <!-- QR Code will be displayed here -->
        </div>
        <p class="username"><?php echo htmlspecialchars($username); ?></p>
    </div>
</div>
<div class="button-container">
        <button class="generate-qr-btn" onclick="generateQR()">Generate QR Code</button>
        <button class="download-qr-btn" id="downloadQR" style="display:none" onclick="downloadQRCode()">Download QR Code</button>
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
        <style>
          /* Wrapper for layout */
          .qr-wrapper {
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    justify-content: center;
    min-height: 70vh;
    font-family: 'Arial', sans-serif;
    position: relative; /* For absolute positioning of the title */
}

/* Styling for the title */
.qr-title {
    font-size: 36px;
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 40px;
    text-transform: uppercase;
    letter-spacing: 2px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: left;
    position: absolute;
    top: 20px;
    left: 0; /* Positioned directly on the left edge */
}

/* Card Styling */
.qr-card {
    width: 100%;
    max-width: 380px;
    padding: 35px;
    background-color: #ffffff;
    border-radius: 20px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: all 0.3s ease;
}

.qr-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
}

/* Shop Name Styling */
.shop-name {
    font-size: 24px;
    font-weight: 600;
    color: #2980b9;
    margin-bottom: 25px;
    font-family: 'Georgia', serif;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* QR Code Container Styling */
.qr-code-container {
    width: 260px;
    height: 260px;
    margin: 25px auto;
    border: 3px solid #e74c3c;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #ecf0f1;
    border-radius: 15px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

/* Media Queries for responsiveness */
@media (max-width: 768px) {
    .qr-wrapper {
        flex-direction: column;
        align-items: center;
    }

    .qr-title {
        position: static;
        font-size: 30px;
        margin-bottom: 20px;
        text-align: center;
    }

    .qr-card {
        margin-top: 20px;
        max-width: 90%;
        padding: 25px;
    }

    .shop-name {
        font-size: 20px;
    }

    .qr-code-container {
        width: 200px;
        height: 200px;
    }
}

@media (max-width: 480px) {
    .qr-title {
        font-size: 24px;
    }

    .qr-card {
        max-width: 95%;
        padding: 20px;
    }

    .shop-name {
        font-size: 18px;
    }

    .qr-code-container {
        width: 180px;
        height: 180px;
    }
}

/* Username Styling */
.username {
    font-size: 24px;
    font-weight: 600;
    color: #2980b9;
    margin-top:40px;
    font-family: 'Georgia', serif;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Button Container */
.button-container {
    display: flex;
    justify-content: center;
    gap: 25px;
    margin-top: 25px;
}

/* General Button Styling */
button {
    background-color: #3498db;
    color: white;
    border: none;
    padding: 16px 32px;
    font-size: 18px;
    cursor: pointer;
    border-radius: 10px;
    transition: all 0.3s ease;
    font-family: 'Arial', sans-serif;
}

button:hover {
    background-color: #2980b9;
    transform: scale(1.05);
}

/* Styling for the "Generate QR Code" button */
.generate-qr-btn {
    background-color: #27ae60;
}

.generate-qr-btn:hover {
    background-color: #2ecc71;
}

/* Styling for the "Download QR Code" button */
.download-qr-btn {
    background-color: #f39c12;
}

.download-qr-btn:hover {
    background-color: #e67e22;
}

/* Active state for buttons */
button:active {
    transform: scale(0.98);
}

        </style>
    </body>
</html>