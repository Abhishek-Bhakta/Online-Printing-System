<?php
include('db.php');

// Fetch all shop details from the database
$query = "SELECT shop_id, shop_name, address, latitude, longitude FROM shops";
$result = mysqli_query($conn, $query);

$shops = [];
while ($row = mysqli_fetch_assoc($result)) {
    $shops[] = $row;
}

// Return the shop data as JSON
echo json_encode($shops);
?>
