<?php
require '../config.php';  // Ensure config.php is using PDO

$response = [];

// 📌 Fetch Data Function (Compatible with PDO)
function fetchData($conn, $query) {
    $stmt = $conn->prepare($query);  
    $stmt->execute();  
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);  

    $labels = [];
    $values = [];
    foreach ($result as $row) {
        $labels[] = $row[array_keys($row)[0]];
        $values[] = $row[array_keys($row)[1]];
    }
    return ["labels" => $labels, "values" => $values];
}

// 📊 Monthly Sales Report
$monthlySalesQuery = "SELECT MONTH(transaction_date) AS month, SUM(amount) AS total FROM transactions GROUP BY MONTH(transaction_date)";
$response['monthlySales'] = fetchData($conn, $monthlySalesQuery);

// 📊 Yearly Sales Growth
$yearlySalesQuery = "SELECT YEAR(transaction_date) AS year, SUM(amount) AS total FROM transactions GROUP BY YEAR(transaction_date)";
$response['yearlySales'] = fetchData($conn, $yearlySalesQuery);

// 📊 Shop-wise Sales
$shopSalesQuery = "SELECT s.shop_name, SUM(t.amount) AS total FROM transactions t JOIN shops s ON t.shopkeeper_id = s.shopkeeper_id GROUP BY s.shop_name";
$response['shopSales'] = fetchData($conn, $shopSalesQuery);

// 📊 New vs Returning Customers
$newReturningUsersQuery = "SELECT 'New Users' AS type, COUNT(*) FROM clients WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
                           UNION
                           SELECT 'Returning Users' AS type, COUNT(DISTINCT client_id) FROM transactions WHERE transaction_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
$response['newReturningUsers'] = fetchData($conn, $newReturningUsersQuery);

// 📊 Order Status Breakdown
$orderStatusQuery = "SELECT order_status, COUNT(*) FROM documents GROUP BY order_status";
$response['orderStatus'] = fetchData($conn, $orderStatusQuery);

// 📊 Total Revenue Over Time
$totalRevenueQuery = "SELECT DATE(transaction_date) AS date, SUM(amount) AS total FROM transactions GROUP BY DATE(transaction_date)";
$response['totalRevenue'] = fetchData($conn, $totalRevenueQuery);

// 📊 Payment Methods Used
$paymentMethodsQuery = "SELECT payment_status, COUNT(*) FROM transactions GROUP BY payment_status";
$response['paymentMethods'] = fetchData($conn, $paymentMethodsQuery);

// Send JSON Response
header('Content-Type: application/json');
echo json_encode($response);
?>
