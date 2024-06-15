<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
    header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
        <!-- fav-con  -->
        <link rel="icon" type="image/x-icon" href="images/chessburger logo.jpg">

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="libraries/fontawesome-free/css/all.min.css">

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="css/style.css">
    <style>
    .box-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        margin-top: 20px;
    }

    .box {
        width: 300px; /* Increased the width for bigger boxes */
        border: 1px solid #000;
        margin: 10px;
        padding: 20px; /* Increased padding for more spacing */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s; /* Added smooth transition effect */
        margin-bottom: 20px; /* Added bottom margin to the box */
    }

    .box:hover {
        transform: scale(1.02);
    }

    .box h3 {
        margin-bottom: 10px;
        font-size: 15px;
    }

    .box p {
        margin: 5px 0 10px; 
        font-size: 15px;
    }

    .message {
        color: red;
        margin-top: 10px;
    }
    .box {
        transition: transform 0.2s;
        margin-bottom: 20px;
    }

    .box:hover {
        transform: scale(1.02);
    }

    .box h3 {
        margin-bottom: 10px;
    }

    .box p {
        margin: 5px 0 10px;
    }
    ol{
        font-size: 12px;
    }
</style>

   

</head>

<body>

    <!-- Header section starts -->
    <?php include 'components/user_header.php'; ?>
    <!-- Header section ends -->

    <div class="heading">
        <h3>Order History</h3>
        <p><a href="home.php">Home</a> <span>/ Orders</span></p>
    </div>

    <section class="orders">

        <div class="box-container">

            <?php
            if ($user_id == '') {
                echo '<p class="empty">Please login to see your orders</p>';
            } else {
                $select_orders = $conn->prepare("SELECT
                    order_number,
                    user_id,
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
                FROM
                    Orders
                WHERE
                    user_id = :user_id
                GROUP BY
                    order_number, user_id, user_info, method, placed_on,
                    order_status, table_id, order_type, time, date, proof_payment
                ORDER BY
                    date DESC");
                $select_orders->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $select_orders->execute();

                if ($select_orders->rowCount() > 0) {
                    while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
              ?>
                        <div class="box">
                            <h3>Placed On: <?= $fetch_orders['placed_on']; ?></h3>
                            <p><b>Order Number:</b>  <?= $fetch_orders['order_number']; ?></p>
                            <p><b>Payment Method:</b> <?= $fetch_orders['method']; ?></p>
                            <p><b>Order Type: </b>   <?= ($fetch_orders['order_type'] === 'dine-in') ? 'Dine-In (Table ' . $fetch_orders['table_id'] . ')' : $fetch_orders['order_type']; ?></p>
                            <p><b>Ordered Food:</b><ol> <?= $fetch_orders['product_details']; ?></ol></p>
                            <p><b>Amount: </b> ₱ <?= $fetch_orders['total_price']; ?></p>
                            <p><b>Date Selected: </b>  <?= $fetch_orders['date']; ?></p>
                            <p><b>Time Selected: </b>  <?= $fetch_orders['time']; ?></p>
                            <p><b>Order Status: </b> 
                                            <span style="color:<?php 
                                                if($fetch_orders['order_status'] == 'Order Placed' || 
                                                $fetch_orders['order_status'] == 'cancel' || 
                                                $fetch_orders['order_status'] == 'denied') {
                                                    echo 'red'; 
                                                } else {
                                                    echo 'green'; 
                                                }; 
                                            ?>">
                                                <?= $fetch_orders['order_status']; ?>
                                            </span>
                            </p>
                        </div>   
            <?php
                    }
                } else {
                    echo '<p class="empty">No orders placed yet!</p>';
                }
            }
            ?>

        </div>

    </section>

    <!-- Footer section starts -->
    <?php include 'components/footer.php'; ?>
    <!-- Footer section ends -->

    <!-- Custom JS file link -->
    <script src="js/script.js"></script>

</body>

</html>
