<?php
session_start();

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['document'])) {
    // Process the file upload here...

    // Example data
    $documentName = $_FILES['document']['name'];
    $pages = 1; // Calculate pages based on the file type
    $price = 3; // Example price per page

    // Store in session
    if (!isset($_SESSION['uploaded_documents'])) {
        $_SESSION['uploaded_documents'] = [];
    }
    $_SESSION['uploaded_documents'][] = [
        'name' => $documentName,
        'pages' => $pages,
        'price' => $price
    ];

    // Redirect or response
    header("Location: cart.html"); // Redirect to cart or another page
    exit();
}
?>
