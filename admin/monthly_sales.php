<?php
// Include your database connection code here
include '../components/connect.php';

$monthlySalesData = array();

$selectMonthlySales = $conn->prepare("SELECT DATE_FORMAT(placed_on, '%Y-%m') AS month, SUM(price * quantity) AS monthly_sales FROM orders GROUP BY month ORDER BY month;");
$selectMonthlySales->execute();
$monthlySalesResult = $selectMonthlySales->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($monthlySalesResult);
?>
