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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Product Sold</title>
      <!-- fav-con  -->
      <link rel="icon" type="image/x-icon" href="../images/chessburger logo.jpg">
    <style>
    body {
        text-align: center;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        text-align: left;
        padding: 20px;
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

    .h3{
        font-size: 18px;
        float: left;

    }

    .header p {
        font-size: 16px;
        margin: 0;
        float: left;
       
       
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin: 10px 0;
    }

    table, th, td {
        border: 1px solid #ddd;
    }

    th, td {
        padding: 8px;
        text-align: center;
        font-size: 16px;
    }

    th {
        background-color: #f2f2f2;
    }

    .overall-total {
        font-weight: bold;
        font-size: 18px;

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

    .filter-form {
        display: block;
    }

    .total-row{
        font-weight:bold;
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
        .print-button,
        .filter-form{
            display: none;
        }

        .header img {
            max-width: 100px;
            max-height: 100px;
        }

        .header {
            float: none;
        }

        .header h2,
        .header p,
        .header h3 {
            margin: 0;
        }
        .sidenav{
            display: none;
        }


    }
    @media screen {
            .print-button {
                display: block;
            }

            .filter-form {
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

<div class="container">
    <button class="print-button" onclick="togglePrintView()">Print Report</button>
    <div class="header">
        <img src="../images/chessburger logo.jpg" alt="chessburger logo">
        <br><h3> Company name: Chess Burger Cafe</h3><br>
        <p> Street name: Purok Malipayon, Bug-Ang,</p><br>
        <p> City/State: Toboso,Negross Occidental, 6125,</p><br>
        <p>Phone number: 0926 014 2576</p><br>
        <p>Date:<?php echo $currentDate; ?></p>
    </div>
    <br>
    <br>

    <h4>Monthly Sales Report- <?php echo date('F Y', strtotime("$selectedYear-$selectedMonth-01")); ?></h4>
   
    <!-- Filter form -->
    <div class="filter-form">
    <form method="post">
        <label for="month">Select Month: </label>
        <select id="month" name="month">
            <?php
            foreach ($months as $monthNum => $monthName) {
                echo '<option value="' . $monthNum . '"';
                if ($selectedMonth == $monthNum) {
                    echo ' selected';
                }
                echo '>' . $monthName . '</option>';
            }
            ?>
        </select>
        <label for="year">Select Year: </label>
        <select id="year" name="year">
            <?php
            foreach ($years as $year) {
                echo '<option value="' . $year . '"';
                if ($selectedYear == $year) {
                    echo ' selected';
                }
                echo '>' . $year . '</option>';
            }
            ?>
        </select>
        <input type="submit" name="filter" value="Filter">
    </form>
    </div>


    <?php
    if (empty($productSales)) {
        echo '<p>No data available for the selected month.</p>';
    } else {
        // Your existing table for product sales
        echo '<table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Total Quantity Sold</th>
                        <th>Total Sales</th>
                    </tr>
                </thead>
                <tbody>';
        $totalQuantity = 0;
        $totalSales = 0;
        foreach ($productSales as $productRow) {
            echo '<tr>
                <td>' . $productRow['product_name'] . '</td>
                <td>' . number_format($productRow['total_quantity']) . '</td>
                <td>₱' . number_format($productRow['total_sales'], 2) . '</td>
            </tr>';
            $totalQuantity += $productRow['total_quantity'];
            $totalSales += $productRow['total_sales'];
        }
        echo '<tr class="total-row">
                <td>Total sold:</td>
                <td>' . number_format($totalQuantity) . '</td>
                <td>₱' . number_format($totalSales, 2) . '</td>
            </tr>';
        echo '</tbody>
            </table>';
    }
    ?>


<script>
    function togglePrintView() {
            window.print();
        }
</script>

</body>
</html>
