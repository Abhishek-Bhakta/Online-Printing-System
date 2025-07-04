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
<!-- Leaflet CSS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet.fullscreen/dist/leaflet.fullscreen.css" />
    
    <!-- Include Leaflet JS and Fullscreen Plugin -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.fullscreen/dist/leaflet.fullscreen.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

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
                            
                            <a class="nav-link collapsed" href="qr.php" >
                                <div class="sb-nav-link-icon"><i class="fas fa-qrcode"></i></div>
                                QR
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <a class="nav-link collapsed" href="setting.php">
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
                <h2 style="margin-bottom:20px;">Shop Settings</h2>

    <form method="POST" action="">
        <fieldset>
            <legend>Shop Details</legend>
            <label for="shop_name">Shop Name:</label>
            <input type="text" name="shop_name" value="<?php echo htmlspecialchars($shop['shop_name']); ?>" required><br><br>

            <label for="shop_address">Shop Address:</label>
            <textarea name="shop_address" required><?php echo htmlspecialchars($shop['shop_address']); ?></textarea><br><br>

            <label for="latitude">Latitude:</label>
            <input type="text" name="latitude" id="latitude" value="<?php echo htmlspecialchars($shop['latitude']); ?>" required readonly><br><br>

            <label for="longitude">Longitude:</label>
            <input type="text" name="longitude" id="longitude" value="<?php echo htmlspecialchars($shop['longitude']); ?>" required readonly><br><br>
            <div id="map-container" style="width: 100%; height: 300px; margin-bottom: 20px;"></div>

            <label for="price_bw">Price for Black and White:</label>
            <input type="number" name="price_bw" step="0.01" value="<?php echo htmlspecialchars($shop['price_bw']); ?>" required><br><br>

            <label for="price_color">Price for Color:</label>
            <input type="number" name="price_color" step="0.01" value="<?php echo htmlspecialchars($shop['price_color']); ?>" required><br><br>

            <button class="bnt" type="submit" name="update_shop_details">Update Shop Details</button>
        </fieldset>
    </form>
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
        <style>
/* Reset some default styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Container for the page (Card Styling) */
.container {
    max-width: 1000px; /* Increased card width */
    margin: 0 auto;
    background: #fff;
    border-radius: 16px; /* Larger border radius for a more card-like effect */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); /* Stronger shadow for a prominent look */
    padding: 60px; /* Increased padding for more space */
}

/* Header Styling */

/* Form Styling */
form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

fieldset {
    border: none;
    padding: 50px; /* Increased padding for the fieldsets */
    background: #f9f9f9;
    border-radius: 8px;
    box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1);
    width: 65%; /* Adjust this value to set the desired width */
    margin: 0 auto; /* Centers the fieldset horizontally */
    height:50%;
}

legend {
    font-size: 24px; /* Larger legend text */
    font-weight: bold;
    color:rgb(17, 110, 31);
    margin-bottom: 25px; /* More space between legend and inputs */
}

/* Label Styling */
label {
    font-size: 16px; /* Larger label font */
    color: #555;
    font-weight: 600;
    margin-bottom: 8px;
}
/* Label Styling */
label {
    font-size: 16px; /* Larger label font */
    color: #555;
    font-weight: 600;
    margin-bottom: 4px; /* Reduced space between label and input */
}

/* Input and Textarea Styling */
input[type="text"], input[type="number"], textarea {
    width: 100%;
    padding: 14px; /* Slightly larger input padding */
    border-radius: 8px; /* More rounded corners */
    border: none; /* Remove default border */
    font-size: 16px; /* Slightly larger font size */
    color: #333;
    background-color: #f9f9f9;
    transition: all 0.3s ease;
    box-shadow: 0 0 0 0.5px #000; /* Black border using box-shadow */
}
/* Map Box Styling */
#map-box {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 350px;
    height: 350px;
    z-index: 9999;
    display: none;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 0 0.5px #000, 0 0 5px rgba(0, 0, 0, 0.5); /* Thinner black border using box-shadow */
    transition: all 0.3s ease;
}


/* Button Styling */
.bnt {
    padding: 14px 30px; /* Larger button padding */
    background-color: rgb(17, 110, 31);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 18px; /* Larger font size */
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: fit-content;
    margin: 15px auto; /* More space between button and other elements */
}

.bnt:hover {
    background-color:rgb(0, 179, 92);
}

.bnt:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}
/* Map Box Styling */
#map-box {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 350px;
    height: 350px;
    z-index: 9999;
    display: none;
    background: #fff;
    border: 2px solid #28a745; /* Green border */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    transition: all 0.3s ease;
}

/* Draggable Handle Styling */
#drag-handle {
    width: 100%;
    height: 35px;
    background: #007bff;
    color: white;
    text-align: center;
    line-height: 35px;
    font-weight: bold;
    cursor: grab;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    font-size: 14px;
    user-select: none;
}

#drag-handle:hover {
    background: #0056b3;
}

/* Map Container */
#map-container {
    width: 100%;
    height: 100%;
    border-radius: 8px;
}

/* Add spacing between inputs and labels */
input, textarea {
    margin-bottom: 20px; /* More space between inputs and labels */
}

/* Media Query for Small Screens */
@media (max-width: 768px) {
    .container {
        padding: 20px;
    }

    h2 {
        font-size: 26px;
    }

    button {
        width: 100%;
        padding: 16px;
    }

    #map-box {
        width: 90%;
        height: 300px;
    }
}

    </style>
    </body>
</html>