<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopkeeper Dashboard</title>
</head>
<body>
  <h2>Add Shop</h2>
  <form action="save_shop_address.php" method="POST">
    <label for="shop_name">Shop Name:</label>
    <input type="text" id="shop_name" name="shop_name" required><br><br>

    <label for="address">Shop Address:</label>
    <input type="text" id="address" name="address" required><br><br>

    <label for="latitude">Latitude:</label>
    <input type="text" id="latitude" name="latitude" required><br><br>

    <label for="longitude">Longitude:</label>
    <input type="text" id="longitude" name="longitude" required><br><br>

    <button type="submit">Save Shop</button>
  </form>
</body>
</html>
