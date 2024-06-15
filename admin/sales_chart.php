<?php
include '../components/connect.php';

// Function to fetch data for a specific date from orders
function fetchorders($conn, $year, $month) {
    $selectData = $conn->prepare("SELECT product_name, quantity, price, date,
                                 (quantity * price) AS total_price
                                 FROM orders
                                 WHERE DATE_FORMAT(date, '%Y-%m') = :date");
    $date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT); // Ensure 2-digit month
    $selectData->bindParam(':date', $date);
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

$selectedMonth = null;
$selectedYear = null;
$orders = [];

if (isset($_POST['filter']) && isset($_POST['month']) && isset($_POST['year'])) {
    $selectedMonth = $_POST['month'];
    $selectedYear = $_POST['year'];
    $orders = fetchorders($conn, $selectedYear, $selectedMonth);
}


$currentDate = date('F j, Y'); // Format the date as desired

?>

<!DOCTYPE html>
<html>
<head>
    <title>Monthly sales report</title>
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
        .back-arrow{
            display: none;
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
    <h4>Monthly Sales Report</h4>
    <div class="filter-form">
        <form method="post">
            <label for="month">Select Month and Year: </label>
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
if (isset($_POST['filter'])) {
    if (empty($orders)) {
        echo '<p>No data available for the selected month and year.</p>';
    } else {
        // Your existing table for order details
        echo '<table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Sales</th>
                    </tr>
                </thead>
                <tbody>';
        foreach ($orders as $row) {
            echo '<tr>
                    <td>' . $row['date'] . '</td>
                    <td>' . $row['product_name'] . '</td>
                    <td>' . $row['quantity'] . '</td>
                    <td>₱' . number_format($row['price'], 2, '.', ',') . '</td>
                    <td>₱' . number_format($row['total_price'], 2, '.', ',') . '</td>
                </tr>';
        }
        echo '</tbody>
              <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right;">Total Sales:</td>
                    <td class="overall-total">₱' . number_format(array_sum(array_column($orders, 'total_price')), 2, '.', ',') . '</td>
                </tr>
              </tfoot>
            </table>';

    }
}
?>



</div>

    <script>
    function togglePrintView() {
            window.print();
        }
    </script>
</body>
</html>
