<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_message = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
   $delete_message->execute([$delete_id]);
   header('location:messages.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>messages</title>
     <!-- fav-con  -->
     <link rel="icon" type="image/x-icon" href="../images/chessburger logo.jpg">
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="libraries/fontawesome-free/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<style>
   .messages {
      margin: 20px;
   }

   .heading {
      background-color: black; /* Added black background */
      text-align: center;
      padding: 20px; /* Added padding for better visibility */
   }
   .heading h2{
    color: #fff;
    font-size: 24px;
   }

   .box-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-around;
      margin-top: 20px;
   }

   .box {
      width: 350px;
      border: 1px solid #000;
      margin: 10px;
      padding: 15px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s;
      margin-bottom: 20px;
      align-items: center;
   }

   .box:hover {
      transform: scale(1.02);
   }

   .box-header,
   .box-body,
   .box-footer {
      padding: 10px;
   }

   .box p {
      margin: 5px 0;
   }

   .box span {
      font-weight: bold;
   }

   .delete-btn {
      display: block;
      background-color: #ff5555;
      color: #fff;
      text-align: center;
      padding: 8px;
      text-decoration: none;
      margin-top: 10px;
      border-radius: 4px;
      transition: background-color 0.3s;
   }

   .delete-btn:hover {
      background-color: #ff3333;
   }
   .empty {
      text-align: center;
      font-style: italic;
   }
</style>

<body>

<?php include '../components/admin_header.php' ?>

<!-- messages section starts  -->
    <div class="heading">
        <h2>Messages</h2>
    </div>
    
<section class="messages">


   <div class="box-container">

   <?php
      $select_messages = $conn->prepare("SELECT * FROM `messages`");
      $select_messages->execute();
      if($select_messages->rowCount() > 0){
         while($fetch_messages = $select_messages->fetch(PDO::FETCH_ASSOC)){
   ?>
    <div class="box">
        <div class="box-header">
            <p><span>Name: <i><?= $fetch_messages['name']; ?></i></span></p>
            <p><span>Email: <?= $fetch_messages['email']; ?></span></span></p>
            <p><span>Number: <i><?= $fetch_messages['number']; ?></i></span></p>
        </div>
        <div class="box-body">
            <p><span>Message: <i><?= $fetch_messages['message']; ?></i></span></p>
        </div>
        <div class="box-footer">
            <a href="messages.php?delete=<?= $fetch_messages['id']; ?>" class="delete-btn" onclick="return confirm('delete this message?');">Delete</a>
        </div>
    </div>

   <?php
         }
      }else{
         echo '<p class="empty">you have no messages</p>';
      }
   ?>

   </div>

</section>

<!-- messages section ends -->









<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>