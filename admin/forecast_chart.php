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

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $samples[] = [strtotime($row['placed_month'])];
        $targets[] = $row['monthly_sales'];
    }

    // Train the model
    $regression = new LeastSquares();
    $regression->train($samples, $targets);

    // Set the start date for forecasting
    $startDate = strtotime('now'); // Start from the current date

    // Set the end date for forecasting (3 months from the current date)
    $endDate = strtotime('+2 months', $startDate);

   
        // Forecasting loop
        $nextThreeMonthsMonths = [];
        $predictedSales = [];
        $percentageChanges = [];

        while ($startDate <= $endDate) {
            $startMonth = date('Y-m', $startDate);

            // Forecast for the next month
            $prediction = $regression->predict([$startDate]);
            $nextThreeMonthsMonths[] = $startMonth;
            $predictedSales[] = round($prediction, 2);

            // Move to the next month
            $startDate = strtotime('+1 month', $startDate);
        }

        // Calculate percentage changes
        for ($i = 1; $i < count($predictedSales); $i++) {
            $percentageChange = (($predictedSales[$i] - $predictedSales[$i - 1]) / $predictedSales[$i - 1]) * 100;
            $percentageChanges[] = $percentageChange;
        }



} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
} finally {
    // Close the database connection
    $conn = null;
}



$currentDate = date('F j, Y');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Sales Forecast</title>
      <!-- fav-con  -->
      <link rel="icon" type="image/x-icon" href="../images/chessburger logo.jpg">
    <style>
       
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            /* background-color: #f5f5f5; */
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-left: auto;
            margin-right: auto;
            border: 1px solid #ddd;
        }

        th, td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e1e1e1;
        }

        .print-button {
        position: absolute;
        top: 20px;
        right: 20px;
        background-color: #007bff;
        color: #fff;
        border: 2px solid #007bff;
        padding: 6px 12px;
        cursor: pointer;
        border-radius: 5px;
        font-size: 18px;
        }

        .print-button:hover {
            background-color: #0056b3;
        }

        .price {
            font-weight: bold;
        }

        .price::before {
            content: "₱ ";
        }

        .table-container {
            display: flex;
            justify-content: space-between;
            margin: 0 auto;
            max-width: 1200px;
        }

        .header img {
            max-width: 100px;
            max-height: 100px;
            float: right;
            margin-right: 20px;
            margin-bottom: 60px;
        }

        .header h2 {
            font-size: 24px;
            margin: 0;
            margin-top: 10px;
            text-align: center;
        }

        .h3 {
            font-size: 18px;
            float: left;
        }

        .header p {
            font-size: 16px;
            margin: 0;
            float: left;
        }

           /* Sidebar styles */
        .sidenav {
            height: 100%;
            width: 200px;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #ddd;
            overflow-x: hidden;
            padding-top: 20px;
        }

        .sidenav a {
            padding: 8px 8px 8px 16px;
            text-decoration: none;
            font-size: 18px;
            color: #000;
            display: block;
            text-align: left;
        }

        .sidenav a:hover {
            color: #e312ea;
        }

        @media print {
            .print-button,.sidenav{
                display: none;
            }

            .header{
                display: block;
            }
            h2 {
                text-align: center;
            }

            table {
                width: 100%;
                margin: 0 auto;
                border: 1px solid #ddd;
            }
        }

        @media screen {
            .print-button {
                display: block;
            }

            .header {
                display: none;
            }
        }
    </style>
</head>
<body>
     <!-- Sidebar -->
     <div class="sidenav">
        <a href="dashboard.php">Dashboard</a>
        <!-- <a href="sales_chart.php">Monthly Sales</a> -->
        <a href="daily_sold.php">Daily Sales</a>
        <a href="permonth_sold.php">Monthly Sales</a>
        <a href="forecast_chart.php">Forecasted Sales</a>
    </div>

<button class="print-button" onclick="togglePrintView()">Print Report</button>
<div class="header">
    <img src="../images/chessburger logo.jpg" alt="chessburger logo">
    <br><h3> Company name: Chess Burger Cafe</h3><br>
    <p> Street name: Purok Malipayon, Bug-Ang,</p><br>
    <p> City/State: Toboso,Negross Occidental, 6125,</p><br>
    <p>Phone number: 0926 014 2576</p><br>
    <p>Date:<?php echo $currentDate; ?></p>
</div>

<h2> Monthly Sales Forecast</h2>



    <!-- Forecasted Data Table -->
    <table class ="container">
        <tr>
            <th>Months</th>
            <th>Sales</th>
            <th>Percentage Change</th>
        </tr>
        <?php
        // Loop through forecasted sales data
        foreach ($nextThreeMonthsMonths as $index => $month) {
            echo '<tr>';
            echo '<td>' . $month . '</td>';
            echo '<td class="price">' . number_format($predictedSales[$index], 2) . '</td>';
            // Display percentage change for each forecasted month
            if ($index > 0) {
                $percentageChange = $percentageChanges[$index - 1];
                echo '<td>' . number_format($percentageChange, 2) . '%</td>';
            } else {
                // No percentage change for the first forecasted month
                echo '<td></td>';
            }
            echo '</tr>';
        }
        ?>
    </table>

</div>

<script>
    function togglePrintView() {
        window.print();
    }
</script>
</body>
</html>
