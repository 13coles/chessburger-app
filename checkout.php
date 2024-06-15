<?php

include 'components/connect.php';

session_start();

// Check if the user is logged in
if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
   exit;
};

$user_id = $_SESSION['user_id'];

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch the user's profile information from the database based on user_id
$userProfileQuery = "SELECT name, address, number FROM users WHERE id = ?";
$userProfileStmt = $conn->prepare($userProfileQuery);
$userProfileStmt->execute([$user_id]);

// if else statement used here to execute both the true part and false part of given condition
if ($userProfileStmt->rowCount() > 0) {
    $userProfile = $userProfileStmt->fetch(PDO::FETCH_ASSOC);
    $user_name = $userProfile['name'];
    $user_address = $userProfile['address'];
    $user_number = $userProfile['number'];
} else {
    // Handle the case where the user's profile is not found
    $user_name = 'Default Name';
    $user_address = 'Default Address';
    $user_number = 'Default Number';
}

// Initialize variables with default values to avoid warnings
$name = $number = $method = $address = $i = $product_id = $product_name = $quantity = $price = $order_type = $order_id = $table_id = $date = $time = $img = '';

if(isset($_POST['submit'])){

   // Generate a unique order ID
   $order_number = sprintf("%s%09d", str_replace('.', '', microtime(true)), mt_rand(0, 999));
   
    // Handle form submission
    // htmlspecialchars() to sanitize input data
    $order_type = isset($_POST['order-type']) ? htmlspecialchars($_POST['order-type'], ENT_QUOTES, 'UTF-8') : '';
    $method = isset($_POST['method']) ? htmlspecialchars($_POST['method'], ENT_QUOTES, 'UTF-8') : '';

    // if order type is pick-up required only name, number, method, total products and total price
    if ($order_type === 'pick-up' && ($method === 'cash' || $method === 'gcash')) {
        $name = isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : '';
        $number = isset($_POST['number']) ? htmlspecialchars($_POST['number'], ENT_QUOTES, 'UTF-8') : '';
        $product_id = isset($_POST['product_id'][$i]) ? htmlspecialchars($_POST['product_id'][$i], ENT_QUOTES, 'UTF-8') : '';
        $product_name = isset($_POST['product_name'][$i]) ? htmlspecialchars($_POST['product_name'][$i], ENT_QUOTES, 'UTF-8') : '';
        $quantity = isset($_POST['quantity'][$i]) ? htmlspecialchars($_POST['quantity'][$i], ENT_QUOTES, 'UTF-8') : '';
        $price = isset($_POST['price'][$i]) ? htmlspecialchars($_POST['price'][$i], ENT_QUOTES, 'UTF-8') : '';
        $date = isset($_POST['date']) ? htmlspecialchars($_POST['date'], ENT_QUOTES, 'UTF-8') : '';
        $time = isset($_POST['time']) ? htmlspecialchars($_POST['time'], ENT_QUOTES, 'UTF-8') : '';

      
      // if order type is delivery required all
    } elseif ($order_type === 'delivery' && ($method === 'gcash' || $method === 'cod')) {
        $name = isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : '';
        $number = isset($_POST['number']) ? htmlspecialchars($_POST['number'], ENT_QUOTES, 'UTF-8') : '';
        $address = isset($_POST['address']) ? htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8') : '';
        $product_id = isset($_POST['product_id'][$i]) ? htmlspecialchars($_POST['product_id'][$i], ENT_QUOTES, 'UTF-8') : '';
        $product_name = isset($_POST['product_name'][$i]) ? htmlspecialchars($_POST['product_name'][$i], ENT_QUOTES, 'UTF-8') : '';
        $quantity = isset($_POST['quantity'][$i]) ? htmlspecialchars($_POST['quantity'][$i], ENT_QUOTES, 'UTF-8') : '';
        $price = isset($_POST['price'][$i]) ? htmlspecialchars($_POST['price'][$i], ENT_QUOTES, 'UTF-8') : '';
        $date = isset($_POST['date']) ? htmlspecialchars($_POST['date'], ENT_QUOTES, 'UTF-8') : '';
        $time = isset($_POST['time']) ? htmlspecialchars($_POST['time'], ENT_QUOTES, 'UTF-8') : '';      
   
      
      // if order type is dine-in required table_id and the rest same reuirements to pick-up
    } elseif ($order_type === 'dine-in' && ($method === 'cash' || $method === 'gcash')) {
        $name = isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : '';
        $number = isset($_POST['number']) ? htmlspecialchars($_POST['number'], ENT_QUOTES, 'UTF-8') : '';
        $table_id = isset($_POST['selected_table_id']) ? htmlspecialchars($_POST['selected_table_id'], ENT_QUOTES, 'UTF-8') : '';
        $product_id = isset($_POST['product_id'][$i]) ? htmlspecialchars($_POST['product_id'][$i], ENT_QUOTES, 'UTF-8') : '';
        $product_name = isset($_POST['product_name'][$i]) ? htmlspecialchars($_POST['product_name'][$i], ENT_QUOTES, 'UTF-8') : '';
        $quantity = isset($_POST['quantity'][$i]) ? htmlspecialchars($_POST['quantity'][$i], ENT_QUOTES, 'UTF-8') : '';
        $price = isset($_POST['price'][$i]) ? htmlspecialchars($_POST['price'][$i], ENT_QUOTES, 'UTF-8') : '';
        $date = isset($_POST['date']) ? htmlspecialchars($_POST['date'], ENT_QUOTES, 'UTF-8') : '';
        $time = isset($_POST['time']) ? htmlspecialchars($_POST['time'], ENT_QUOTES, 'UTF-8') : '';
       
    } else {
        // Handle the case where an invalid order type is selected.
        echo "Invalid order type selected.";
        exit;
    }

 
     // Check if the cart is not empty
      $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $check_cart->execute([$user_id]);

      if ($check_cart->rowCount() > 0) {
         // Insert order data into the database based on order type
         try {
             // Handle file upload for proof of payment
               $proof_payment = ''; // Initialize the variable

               if ($order_type === 'pick-up' || $order_type === 'delivery' || $order_type === 'dine-in') {
                  $payment_method = isset($_POST['method']) ? htmlspecialchars($_POST['method'], ENT_QUOTES, 'UTF-8') : '';

                  if (($payment_method !== 'cash' && $payment_method !== 'cod') && isset($_FILES['proof_payment']) && $_FILES['proof_payment']['error'] == 0) {
                     $targetDirectory = "proof_payment_uploads/"; // directory where to store the uploaded proof of payment screenshots
                     $targetFile = $targetDirectory . basename($_FILES["proof_payment"]["name"]);

                     // Check if the file is an image
                     $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                     $allowedExtensions = array("jpg", "jpeg", "png", "webp");

                     if (in_array($imageFileType, $allowedExtensions)) {
                           if (move_uploaded_file($_FILES["proof_payment"]["tmp_name"], $targetFile)) {
                              $proof_payment = basename($targetFile); // Set the proof_payment variable to the uploaded file name
                           } else {
                              echo "Error uploading the proof of payment file.";
                           }
                     } else {
                           echo "Only JPG, JPEG, PNG, and WEBP files are allowed for proof of payment.";
                     }
                  }
               }


     
               for ($i = 0; $i < count($_POST['product_id']); $i++) {
                  $product_id = $_POST['product_id'][$i];
                  $product_name = $_POST['product_name'][$i];
                  $quantity = $_POST['quantity'][$i];
                  $price = $_POST['price'][$i];
               
                  // Insert order data into the database based on order type.
                  if ($order_type === 'pick-up' && ($method === 'cash' || $method === 'gcash')) {
                  $insert_order = $conn->prepare("INSERT INTO `orders` (order_number, user_id, name, number, method, product_id, product_name, quantity, price, order_type, time, date, proof_payment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                  $insert_order->execute([$order_number, $user_id, $name, $number, $method, $product_id, $product_name, $quantity, $price, $order_type, $time, $date, $proof_payment]);

                  } elseif ($order_type === 'delivery' && ($method === 'gcash' || $method === 'cod')) {
                  $insert_order = $conn->prepare("INSERT INTO `orders` (order_number, user_id, name, number, method, address, product_id, product_name, quantity, price, order_type, time, date, proof_payment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                  $insert_order->execute([$order_number, $user_id, $name, $number, $method, $address, $product_id, $product_name, $quantity, $price, $order_type, $time, $date, $proof_payment]);

                  } elseif ($order_type === 'dine-in' && ($method === 'cash' || $method === 'gcash')) {
                  $insert_order = $conn->prepare("INSERT INTO `orders` (order_number, user_id, name, number, method, product_id, product_name, quantity, price, table_id, order_type, time, date, proof_payment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                  $insert_order->execute([$order_number, $user_id, $name, $number, $method, $product_id, $product_name, $quantity, $price, $table_id, $order_type, $time, $date, $proof_payment]);

                  // Insert reservation data for dine-in orders only.
                  $insert_reservation = $conn->prepare("INSERT INTO `reservations` (user_name, reservation_time, table_id, is_active, reservation_date) VALUES (?, ?, ?, 1, ?)");
                  $insert_reservation->execute([$name, $time, $table_id, $date]);
                  }
               }
               


         } catch (PDOException $e) {
             echo "Database Error: " . $e->getMessage();
         }
 
         // Delete the cart contents after placing the order
         $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
         $delete_cart->execute([$user_id]);
 
         // Provide a success message
         $message[] = 'Order placed successfully!';
     } else {
         $message[] = 'Your cart is empty';
     }
 }


   // fetch available tables
         $query = "SELECT t.table_id, t.table_name, t.capacity
         FROM tables t
         WHERE t.table_id NOT IN (
            SELECT r.table_id
            FROM reservations r
            WHERE r.is_active = 1
         )";

   $stmt = $conn->prepare($query);
   $stmt->execute();
   $availableTables = $stmt->fetchAll(PDO::FETCH_ASSOC);

 ?>    


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>
       <!-- fav-con  -->
       <link rel="icon" type="image/x-icon" href="images/chessburger logo.jpg">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="libraries/fontawesome-free/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<div class="heading">
   <h3>checkout</h3>
   <p><a href="home.php">home</a> <span> / checkout</span></p>
</div>

<div class="container">
    <section class="checkout">

      <!-- form action -->
      <form action="" method="POST" enctype="multipart/form-data">

         <div class="order-type">
            <h2> Select Order type</h2>
           
            <label>
               <input type="radio" name="order-type" id="dine-in" value="dine-in">
               Dine-in
            </label>
                     
         
            <label>
               <input type="radio" name="order-type" id="pick-up" value="pick-up">
               Pick-up
            </label>

            <label>
               <input type="radio" name="order-type" id="delivery" value="delivery">
               Delivery
            </label>

         </div>

         <div class="table-resevation">
            <h2> Select Table for Reservation</h2>
            <?php if (count($availableTables) > 0): ?>
               <table>
                     <tr>
                        <th>Select</th>
                        <th>Table Number</th>
                        <th>Capacity</th>
                        <!-- <th>Status</th> -->
                     </tr>
                     <?php foreach ($availableTables as $table): ?>
                        <tr>
                           <td>
                                 <input type="radio" name="selected_table_id" value="<?php echo $table['table_id']; ?>">
                           </td>
                           <td><?php echo $table['table_id']; ?></td>
                           <td><?php echo $table['capacity']; ?> person</td>
                           <!-- <td>Available</td> -->
                        </tr>
                     <?php endforeach; ?>
               </table>
            <?php else: ?>
               <p>No available tables at the moment. Please select other options.</p>
            <?php endif; ?>
         </div>   
            
         <div class="input-group">
            <h2>Personal Information:</h2>
            <div class="data-field">
                  <p>
                     <i class="fas fa-user"></i>
                     <input type="text" name="name" value="<?php echo htmlspecialchars($user_name); ?>" readonly>
                  </p>
            </div>
            <div class="data-field">
                  <p>
                     <i class="fas fa-phone"></i>
                     <input type="text" name="number" value="<?php echo $user_number; ?>" readonly>
                  </p>
            </div>
         </div>

         <div class="address">
            <h2 style="font-size: 18px; color: #333;"> Delivery Address:</h2>
            <p style="font-size: 15px; color: #555;">
               <i class="fas fa-map-marker-alt" style="font-size: 15px; color: #555; margin-right: 10px;"></i>
               <input type="text" name="address" value="<?php echo htmlspecialchars($user_address); ?>">

            </p>
            <br>
            <button id="updateAddressButton" style="background-color: #007BFF; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; text-decoration: none;">
               <a href="update_address.php" style="color: white; text-decoration: none;">Edit Address</a>
            </button>
         </div>

         <div class="date-time">
            <h2>Select Date & Time</h2>

            <input type="date" id="date" name="date"> 
            
            <input type="time" id="time" name="time">

            <br>
         </div>

         

         <div class="payment-method">
            <h2>Payment Method</h2>
         
            <label>
               <input type="radio" name="method" id="gcash" value="gcash">
               Gcash
            </label>
            <label class="cod" >
               <input type="radio" name="method" id="cod" value="cod">
               COD
            </label>
            <label class ="cash">
               <input type="radio" name="method" id="cash" value="cash">
              Pay in Cafe
            </label>

         </div>

         <div class="pay-via-gcash">
            <h2>Gcash number: 09454413739</h2>
            <img src="images/scan-me.jpg">
            <label for="proof_payment">Upload proof of payment:</label>
            <input type="file" name="proof_payment" id="proof_payment" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
         </div>

         <h1 class="title">Order Summary</h1>

         <!-- Hidden input fields for each cart item -->
         <?php
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);

         if ($select_cart->rowCount() > 0) {
            while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
               ?>
               <input type="hidden" name="product_id[]" value="<?php echo $fetch_cart['pid']; ?>">
               <input type="hidden" name="product_name[]" value="<?php echo $fetch_cart['name']; ?>">
               <input type="hidden" name="quantity[]" value="<?php echo $fetch_cart['quantity']; ?>">
               <input type="hidden" name="price[]" value="<?php echo $fetch_cart['price']; ?>">
               <?php
            }
         }
         ?>
         
         <!-- Cart items -->
         <table>
            <thead>
               <tr>
                  <th>Food Name</th>
                  <th>Quantity</th>
                  <th>Price</th>
               </tr>
            </thead>
            <tbody>
               <?php
               $grand_total = 0; // Initialize total amount

               $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
               $select_cart->execute([$user_id]);

               if ($select_cart->rowCount() > 0) {
                  while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                     $subtotal = $fetch_cart['price'] * $fetch_cart['quantity'];
                     $grand_total += $subtotal; // Update total amount
                     ?>
                     <tr>
                        <td><?= $fetch_cart['name']; ?></td>
                        <td><?= $fetch_cart['quantity']; ?></td>
                        <td>₱<?= $fetch_cart['price']; ?></td>
                     </tr>
                     <?php
                  }
               } else {
                  echo '<tr><td colspan="3"> Thank you for placing your orders!</td></tr>';
               }
               ?>
            </tbody>
            <tfoot>
               <tr>
                  <td colspan="2">Grand Total:</td>
                  <td>₱<?= $grand_total; ?>.00</td>
               </tr>
            </tfoot>
         </table>



         <input type="submit" name="submit" value="Place Order" class="btn">

      
      </form>
      
   </section>
