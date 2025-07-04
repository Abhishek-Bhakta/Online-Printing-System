         /*!
    * Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
    * Copyright 2013-2023 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
    // 
// Scripts
// 

window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }
    
    });
    function toggleStatus() {
    $.ajax({
        url: 'features/update_shop_status.php',
        type: 'POST',
        success: function(response) {
            if (response === "success") {
                let currentStatus = $("#shop_status").text().trim().toLowerCase();
                let newStatus = (currentStatus === "offline") ? "online" : "offline";
                $("#shop_status").text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
            } else {
                alert("Failed to update status!");
            }
        }
    });
    }
    
    function updateStatus(document_id, status) {
    $.ajax({
        url: 'features/update_status.php',
        type: 'POST',
        data: { document_id: document_id, status: status },
        success: function(response) {
            if (response === "success") {
                $("#status_" + document_id).text(status.charAt(0).toUpperCase() + status.slice(1));
            } else {
                alert("Failed to update status!");
            }
        }
    });
    }
// Default QR Code generation on page load
window.onload = function() {
    generateQR(true);
};

function generateQR(isDefault = false) {
    var shopURL = "<?php echo $shop_url; ?>"; // Shop URL
    var shopName = "<?php echo htmlspecialchars($shop_name); ?>"; // Shop name
    var shopkeeperName = "<?php echo htmlspecialchars($username); ?>"; // Shopkeeper's name
    var qrCodeDiv = document.getElementById("qrCode");
    
    var qrImage = new Image();
    // Update QR code size and quality (increased size to 300x300 for better clarity)
    qrImage.src = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" + encodeURIComponent(shopURL);
    
    qrImage.onload = function() {
        var canvas = document.createElement("canvas");
        var ctx = canvas.getContext("2d");

        // Set canvas size to match the QR code size (increased size for better visibility)
        canvas.width = qrImage.width;
        canvas.height = qrImage.height + 80;

        ctx.drawImage(qrImage, 0, 40); // Draw the QR code image on the canvas

        // Add text on top of the QR code (shop name and shopkeeper's name)
        ctx.font = "bold 16px Arial"; // Increase font size for better visibility
        ctx.fillStyle = "#000000"; // Text color
        ctx.textAlign = "center";
        ctx.fillText(shopName, canvas.width / 2, 30); // Shop name text

        ctx.font = "italic 14px Arial"; // Smaller font for shopkeeper's name
        ctx.fillText(shopkeeperName, canvas.width / 2, qrImage.height + 60); // Shopkeeper name text

        // Set the id for the canvas so it's accessible for download
        canvas.id = "qrCanvas";

        qrCodeDiv.innerHTML = "";
        qrCodeDiv.appendChild(canvas);

        // Show the download button once the QR code is generated
        document.getElementById("downloadQR").style.display = "block";
    };
    
    // If it's a default QR code, generate it without needing to click the button
    if (isDefault) {
        qrImage.src = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" + encodeURIComponent(shopURL);
    }
}

function downloadQR() {
    var canvas = document.getElementById("qrCanvas");
    if (!canvas) {
        alert("QR code not generated yet!");
        return;
    }
    
    // Create a link to download the image
    var link = document.createElement("a");
    link.href = canvas.toDataURL("image/png");
    link.download = "shop_qr_code.png";
    link.click();


    // Get the canvas content as a Blob (binary large object)
    canvas.toBlob(function(blob) {
        // Create a link to download the Blob content
        var link = document.createElement("a");
    
        // Create a URL for the Blob
        var url = URL.createObjectURL(blob);
    
        // Set up the download link
        link.href = url;
        link.download = "shop_qr_code_<?php echo $shop_id; ?>.png"; // Dynamic file name
    
        // Programmatically trigger the click to download
        link.click();
    
        // Clean up the object URL after download
        URL.revokeObjectURL(url);
    }, "image.png");
    }
    fetch("/Printify/shops/features/fetch_analytics.php")
                .then(response => response.json())
                .then(data => {
                    // Sales Analytics
                    renderBarChart("monthlySalesChart", "Monthly Sales", data.monthlySales.labels, data.monthlySales.values);
                    renderLineChart("yearlySalesChart", "Yearly Sales", data.yearlySales.labels, data.yearlySales.values);
                    renderPieChart("shopSalesChart", "Shop Sales", data.shopSales.labels, data.shopSales.values);
                    renderLineChart("dailySalesChart", "Daily Sales", data.totalRevenue.labels, data.totalRevenue.values);
    
                    // User Growth & Engagement
                    renderDoughnutChart("newReturningUsersChart", "New vs Returning Users", data.newReturningUsers.labels, data.newReturningUsers.values);
                    renderBarChart("totalUsersChart", "Total User Registrations", ["Jan", "Feb", "Mar", "Apr"], [50, 75, 100, 125]);  // Example Data
                    renderBarChart("activeUsersChart", "Most Active Users", ["User1", "User2", "User3"], [5, 7, 10]);  // Example Data
                    renderBarChart("shopRetentionChart", "Shops with Highest User Retention", ["Shop1", "Shop2"], [10, 15]);  // Example Data
    
                    // Shop Performance
                    renderBarChart("totalOrdersChart", "Total Orders per Shop", ["Shop1", "Shop2", "Shop3"], [30, 50, 20]);  // Example Data
                    renderLineChart("monthlyRevenueChart", "Monthly Revenue per Shop", data.totalRevenue.labels, data.totalRevenue.values);
                    renderPieChart("orderStatusChart", "Order Status", data.orderStatus.labels, data.orderStatus.values);
    
                    // Transaction Analytics
                    renderLineChart("totalRevenueChart", "Total Revenue Over Time", data.totalRevenue.labels, data.totalRevenue.values);
                    renderPieChart("paymentMethodsChart", "Payment Methods", data.paymentMethods.labels, data.paymentMethods.values);
                    renderBarChart("avgOrderValueChart", "Average Order Value", ["Jan", "Feb", "Mar", "Apr"], [100, 150, 120, 130]);  // Example Data
                })
                .catch(error => console.error("Error fetching analytics data:", error));
    
            // 📌 Helper Function for Bar Chart
            function renderBarChart(canvasId, label, labels, values) {
                new Chart(document.getElementById(canvasId), {
                    type: "bar",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: values,
                            backgroundColor: "rgba(75, 192, 192, 0.6)",
                            borderColor: "rgba(75, 192, 192, 1)",
                            borderWidth: 1
                        }]
                    }
                });
            }
    
            // 📌 Helper Function for Line Chart
            function renderLineChart(canvasId, label, labels, values) {
                new Chart(document.getElementById(canvasId), {
                    type: "line",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: values,
                            borderColor: "rgba(255, 99, 132, 1)",
                            borderWidth: 2,
                            fill: false
                        }]
                    }
                });
            }
    
            // 📌 Helper Function for Pie Chart
            function renderPieChart(canvasId, label, labels, values) {
                new Chart(document.getElementById(canvasId), {
                    type: "pie",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: values,
                            backgroundColor: ["red", "blue", "green", "yellow", "purple"]
                        }]
                    }
                });
            }
    
            // 📌 Helper Function for Doughnut Chart
            function renderDoughnutChart(canvasId, label, labels, values) {
                new Chart(document.getElementById(canvasId), {
                    type: "doughnut",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: values,
                            backgroundColor: ["orange", "cyan", "purple", "lime", "pink"]
                        }]
                    }
                });
            }function generateQR() {
                var shopURL = "<?php echo $shop_url; ?>"; // Shop URL
                var shopName = "<?php echo htmlspecialchars($shop_name); ?>"; // Shop Name
                var shopkeeperName = "<?php echo htmlspecialchars($username); ?>"; // Shopkeeper Name
            
                var qrCodeDiv = document.getElementById("qrCode");
            
                // Clear any previous QR Code before creating a new one
                qrCodeDiv.innerHTML = "";
            
                // Generate a unique identifier to ensure the QR code is different each time
                var uniqueId = new Date().getTime() + Math.random(); // Combine current time with a random value
            
                // Create the unique data string for the QR code (adding the unique identifier to avoid caching)
                var qrData = shopURL + "?unique=" + uniqueId;
            
                // Generate the QR Code using the qrcode.js library
                var qrcode = new QRCode(qrCodeDiv, {
                    text: qrData,
                    width: 300,
                    height: 300,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.L
                });
            
                // Delay to ensure the QR Code has been rendered
                setTimeout(function () {
                    var canvas = qrCodeDiv.querySelector("canvas");
            
                    if (canvas) {
                        // Add optional text to the QR code canvas
                        var ctx = canvas.getContext("2d");
                        ctx.font = "20px Arial";
                        ctx.fillStyle = "#000000";
                        ctx.fillText(shopName, 10, 350); // Example: Adding shop name below QR code
                    }
            
                    // Show the download button after QR code is generated
                    document.getElementById("downloadQR").style.display = "inline-block";
                }, 500); // Increased delay to ensure QR code is rendered
            }
            
            function downloadQRCode() {
                var qrCard = document.querySelector(".qr-card");
            
                domtoimage.toPng(qrCard, { quality: 1 }) // High-quality PNG capture
                    .then(function (dataUrl) {
                        var link = document.createElement("a");
                        link.href = dataUrl;
                        link.download = "shop_qr_code.png"; // File name
                        link.click();
                    })
                    .catch(function (error) {
                        console.error("Error generating image:", error);
                    });
            }
            
                                                
// Initialize the map with latitude and longitude
function initMap(lat, lon) {
    const map = L.map('map-container').setView([lat, lon], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const marker = L.marker([lat, lon], { draggable: true }).addTo(map);

    // Update lat/lon when marker is dragged
    marker.on('dragend', function(event) {
        const position = event.target.getLatLng();
        document.getElementById('latitude').value = position.lat.toFixed(6);
        document.getElementById('longitude').value = position.lng.toFixed(6);
    });

    // Click on the map to update the marker position
    map.on('click', function(event) {
        const lat = event.latlng.lat.toFixed(6);
        const lon = event.latlng.lng.toFixed(6);
        marker.setLatLng([lat, lon]);
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lon;
    });
}

// Call this function when the page loads to display the map
document.addEventListener('DOMContentLoaded', function() {
    const lat = parseFloat(document.getElementById('latitude').value);
    const lon = parseFloat(document.getElementById('longitude').value);

    // Initialize the map with the default lat/lon values from the form
    if (!isNaN(lat) && !isNaN(lon)) {
        initMap(lat, lon);
    } else {
        alert("Invalid coordinates, using default location.");
        initMap(20.5937, 78.9629);  // Default coordinates (India)
    }
});
