<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
};

if(isset($_POST['delete'])){
   $cart_id = $_POST['cart_id'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$cart_id]);
   $message[] = 'cart item deleted!';
}

if(isset($_POST['delete_all'])){
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);
   // header('location:cart.php');
   $message[] = 'deleted all from cart!';
}

if(isset($_POST['update_qty'])){
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $qty = htmlspecialchars($qty, ENT_QUOTES, 'UTF-8');
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$qty, $cart_id]);
   $message[] = 'cart quantity updated';
}


$grand_total = 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>cart</title>
    <!-- fav-con  -->
    <link rel="icon" type="image/x-icon" href="images/chessburger logo.jpg">
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<style>
   /* Style for the trash button */
   .trash-btn {
      background-color: #dc3545; 
      color: #fff; 
      border: none; 
      padding: 8px 12px; 
      cursor: pointer; 
      border-radius: 4px; 
      transition: background-color 0.3s ease; 
   }

   /* Style for the trash icon */
   .fa-trash-can {
      font-size: 18px; /* Adjust the icon size */
   }

   /* Hover effect for the button */
   .trash-btn:hover {
      background-color: #c82333; 
   }

</style>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<div class="heading">
   <h3>Food cart</h3>
   <p><a href="home.php">home</a> <span> / cart</span></p>
</div>

<!-- shopping cart section starts  -->
<section class="shopping-cart">
   <div class="cart-table-container"> 
      <table class="cart-table">
         <thead>
            <tr>
               <th>Products</th>
               <th>Sub-total</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
            <?php
               $grand_total = 0;
               $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
               $select_cart->execute([$user_id]);
               if ($select_cart->rowCount() > 0) {
                  while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                     $sub_total = $fetch_cart['price'] * $fetch_cart['quantity'];
                     $grand_total += $sub_total;
            ?>
            <tr>
               <td>
                  <div class="product-details">
                     <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="<?= $fetch_cart['name']; ?>" class="product-image">
                     <div class="name"><?= $fetch_cart['name']; ?></div>
                     <div class="price">₱<?= $fetch_cart['price']; ?></div>
                     <div class="quantity-update">
                        <form action="" method="post">
                           <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
                           <input type="number" name="qty" class="qty" min="1" max="99" value="<?= $fetch_cart['quantity']; ?>" maxlength="2">
                           <button type="submit" class="edit-button" name="update_qty">
                              <i class="fa-solid fa-pencil"></i>
                           </button>
                        </form>
                     </div>
                  </div>
               </td>
               <td>₱<?= $sub_total; ?></td>
               <td>
                  <div class="delete">
                     <form action="" method="post">
                        <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
                        <button type="submit" class="trash-btn" name="delete" onclick="return confirm('Delete this item?');">
                           <i class="fa-solid fa-trash-can"></i>
                        </button>
                     </form>
                  </div>
               </td>
            </tr>
            <?php
                  }
               } else {
                  echo '<tr><td colspan="3" class="empty">Your cart is empty</td></tr>';
               }
            ?>
         </tbody>
         <tfoot>
            <tr>
               <td class="cart-total" colspan="2">
                  <p>Cart Total: <span>₱<?= $grand_total; ?></span></p>
               </td>
               <td colspan="1">
                <form action="" method="post">
                     <button type="submit" class="del-btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>" name="delete_all" onclick="return confirm('Delete all from cart?');">Delete All</button>
                  </form>
               </td>
            </tr>
            <tr>
               <td colspan="1" class="actions">
               <a href="menu.php" class="continue-btn btn btn-primary">Buy more</a>  
               </td>
               <td colspan="2">
                  <a href="checkout.php" class="proceed-btn btn btn-success <?= ($grand_total > 1) ? '' : 'disabled'; ?>">Proceed to Checkout</a>
               </td>
            </tr>
         </tfoot>
      </table>
   </div>
</section>




<!-- shopping cart section ends -->










<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->








<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>