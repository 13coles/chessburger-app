<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/add_cart.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>
       <!-- fav-con  -->
       <link rel="icon" type="image/x-icon" href="images/chessburger logo.jpg">

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="libraries/swiper/swiper-bundle.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="libraries/fontawesome-free/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
   <style>
      .box i {
         color: black;
         transition: color 0.3s ease;
      }

      .box:hover i {
         color: white;
      }
   </style>
<body>

<?php include 'components/user_header.php'; ?>



<section class="hero">

   <div class="swiper hero-slider">

      <div class="swiper-wrapper">

         <div class="swiper-slide slide">
            <div class="content">
               <span>order online</span>
               <h3>crispy chicken</h3>
               <a href="menu.php" class="btn">see menus</a>
            </div>
            <div class="image">
               <img src="images/home-3.png" alt="crispy chicken">
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="content">
               <span>order online</span>
               <h3>chezzy hamburger</h3>
               <a href="menu.php" class="btn">see menus</a>
            </div>
            <div class="image">
               <img src="images/home-img-2.png" alt="chezzy hamburger">
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="content">
               <span>order online</span>
               <h3>refreshing drinks</h3>
               <a href="menu.php" class="btn">see menus</a>
            </div>
            <div class="image">
               <img src="images/drink-1.png" alt="refreshing drinks">
            </div>
         </div>

      </div>

      <div class="swiper-pagination"></div>

   </div>

</section>

<section class="category">

   <h1 class="title">Our Category</h1>

   <div class="box-container">

      <a href="category.php?category=Soups/Noodles" class="box">
         <i class="fas fa-plate-wheat fa-4x"></i>
         <h3>Soups/Noodles</h3>
      </a>

      <a href="category.php?category=Chicken joy" class="box">
         <i class="fas fa-drumstick-bite fa-4x"></i>
         <h3> Chicken joy</h3>
      </a>

      <a href="category.php?category=Beverages" class="box">
         <i class="fas fa-coffee fa-4x"></i>
         <h3>Beverages</h3>
      </a>

      <a href="category.php?category=Burgers/Fries" class="box">
         <i class="fas fa-hamburger fa-4x"></i>
         <h3>Burgers/Fries</h3>
      </a>

   </div>

</section>

<section class="products">

   <h1 class="title">Best Seller</h1>
   

   <div class="box-container">

      <?php
         $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 8");
         $select_products->execute();
         if($select_products->rowCount() > 0){
            while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
      ?>
      <form action="" method="post" class="box">
         <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
         <input type="hidden" name="name" value="<?= $fetch_products['name']; ?>">
         <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
         <input type="hidden" name="image" value="<?= $fetch_products['image']; ?>">
         <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
         <a href="category.php?category=<?= $fetch_products['category']; ?>" class="cat"><?= $fetch_products['category']; ?></a>
         <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
         <div class="name"><?= $fetch_products['name']; ?></div>
         <div class="flex">
            <div class="price"><span>₱</span><?= $fetch_products['price']; ?></div>
            <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
         </div>
      </form>
      <?php
            }
         }else{
            echo '<p class="empty">no products added yet!</p>';
         }
      ?>

   </div>

   <!-- <div class="more-btn">
      <a href="menu.php" class="btn">View all</a>
   </div> -->

</section>

<section class="how-to-order">


   <div class="box-container">

      <div class="box">
         <img src="images/step.png" alt=" how to order photo">
         
         
      </div>

   </div>

</section>



















<?php include 'components/footer.php'; ?>


<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script src="libraries/swiper/swiper-bundle.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".hero-slider", {
   loop:true,
   grabCursor: true,
   effect: "flip",
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   
});
// var swiper = new Swiper(".category-slider", {
//    loop:true,
//    grabCursor: true,
//    spaceBetween: 20,
//    pagination: {
//       el: ".swiper-pagination",
//       clickable:true,
//    },
//    breakpoints: {
//       220: {
//       slidesPerView: 2,
//       },
//       600: {
//       slidesPerView: 2,
//       },
//       700: {
//       slidesPerView: 3,
//       },
//       1024: {
//       slidesPerView: 4,
//       },
//    },
// });
</script>

</body>
</html>