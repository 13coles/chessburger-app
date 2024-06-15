<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

try {
    include '../components/connect.php';
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location: index.php');
    exit();
}

if (isset($_POST['update_order'])) {
    $order_id = isset($_POST['order_id']) ? htmlspecialchars($_POST['order_id']) : '';
    $order_status = isset($_POST['order_status']) ? htmlspecialchars($_POST['order_status']) : '';

    $update_status = $conn->prepare("UPDATE `orders` SET order_status = ? WHERE order_number = ?");
    $update_status->execute([$order_status, $order_id]);
    $message[] = 'Order status updated!';
}

if (isset($_GET['delete'])) {
    $delete_id = isset($_GET['delete']) ? htmlspecialchars($_GET['delete']) : '';
    $delete_order = $conn->prepare("DELETE FROM `orders` WHERE order_number = ?");
    $delete_order->execute([$delete_id]);
    header('location: placed_orders.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placed Orders</title>
      <!-- fav-con  -->
      <link rel="icon" type="image/x-icon" href="../images/chessburger logo.jpg">

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="../libraries/fontawesome-free/css/all.css">
   
    <!-- <link rel="stylesheet" href="../css/admin_style.css"> -->

    <style>
    .heading {
      background-color: black; /* Added black background */
      text-align: center;
      padding: 20px; /* Added padding for better visibility */
   }
   .heading h2{
    color: #fff;
    font-size: 24px;
   }

  table {
    border-collapse: collapse;
    width: auto;
    margin-bottom: 20px;
    font-family: 'Arial', sans-serif;
    }

    th, td {
        border: 1px solid #ddd;
        text-align: left;
        padding: 12px;
        font-size: 14px;
        font-weight: normal;
        background-color: #f9f9f9;
    }

    td {
        color: #000;
        font-weight: 800;
        width: 100px; /* Adjust the width as needed */
    }

    th {
        background-color: #2c2c2c;
        color: #fff;
        width: 150px; /* Adjust the width as needed */
    }

    /* Style for Filter Forms */
    .container {
        margin-top: 20px;
    }

    .row {
        display: flex;
        justify-content: space-between;
    }

    .filter-form, .filter-ordertype {
        width: 48%; /* Adjust the width as needed */
    }

    .form-label {
        margin-right: 1rem;
    }

    /* Optional: Style the submit button */
    .filter-form input[type="submit"], .filter-ordertype input[type="submit"] {
        width: auto; /* Adjust the width as needed */
    }




    /* Button styles */
    .filter-btn{
        display: inline-block;
        padding: 18px 20px;
        font-size: 14px;
        font-weight: bold;
        text-align: center;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .filter-btn {
        background-color: #4CAF50;
        color: white;
    }

    .filter-btn:hover {
        background-color: #45a049;
    }

    .filter-btn-secondary {
        background-color: #ccc;
        color: #333;
    }

    .filter-btn-secondary:hover {
        background-color: #999;
    }

    /* Style for the placed orders section */
    .placed-orders {
    padding: 20px;
    }
    
    .order-table {
    width: 100%;
    border-collapse: collapse;
    }
    
    .order-table th, .order-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
    }
    
    .order-table th {
    background-color: #f2f2f2;
    }
    
    .zoomable-image img {
    cursor: pointer;
    max-width: 100px;
    max-height: 100px;
    transition: transform 0.3s;
    }
    
    .zoomable-image img:hover {
    transform: scale(1.2);
    }
    
    /* Style for the image modal */
    .image-modal {
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 999;
    cursor: pointer;
    }
    
    .image-modal img {
    max-width: 80%;
    max-height: 80%;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
    border: 1px solid #fff;
    border-radius: 5px;
    }
    
    /* Center the modal image horizontally and vertically */
    .image-modal img {
    display: block;
    margin: 0 auto;
    }
    
    /* Close the modal when clicking on it */
    .image-modal {
    cursor: pointer;
    }

    /* Style the "Show Proof" link */
    .show-photo-link {
        cursor: pointer;
        color: var(--main-color);
        text-decoration: underline;
        display: inline-block;
        margin-top: 5px;
    }

    /* Style the proof photo container (initially hidden) */
    .proof-photo-container {
        display: none;
        margin-top: 10px;
    }

    /* Style the proof photo */
    .proof-photo {
        max-width: 100px;
        max-height: 100px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .empty-box {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100px; /* Set the desired height */
    border: 1px solid #ccc; /* Optional: Add border for styling */
    border-radius: 8px; /* Optional: Add border radius for styling */
    }

    .empty-message {
        color: red;
        text-align: center;
        font-size: 18px;
    }


    /* Responsive media query for smaller screens */
    @media (max-width: 1080px) {
    .placed-orders .order-table th, .order-table td {
        font-size: 10px; /* Reduce font size for smaller screens */
    }
    }

    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 20px 0;
        justify-content: flex-end;
    }

    .pagination a {
        text-decoration: none;
        color: #007bff;
        padding: 8px 16px;
        border: 1px solid #007bff;
        margin: 0 4px;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .pagination a:hover {
        background-color: #007bff;
        color: #fff;
    }

    .pagination .active {
        background-color: #007bff;
        color: #fff;
    }
</style>


</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- Placed Orders section starts  -->
<div class="heading">
   <h2>Placed Orders</h2>
</div>
<section class="placed-orders">
 
    <div class="container">
    <div class="row">
        <!-- Filter by Order Type form -->
        
            <form action="" method="GET" class="filter-form">
            
                    <label for="orderType" class="form-label">Filter by Order Type:</label>
                    <select name="orderType" id="orderType" class="form-control">
                        <option value="" selected>All</option>
                        <option value="Dine-in">Dine-in</option>
                        <option value="pick-up">Pick-up</option>
                        <option value="delivery">Delivery</option>
                    </select>
                    <input type="submit" value="Filter" class="btn btn-primary mt-2 ml-2">
             
            </form>
      

        <!-- Filter by Order Status form -->
        
            <form action="" method="GET" class="filter-ordertype">
                    <label for="orderStatus" class="form-label">Filter by Order Status:</label>
                    <select name="orderStatus" id="orderStatus" class="form-control">
                        <option value="" selected>All</option>
                        <option value="Order Placed">Order Placed</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="denied">Denied</option>
                        <option value="preparing">Preparing</option>
                        <option value="order-ready">Order Ready</option>
                        <option value="out-for-delivery">Out for Delivery</option>
                        <option value="completed">Completed</option>
                        <option value="canceled">Canceled</option>
                        <option value="received">Received</option>
                    </select>
                    <input type="submit" value="Filter" class="btn btn-primary mt-2 ml-2">
            </form>
      
    </div>
</div>


    <?php

// Set the number of orders per page
$perPage = 7;

// Get the current page from the URL parameter
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the OFFSET based on the current page and records per page
$offset = ($page - 1) * $perPage;

// Your SQL query
$sql = "
    SELECT
        order_number,
        CONCAT(name, ', <br>', number, ',<br> ', address) AS user_info,
        method,
        GROUP_CONCAT(CONCAT(product_name, ' x ', quantity) SEPARATOR ', <br>') AS product_details,
        SUM(price * quantity) AS total_price,
        placed_on,
        order_status,
        table_id,
        order_type,
        time,
        date,
        proof_payment
    FROM Orders
    WHERE MONTH(placed_on) = MONTH(CURDATE())";

// Filter by Order Type
if (isset($_GET['orderType']) && !empty($_GET['orderType'])) {
    $orderTypeFilter = htmlspecialchars($_GET['orderType']);
    $sql .= " AND order_type = '$orderTypeFilter'";
}

// Filter by Order Status
if (isset($_GET['orderStatus']) && !empty($_GET['orderStatus'])) {
    $orderStatusFilter = htmlspecialchars($_GET['orderStatus']);
    $sql .= " AND order_status = '$orderStatusFilter'";
}

$sql .= "
    GROUP BY
        order_number, user_id, name, number, address, method, placed_on,
        order_status, table_id, order_type, time, date, proof_payment
    ORDER BY date DESC
    LIMIT :per_page OFFSET :offset";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
$stmt->bindParam(':per_page', $perPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

// Fetch the results
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

 // Get the total number of rows for pagination
 $totalRows = $stmt->rowCount();

 // Calculate the total number of pages
 $totalPages = ceil($totalRows / $perPage);
// Display the orders
if ($result) {
    echo "<table>";
    echo "<tr>
            <th>Order Number</th>
            <th>Customer Information</th>
            <th>Method</th>
            <th>Product Details</th>
            <th>Total Price</th>
            <th>Placed On</th>
            <th>Order Type</th>
            <th>Time</th>
            <th>Date</th>
            <th>Proof Payment</th>
            <th>Order Status</th>
            <th>Action</th>
        </tr>";

    foreach ($result as $row) {
        // Display each order details
        // ...

        echo "<tr>
                <td>{$row['order_number']}</td>
                <td>{$row['user_info']}</td>
                <td>{$row['method']}</td>
                <td>{$row['product_details']}</td>
                <td>₱{$row['total_price']}</td>
                <td>{$row['placed_on']}</td>
                <td>";

        // Display order type
        if ($row['order_type'] === 'dine-in') {
            echo "Dine-In";

            // Display table ID for dine-in orders
            echo "<br>";
            echo " (Table {$row['table_id']})";
        } else {
            echo $row['order_type'];
        }

        echo "</td>
                <td>{$row['time']}</td>
                <td>{$row['date']}</td>
                <td class='zoomable-image'>";

        // Display proof of payment image or a message if not available
        if (!empty($row['proof_payment'])) {
            echo "<img src='http://localhost/ChessBurger/proof_payment_uploads/{$row['proof_payment']}' alt='Proof of Payment'>";
        } else {
            echo 'No Proof of Payment';
        }

        echo "</td>
                <td>{$row['order_status']}</td>
                <td>
                    <form action='' method='POST'>
                        <input type='hidden' name='order_id' value='{$row['order_number']}'>
                        <select name='order_status' class='drop-down'>
                            <option value='' selected disabled>{$row['order_status']}</option>
                            <option value='confirmed'>Confirmed</option>
                            <option value='denied'>Denied</option>
                            <option value='preparing'>Preparing</option>
                            <option value='order-ready'>Order Ready</option>
                            <option value='out-for-delivery'>Out for Delivery</option>
                            <option value='completed'>Completed</option>
                        </select>
                        <div class='flex-btn'>
                            <input type='submit' value='Update' class='btn' name='update_order'>
                        </div>
                    </form>
                </td>
              </tr>";
    }

    echo "</table>";

    // Your pagination links here
    echo "<div class='pagination'>";
    for ($i = 1; $i <= $totalPages; $i++) {
        echo "<a href='?page=$i'>$i</a>";
    }
    echo "</div>";
} else {
    echo '<div class="empty-box">
            <p class="empty-message">No records found for the selected order status/type!</p>
        </div>
        ';
}
?>
</section>

<script>
    //this code for delete this has nothing to do the script here
    //<a href='placed_orders.php?delete={$row['order_number']}' class='remove-btn' onclick=\"return confirm('Delete this order?');\">Delete</a>

    document.addEventListener('DOMContentLoaded', function () {
        const zoomableImages = document.querySelectorAll('.zoomable-image img');
    
        zoomableImages.forEach(function(image) {
            image.addEventListener('click', function() {
                // Create a modal for the zoomed image
                const modal = document.createElement('div');
                modal.classList.add('image-modal');
    
                // Create an image element inside the modal
                const modalImage = document.createElement('img');
                modalImage.src = this.src;
                modalImage.alt = this.alt;
    
                // Add the modal image to the modal container
                modal.appendChild(modalImage);
    
                // Close modal when clicking outside the image
                modal.addEventListener('click', function() {
                    modal.remove();
                });
    
                // Append the modal to the body
                document.body.appendChild(modal);
            });
        });
    });

</script>

<!-- Custom JS file link -->
<script src="../js/admin_script.js"></script>

</body>
</html>
