<?php

session_start();
include 'config.php';

if (!isset($_SESSION['shopkeeper_id'])) {
    header("Location: login.php");
    exit();
}

$shopkeeper_id = $_SESSION['shopkeeper_id'];

// Fetch shop details
$stmt = $conn->prepare("SELECT shop_id, shop_name, status FROM shops WHERE shopkeeper_id = ?");
$stmt->execute([$shopkeeper_id]);
$shop = $stmt->fetch(PDO::FETCH_ASSOC);
$shop_id = $shop['shop_id'];
$shop_name = $shop['shop_name'];
$shop_status = isset($shop['status']) ? $shop['status'] : 'offline';

// Fetch current shopkeeper details
$stmt = $conn->prepare("SELECT shopkeeper_name, email, phone, username FROM shopkeepers WHERE shopkeeper_id = ?");
$stmt->execute([$shopkeeper_id]);
$shopkeeper = $stmt->fetch(PDO::FETCH_ASSOC);

// Flag to display success message
$update_success = false;

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Personal Info Update
    if (isset($_POST['update_personal_info'])) {
        $shopkeeper_name = $_POST['shopkeeper_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        // Update shopkeeper personal info
        $stmt = $conn->prepare("UPDATE shopkeepers SET shopkeeper_name = ?, email = ?, phone = ? WHERE shopkeeper_id = ?");
        $stmt->execute([$shopkeeper_name, $email, $phone, $shopkeeper_id]);


        // Add other related tables if needed
        $update_success = true; // Set success flag
    }

    // Security Credentials Update
    if (isset($_POST['update_security_credentials'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Ensure password is not empty
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Update shopkeeper security credentials
            $stmt = $conn->prepare("UPDATE shopkeepers SET username = ?, password = ? WHERE shopkeeper_id = ?");
            $stmt->execute([$username, $hashed_password, $shopkeeper_id]);


            $update_success = true; // Set success flag
        }
    }

    // Refresh page after update
    if ($update_success) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
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
                        <li><a class="dropdown-item" href="#!">Settings</a></li>
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
                <h2 style="margin-bottom:50px; margin-top:20px;">Update Shopkeeper Profile</h2>
                <div class="container">
        

        <!-- Personal Info Section -->
        <form method="POST" action="">
            <fieldset>
                <legend>Personal Info</legend>
                <label for="shopkeeper_name">Name:</label>
                <input type="text" name="shopkeeper_name" value="<?php echo htmlspecialchars($shopkeeper['shopkeeper_name']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($shopkeeper['email']); ?>" required>

                <label for="phone">Phone Number:</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($shopkeeper['phone']); ?>">

                <button class="bnt" type="submit" name="update_personal_info">Update Personal Info</button>
            </fieldset>
        </form>

        <hr>

        <!-- Security Credentials Section -->
        <form method="POST" action="">
            <fieldset>
                <legend>Security Credentials</legend>
                <label for="username">Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($shopkeeper['username']); ?>" required>

                <label for="password">Password:</label>
                <input type="password" name="password" required>

                <button class="bnt" type="submit" name="update_security_credentials">Update Security Credentials</button>
            </fieldset>
        </form>
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
        .container {
    display: flex;
    
    justify-content: space-between;
    gap: 20px;
}

.container form {
    width: 48%;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            margin-bottom: 20px;
        }

        fieldset {
            border: none;
            background: #fafafa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        legend {
            font-size: 18px;
            font-weight: bold;
            color:rgb(36, 142, 56);
        }

        label {
            font-size: 14px;
            font-weight: bold;
            color: #555;
            display: block;
            margin: 10px 0 5px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .bnt {
            background: rgb(36, 142, 56);
            color: white;
            border: none;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
        }

        .bnt:hover {
            background:rgb(67, 199, 89);
        }

        hr {
            border: 0;
            height: 1px;
            background: #ccc;
            margin: 20px 0;
        }
    </style>

    </body>
</html>