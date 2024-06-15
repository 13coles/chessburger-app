<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:index.php');
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>dashboard</title>
     <!-- fav-con  -->
     <link rel="icon" type="image/x-icon" href="../images/chessburger logo.jpg">
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="libraries/fontawesome-free/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- admin dashboard section starts  -->
<div class="dashboard">
  <h2>Sales Analytics</h2>
   <div class="row">

      <div class="column">
         <div class="card">
            <?php

            $new_orders_count = 0;

            try {
                  // Calculate based on order placed
                  $select_new_orders = $conn->prepare("SELECT COUNT(DISTINCT order_number) as order_count FROM `orders` WHERE order_status = 'Order Placed';");
                  $select_new_orders->execute();

                  // Fetch the result
                  $result = $select_new_orders->fetch(PDO::FETCH_ASSOC);

                  // Check if there are new orders
                  if ($result['order_count'] > 0) {
                     // Set the new orders count
                     $new_orders_count = $result['order_count'];
                  }
            } catch (PDOException $e) {
                  echo "Database Error: " . $e->getMessage();
            }
            ?>
            <h3><span>Pending Orders:</span> <?= $new_orders_count; ?></h3>
            <a href="pending_orders.php" class="btn">View Orders</a>
         </div>
      </div>

      <div class="column">
         <div class="card">
            <?php
            $todaySales = 0;
            $todayDate = date('Y-m-d'); // Get today's date in the format 'YYYY-MM-DD'

            $selectTodaySales = $conn->prepare("SELECT SUM(price * quantity) as sales_sum FROM orders WHERE DATE(placed_on) = CURDATE()");
            $selectTodaySales->execute();
            $salesData = $selectTodaySales->fetch(PDO::FETCH_ASSOC);

            if ($salesData && isset($salesData['sales_sum'])) {
                  $todaySales = number_format($salesData['sales_sum'], 2, '.', ',');
               
            }
            ?>
            <h3><span>Today's Sales:</span> ₱ <?= $todaySales; ?></h3>
            <a href="daily_sold.php" class="btn">View Sales</a>
         </div>
      </div>
      
      <div class="column">
         <div class="card">
            <?php
            $total_orders_count = 0;

            // SQL query to count orders placed in October
            $select_total_orders = $conn->prepare("SELECT COUNT(DISTINCT order_number) AS total_orders FROM orders WHERE MONTH(placed_on) = MONTH(CURDATE());");
            $select_total_orders->execute();
            $fetch_count = $select_total_orders->fetch(PDO::FETCH_ASSOC);

            if ($fetch_count && isset($fetch_count['total_orders'])) {
                  $total_orders_count = $fetch_count['total_orders'];
            }

            ?>
            <h3><span>Total Orders:</span> <?= $total_orders_count; ?> </h3>
            <a href="placed_orders.php" class="btn">View Orders</a>
         </div>
      </div>

      <div class="column">
         <div class="card">
            <?php
            $totalSales = 0;

            // Get the current year and month
            $currentYearMonth = date('Y-m');

            // Modify the SQL query to sum total prices for orders placed in the current month
            $selectTotalSales = $conn->prepare("SELECT SUM(price * quantity) as sales_sum FROM orders WHERE DATE_FORMAT(placed_on, '%Y-%m') = :currentYearMonth");
            $selectTotalSales->bindParam(':currentYearMonth', $currentYearMonth);
            $selectTotalSales->execute();
            $salesData = $selectTotalSales->fetch(PDO::FETCH_ASSOC);

            if ($salesData && isset($salesData['sales_sum'])) {
               // Format the total sales value with a comma as a thousands separator
               $totalSales = number_format($salesData['sales_sum'], 2, '.', ',');
            }
            ?>
            <h3><span>Total Sales:</span> ₱ <?= $totalSales; ?></h3>
            <a href="permonth_sold.php" class="btn">View Sales</a>
         </div>

      </div>

   </div>



   <div class="row">
      <div class="rightcolumn">
             <div class="card">
               <h3>Products Chart</h3>
               <a href="permonth_sold.php" class="view-all">View all</a>
               <div class="chart-container">
                  <canvas id="productSalesChart"></canvas>
               </div>
            </div>
         <!-- table reservations management -->
         <div class="card">
            <?php include 'reservation.php'?>
               <h2>Manage Tables Reservation</h2>
               <!-- Display table statuses and allow status updates -->
               <?php foreach ($tables as $table): ?>
                  <div class="table-box <?php echo (isTableAvailable($table['table_id'])) ? 'table-box-available' : 'table-box-occupied'; ?>">
                     <div class="text">
                           <h3>Table Number: <?php echo $table['table_id']; ?></h3><br>
                           <h3>Capacity: <?php echo $table['capacity']; ?> person</h3>
                     </div>
                     <br>
                     <div class="status">
                           <?php
                           $newStatus = (isTableAvailable($table['table_id'])) ? 'occupied' : 'available';
                           $statusText = (isTableAvailable($table['table_id'])) ? 'Available' : 'Occupied';
                           $colorClass = (isTableAvailable($table['table_id'])) ? 'green' : 'red';
                           ?>
                           <a href="dashboard.php?table_id=<?php echo $table['table_id']; ?>&status=<?php echo $newStatus; ?>" style="color: white;" class="<?php echo $colorClass; ?>">
                              <?php echo $statusText; ?>
                           </a>
                           <br>
                     </div>
                  </div>
               <?php endforeach; ?>

            <?php
            function isTableAvailable($tableId) {
               global $conn;
               $checkAvailabilityQuery = "SELECT is_active FROM reservations WHERE table_id = :table_id AND is_active = 1";
               $stmtCheck = $conn->prepare($checkAvailabilityQuery);
               $stmtCheck->bindParam(':table_id', $tableId, PDO::PARAM_INT);
               $stmtCheck->execute();
               $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
               return empty($result);
            }
            ?>
         </div>
      </div>

      <!-- Charts -->
      <div class="leftcolumn">
         <div class="card">
            <h3>Sales Chart</h3>
            <a href="permonth_sold.php" class="view-all">View all</a>
            <div class="chart-container">
               <canvas id="myChart"></canvas>
            </div>
         </div>

         <div class="card">
            <h3>Forecast Chart</h3>
            <?php include '../admin/forecasting.php'?>
            <a href="forecast_chart.php" class="view-all">View all</a>
            <div class="chart-container">
               <canvas id="forecastChart"></canvas>
            </div>
         </div>
      </div>

   </div>

