<?php
include 'config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Get and sanitize user inputs
        $shopkeeper_name = htmlspecialchars($_POST['shopkeeper_name']);
        $email = htmlspecialchars($_POST['email']);
        $phone = !empty($_POST['phone']) ? htmlspecialchars($_POST['phone']) : NULL;
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
        $shop_name = htmlspecialchars($_POST['shop_name']);
        $shop_address = htmlspecialchars($_POST['shop_address']);
        $latitude = htmlspecialchars($_POST['latitude']);
        $longitude = htmlspecialchars($_POST['longitude']);

        // Check if shopkeeper email already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM shopkeepers WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $exists = $stmt->fetchColumn();

        if ($exists > 0) {
            echo "Error: Email already registered.";
        } else {
            // Insert into shopkeepers table
            $stmt = $conn->prepare("INSERT INTO shopkeepers (shopkeeper_name, email, phone, password) 
                                    VALUES (:shopkeeper_name, :email, :phone, :password)");
            $stmt->bindParam(':shopkeeper_name', $shopkeeper_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password);

            if ($stmt->execute()) {
                // Get the last inserted shopkeeper_id
                $shopkeeper_id = $conn->lastInsertId();

                // Insert into shops table
                $stmt = $conn->prepare("INSERT INTO shops (shop_name, shop_address, shopkeeper_name, shopkeeper_id, latitude, longitude) 
                                        VALUES (:shop_name, :shop_address, :shopkeeper_name, :shopkeeper_id, :latitude, :longitude)");
                $stmt->bindParam(':shop_name', $shop_name);
                $stmt->bindParam(':shop_address', $shop_address);
                $stmt->bindParam(':shopkeeper_name', $shopkeeper_name);
                $stmt->bindParam(':shopkeeper_id', $shopkeeper_id);
                $stmt->bindParam(':latitude', $latitude);
                $stmt->bindParam(':longitude', $longitude);

                if ($stmt->execute()) {
                    echo "Shop registered successfully!";
                } else {
                    echo "Error: Could not register shop.";
                }
            } else {
                echo "Error: Could not register shopkeeper.";
            }
        }
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    }
}
?>

<!-- HTML form for shop registration -->
<form method="POST">
    <h2>Shopkeeper Details</h2>
    <input type="text" name="shopkeeper_name" placeholder="Shopkeeper Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="phone" placeholder="Phone Number (optional)">
    <input type="password" name="password" placeholder="Password" required>

    <h2>Shop Details</h2>
    <input type="text" name="shop_name" placeholder="Shop Name" required>
    <textarea name="shop_address" placeholder="Shop Address" required></textarea>
    
    <h2>Shop Location</h2>
    <input type="text" id="latitude" name="latitude" placeholder="Latitude" required>
    <input type="text" id="longitude" name="longitude" placeholder="Longitude" required>
    <button type="button" onclick="getLocation()">Get My Location</button>

    <button type="submit">Register Shop</button>
</form>

<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById("latitude").value = position.coords.latitude;
            document.getElementById("longitude").value = position.coords.longitude;
        }, function(error) {
            alert("Error getting location: " + error.message);
        });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}
</script>