</div>

<script src="libraries/jquery-3.7.1.js"></script> 

<script>
$(document).ready(function() {
  // Initially hide all sections
  $('.input-group, .address, .table-resevation').hide();
  $('.pay-via-gcash').hide();

  // Function to handle showing/hiding elements based on radio button selection
  function handleOrderTypeSelection() {
    // Hide all sections first
    $('.input-group, .address, .table-resevation, .cod, .cash').hide();

    if ($('input[name="order-type"][value="pick-up"]').prop('checked')) {
      // If 'Pick-up' is selected, show the input-group
      $('.input-group, .cash').show();
    } else if ($('input[name="order-type"][value="delivery"').prop('checked')) {
      // If 'Delivery' is selected, show the address
      $('.address, .input-group, .cod').show();
    } else if ($('input[name="order-type"][value="dine-in"]').prop('checked')) {
      // If 'Dine-in' is selected, show the input-group and table-reservation
      $('.input-group, .table-resevation, .cash').show();
    }
  }

  // Call the function initially and when order-type radio buttons change
  handleOrderTypeSelection();
  $('input[name="order-type"]').change(handleOrderTypeSelection);

  // Function to handle showing/hiding "Pay via Gcash" element
  function handlePaymentMethodSelection() {
    if ($('input[name="method"][value="gcash"]').prop('checked')) {
      // If 'Gcash' is selected, show the "Pay via Gcash" section
      $('.pay-via-gcash').show();
    } else if ($('input[name="method"][value="cash"]').prop('checked')) {
      // If any other payment method is selected or unchecked, hide the "Pay via Gcash" section
      $('.pay-via-gcash').hide();
    } else if ($('input[name="method"][value="cod"]').prop('checked')) {
      // If any other payment method is selected or unchecked, hide the "Pay via Gcash" section
      $('.pay-via-gcash').hide();
  }
  }
  // Call the function initially and when payment method radio buttons change
  handlePaymentMethodSelection();
  $('input[name="method"]').change(handlePaymentMethodSelection);
});



</script>












<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->






<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>