</div>



<!-- chart.js cdn link  -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const originalData = {
    labels: [], // The label will go here dynamically populated with monthly dates
    datasets: [{
        label: 'Monthly sales',
        data: [], // The data will be dynamically populated with monthly sales data
        fill: true,
        backgroundColor: 'rgb(34, 82, 205)', // Darker Blue shade
        borderColor: 'rgba(26, 82, 118, 1)', // Darker Blue
        borderWidth: 1
    }]
};

// Fetch monthly sales data dynamically
fetch('monthly_sales.php') // path to for PHP file of monthlysales
    .then(response => response.json())
    .then(monthlySalesData => {
        // Extract monthly dates and sales data from the response
        const dateLabels = monthlySalesData.map(monthlyData => monthlyData.month);
        const salesData = monthlySalesData.map(monthlyData => monthlyData.monthly_sales);

        // Update the originalData object with the dynamic data
        originalData.labels = dateLabels;
        originalData.datasets[0].data = salesData;

        // Create the chart
        const config = {
            type: 'bar', 
            data: originalData,
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Month',
                        }
                    },
                    y: {
                        title: {
                            display: true,
                          
                        },
                        beginAtZero: false,
                    }
                }
            }
        };

        const myChart = new Chart(
            document.getElementById('myChart'),
            config
        );
    })
    .catch(error => {
        console.error('Error fetching monthly sales data:', error);
    });

    // forecasted sales 
    const forecastData = <?php echo $forecastDataJSON; ?>;
        const labels = forecastData.map(entry => entry.month);
        const dataValues = forecastData.map(entry => entry.sales);

        const data = {
            labels: labels,
            datasets: [{
                label: 'Monthly Sales Forecast',
                data: dataValues,
                fill: true,
                backgroundColor: 'rgb(235, 161, 45)',
                borderColor: 'rgb(243, 248, 214)', 
                tension: 0.1
            }]
        };

        const config = {
            type: 'line',
            data: data,
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Month',
                        }
                    },
                    y: {
                        title: {
                            display: true,
                          
                        },
                        beginAtZero: false,
                    }
                }
            }
        };

        // Create the chart
        const ctx = document.getElementById('forecastChart').getContext('2d');
        new Chart(ctx, config);


      // Product Sales Chart
     // Fetch product sales data from PHP
fetch('m_product.php')
   .then(response => response.json())
   .then(productSales => {
      // Extract data from the PHP result
      const productLabels = productSales.map(item => item.product_name);
      const productDataValues = productSales.map(item => item.total_sales);

      // Set up Chart.js data
      const productSalesData = {
         labels: productLabels,
         datasets: [{
            label: 'Total Sales per Product',
            data: productDataValues,
            backgroundColor: getRandomColors(productLabels.length),
            hoverOffset: 4
         }]
      };

      // Set up Chart.js config
      const productSalesConfig = {
         type: 'line',
         data: productSalesData
      };

      // Create the chart
      const productSalesChart = new Chart(
         document.getElementById('productSalesChart'),
         productSalesConfig
      );
   })
   .catch(error => {
      console.error('Error fetching product sales data:', error);
   });

// Function to generate random colors
function getRandomColors(numColors) {
   const colors = [];
   for (let i = 0; i < numColors; i++) {
      colors.push(`rgb(${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)})`);
   }
   return colors;
}

</script>




   

    




<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>
</body>
</html>