<?php
include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}

if(isset($_POST['submit'])){
   $email = htmlspecialchars($_POST['email']);
   $pass = $_POST['pass'];

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]); 
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if($select_user->rowCount() > 0) {
      // these lines for debugging
      echo "Hashed Password from Database: " . $row['password'] . "<br>";
      echo "Password entered: " . $pass . "<br>";
      
      if(password_verify($pass, $row['password'])) {
         $_SESSION['user_id'] = $row['id'];
         header('location: home.php');
         exit(); // exit after redirection to prevent further script execution
      } else {
         $message[] = 'Password verification failed.';
      }
   } else {
      $message[] = 'User not found.';
   }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login</title>
       <!-- fav-con  -->
       <link rel="icon" type="image/x-icon" href="images/chessburger logo.jpg">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="libraries/fontawesome-free-6.4.2-web/css/all.css">
   <link rel="stylesheet" href="libraries/fontawesome-free/css/all.css">
   <link rel="stylesheet" href="libraries/fontawesome-free/css/all.min.css">
   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<style>
   body {
      position: relative;
      height: 100vh;
      margin: 0;
   }

   body::before {
   content: "";
   position: fixed;
   top: 0;
   left: 0;
   width: 100%;
   height: 100%;
   z-index: -1;
   background-image: url('images/chessburger_bg.jpg');
   background-size: cover;
   filter: blur(5px);
}

   .form-login {
      background-color: #ffffff;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      padding: 20px;
      text-align: center;
      max-width: 400px;
      margin-top: 100px;
      margin-bottom: 20px;
   }

   .form-login h3 {
      font-size: 24px;
      color: #333;
      margin-bottom: 20px;
   }

   .box {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
      outline: none;
   }

   .btn {
      background-color: var(--violet);
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 18px;
   }

   p {
      margin-top: 20px;
      font-size: 16px;
      color: #333;
   }

   a {
      color: var(--violet);
      text-decoration: none;
   }

   .box:focus {
      border-color: var(--violet);
      box-shadow: 0 0 5px rgba(220, 18, 190, 0.5);
   }

   .error-message {
      color: red;
      margin-top: 5px;
      font-size: 14px;
   }

   @media (max-width: 768px) {
      body::before {
         filter: blur(5px);
      }
   }

   @media (max-width: 600px) {
      body::before {
         filter: blur(5px);
      }
   }

   @media (min-width: 601px) and (max-width: 1024px) {
      body::before {
         filter: blur(5px);
      }
   }

   @media (max-width: 480px) {
      body::before {
         filter: blur(3px);
      }
   }
   @media only screen and (max-width: 414px) and (max-height: 736px) {
   body::before {
      background-size: contain;
   }
}

</style>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<section class="form-login">

      <form action="" method="post">
         <h3>Login Now</h3>

         <div class="input-container">
            <input type="email" name="email" required placeholder="Enter your email" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
           
         </div>

         <div class="input-container">
            <input type="password" name="pass" required placeholder="Enter your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
          
         </div>

         <input type="submit" value="Login Now" name="submit" class="btn">
         <p>Don't have an account? <a href="register.php">Register Now</a></p>
      </form>


</section>




















</body>
</html>