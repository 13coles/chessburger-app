<?php
include 'components/connect.php';

session_start();

// Initialize $user_id to an empty string
$user_id = '';

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
}

if (isset($_POST['send'])) {
   $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
   $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
   $number = htmlspecialchars($_POST['number'], ENT_QUOTES, 'UTF-8');
   $msg = htmlspecialchars($_POST['msg'], ENT_QUOTES, 'UTF-8');

   // Check if the user exists
   $check_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
   $check_user->execute([$user_id]);

   if ($check_user->rowCount() === 0) {
      // Handle the case where the user_id does not exist in the users table
      $message[] = 'Invalid user ID!';
   } else {
      // Proceed with the message insertion
      $insert_message = $conn->prepare("INSERT INTO `messages` (user_id, name, email, number, message) VALUES (?, ?, ?, ?, ?)");
      $insert_message->execute([$user_id, $name, $email, $number, $msg]);

      $message[] = 'Sent message successfully!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>contact</title>
       <!-- fav-con  -->
       <link rel="icon" type="image/x-icon" href="images/chessburger logo.jpg">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
</head>

<body>

   <!-- header section starts  -->
   <?php include 'components/user_header.php'; ?>
   <!-- header section ends -->

   <div class="heading">
      <h3>contact us</h3>
      <p><a href="home.php">home</a> <span> / contact</span></p>
   </div>

   <!-- contact section starts  -->

   <section class="contact">

      <div class="row">

         <div class="image">
            <img src="images/contact-img.svg" alt="">
         </div>

         <form action="" method="post">
            <h3>For inquiries message Us!</h3>
            <input type="text" name="name" maxlength="50" class="box" placeholder="enter your name" required>
            <input type="number" name="number" min="0" max="9999999999" class="box" placeholder="enter your number" required maxlength="10">
            <input type="email" name="email" maxlength="50" class="box" placeholder="enter your email" required>
            <textarea name="msg" class="box" required placeholder="enter your message" maxlength="500" cols="30" rows="10"></textarea>
            <input type="submit" value="send message" name="send" class="btn">
         </form>

      </div>

   </section>

   <!-- contact section ends -->

   <!-- footer section starts  -->
   <?php include 'components/footer.php'; ?>
   <!-- footer section ends -->

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>
