<?php
include '../components/connect.php';

// Function to fetch sales per product for a specific month
function fetchMonthlyProductSales($conn, $year, $month) {
    $selectData = $conn->prepare("SELECT product_id, product_name, SUM(quantity) AS total_quantity, SUM(price * quantity) AS total_sales
                                 FROM orders
                                 WHERE YEAR(date) = :year AND MONTH(date) = :month
                                 GROUP BY product_id");
    $selectData->bindParam(':year', $year);
    $selectData->bindParam(':month', $month);
    $selectData->execute();
    return $selectData->fetchAll(PDO::FETCH_ASSOC);
}

// Create an array for previous and future years dynamically
$years = range(date('Y') - 5, date('Y') + 5);

// Create an array for months dynamically
$months = [];
for ($i = 1; $i <= 12; $i++) {
    $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
}

// Get the selected year and month from the form or use the current year and month
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('n');

// Fetch product sales based on the selected year and month
$productSales = fetchMonthlyProductSales($conn, $selectedYear, $selectedMonth);

$currentDate = date('F j, Y'); // Format the date as desired
echo json_encode($productSales);
?>