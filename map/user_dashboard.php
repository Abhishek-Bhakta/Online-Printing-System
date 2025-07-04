<?php include('db.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Select Shop</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    /* Floating Map Window */
    .map-container {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 80%;
      max-width: 600px;
      height: 400px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
      padding: 10px;
      display: none;
    }
    .map-container #map { height: 90%; }
    .close-btn {
      cursor: pointer;
      background: red;
      color: white;
      padding: 5px 10px;
      border-radius: 5px;
      float: right;
    }
  </style>
</head>
<body>
  <button onclick="openMap()">Select Shop</button>
  
  <div class="map-container" id="mapWindow">
    <span class="close-btn" onclick="closeMap()">X</span>
    <div id="map"></div>
  </div>
  
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    function openMap() {
      document.getElementById('mapWindow').style.display = 'block';
      initializeMap();
    }
    function closeMap() {
      document.getElementById('mapWindow').style.display = 'none';
    }
    
    function initializeMap() {
      var map = L.map('map').setView([20.5937, 78.9629], 5);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
      
      fetch('get_shops.php')
        .then(response => response.json())
        .then(shops => {
          shops.forEach(shop => {
            L.marker([shop.latitude, shop.longitude]).addTo(map)
              .bindPopup(`<b>${shop.shop_name}</b><br>${shop.address}`)
              .on('click', function() {
                alert("Selected: " + shop.shop_name);
                closeMap();
              });
          });
        });
    }
  </script>
</body>
</html>
