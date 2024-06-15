<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>
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
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<div class="heading">
   <h3>about us</h3>
   <p><a href="home.php">home</a> <span> / about</span></p>
</div>

<!-- about section starts  -->

<section class="about">

   <div class="row">

      <div class="image">
         <img src="images/chessburger_bg.jpg" alt="">
      </div>

      <div class="content">
         <h3>why choose us?</h3>
         <p>Chess Burger Cafe prides itself on being more than just a place to satisfy hunger; it aims to provide an exceptional dining experience.</p>
         <a href="menu.php" class="btn">our menu</a>
      </div>

   </div>

</section>

<!-- about section ends -->

<!-- steps section starts  -->

<section class="steps">

   <h1 class="title"> Service we offer</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/step-1.png" alt="">
         <h3> online ordering </h3>
         <p>🍔 Online Order: No lines, no wait – just pure convenience.
            </p>
      </div>

      <div class="box">
         <img src="images/step-2.png" alt="">
         <h3>fast delivery</h3>
         <p>🚀 Fast Delivery: We're not just about speed; we're about delivering smiles to your door.
         </p>
      </div>

      <div class="box">
         <img src="images/step-3.png" alt="">
         <h3> Quality foods</h3>
         <p>
         🌟 Quality Food: At Chess Burger Cafe, quality is our cornerstone.</p>
      </div>

   </div>

</section>

<!-- steps section ends -->

<!-- reviews section starts  -->

<section class="reviews">
   <h1 class="title">Our valued customer's </h1>
   <div class="swiper reviews-slider">
      <div class="swiper-wrapper">
         <div class="swiper-slide slide">
            <img src="images/c4.jpg" alt="">
         </div>
         <div class="swiper-slide slide">
            <img src="images/c5.jpg" alt="">
         </div>
         <div class="swiper-slide slide">
            <img src="images/customer3.jpg" alt="">
         </div>
         <div class ="swiper-slide slide">
            <img src="images/c8.jpg" alt="">
         </div>
         <div class="swiper-slide slide">
            <img src="images/c10.jpg" alt="">
         </div>
         <div class="swiper-slide slide">
            <img src="images/c9.jpg" alt="">
         </div>
      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>


<!-- reviews section ends -->


















<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->=






<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script src="libraries/swiper/swiper-bundle.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".reviews-slider", {
   loop:true,
   grabCursor: true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      0: {
      slidesPerView: 1,
      },
      700: {
      slidesPerView: 2,
      },
      1024: {
      slidesPerView: 3,
      },
   },
});

</script>

</body>
</html>