<?php
require '../vendor/autoload.php';
use Phpml\Regression\LeastSquares;
include '../components/connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Query to fetch data for training the model
$sql = "SELECT DATE_FORMAT(placed_on, '%Y-%m') AS placed_month, SUM(price * quantity) AS monthly_sales
        FROM orders
        WHERE placed_on < CURDATE() AND placed_on < '2023-11-01'
        GROUP BY placed_month
        ORDER BY placed_month";
try {
    $result = $conn->query($sql);

    if (!$result) {
        die("Error in the query: " . $conn->errorInfo()[2]);
    }

    // Check if there is enough historical data for training
    $minHistoricalDataCount = 3; // Minimum number of months required
    $historicalDataCount = $result->rowCount();

    if ($historicalDataCount < $minHistoricalDataCount) {
        die("Not enough historical data available for training. Need at least $minHistoricalDataCount months.");
    }

    // Continue with training the model
    $samples = [];
    $targets = [];

    // while statement use to execute statements repeatedly, and results are true
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $samples[] = [strtotime($row['placed_month'])];
        $targets[] = $row['monthly_sales'];
    }

    // Train the model
    $regression = new LeastSquares();
    $regression->train($samples, $targets);

    // strtotime function converts string to a timestamp or current date
    // Set the start date for forecasting
    $startDate = strtotime('now'); // Start from the current date

    // Set the end date for forecasting (3 months from the current date)
    $endDate = strtotime('+2 months', $startDate);

    // Forecasting loop
    $forecastData = [];

    while ($startDate <= $endDate) {
        $startMonth = date('Y-m', $startDate);

        // Forecast for the next month
        $prediction = $regression->predict([$startDate]);
        $forecastData[] = [
            'month' => $startMonth,
            'sales' => round($prediction, 2),
        ];

        // Move to the next month
        $startDate = strtotime('+1 month', $startDate);
    }

    // json_encode used to convert PHP array or objects into JSON representaions
    // this is used to visualize the chart or graphs
    // JSON encode the forecast data for JavaScript
    $forecastDataJSON = json_encode($forecastData);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
} finally {
    // Close the database connection
    $conn = null;
}
?>

 