<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shop_name = $_POST['shop_name'];
    $address = $_POST['address'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $query = "INSERT INTO shops (shop_name, address, latitude, longitude) VALUES ('$shop_name', '$address', '$latitude', '$longitude')";
    if (mysqli_query($conn, $query)) {
        echo "Shop added successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

