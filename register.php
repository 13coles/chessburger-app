<?php
include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}

if(isset($_POST['submit'])) {
   $name = htmlspecialchars($_POST['name']);
   $email = htmlspecialchars($_POST['email']);
   $number = htmlspecialchars($_POST['number']);
   $pass = $_POST['pass'];
   $cpass = $_POST['cpass'];
   $address = htmlspecialchars($_POST['purok_street_name'].', '.$_POST['barangay_name'].', '.$_POST['municipality_city_name'].' - '. $_POST['zip_code']);

   // Additional checks for password length, complexity, etc. can be added here

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? OR number = ?");
   $select_user->execute([$email, $number]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if($select_user->rowCount() > 0){
      $message[] = 'Email or number already exists!';
   } else {
      if($pass != $cpass){
         $message[] = 'Confirm password not matched!';
      } else {
         // Use password_hash() instead of sha1
         $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

         $insert_user = $conn->prepare("INSERT INTO `users` (name, email, number, password, address) VALUES (?, ?, ?, ?, ?)");
         $insert_user->execute([$name, $email, $number, $hashed_password, $address]);

         // Redirect to login.php after successful registration
         header('location: login.php');
         exit(); // Make sure to exit after redirection to prevent further script execution
      }
   }
}

?>
<!-- ... rest of the HTML code remains the same ... -->




<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>
       <!-- fav-con  -->
       <link rel="icon" type="image/x-icon" href="images/chessburger logo.jpg">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
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
.form-container {
   background-color: #ffffff;
   border: 1px solid #ccc;
   border-radius: 8px;
   box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
   padding: 20px;
   text-align: center;
   max-width: 400px;
   margin-top: 50px;
   margin-bottom: 50px;
}

.form-container h3 {
   font-size: 24px;
   color: #333;
   margin-bottom: 20px;
}

/* Text input fields */
.box {
   width: 100%;
   padding: 10px;
   margin-bottom: 15px;
   border: 1px solid #ccc;
   border-radius: 5px;
   font-size: 16px;
   outline: none;
}

/* Submit button */
.btn {
   background-color: var(--violet);
   color: #fff;
   padding: 10px 20px;
   border: none;
   border-radius: 5px;
   cursor: pointer;
   font-size: 18px;
}

/* Login link */
p {
   margin-top: 20px;
   font-size: 16px;
   color: #333;
}

a {
   color: var(--violet);
   text-decoration: none;
}

/* Focus styles */
.box:focus {
   border-color: var(--violet);
   box-shadow: 0 0 5px rgba(220, 18, 190, 0.5);
}

/* Error message styles */
.error-message {
   color: red;
   margin-top: 5px;
   font-size: 14px;
}

/* Adjustments for smaller screens */
@media only screen and (max-width: 600px) {
   body::before {
      filter: blur(5px); /* Adjust the blur intensity for small screens */
   }
}

/* Adjustments for iPad/tablet screens */
@media only screen and (min-width: 601px) and (max-width: 1024px) {
   body::before {
      filter: blur(5px); /* Adjust the blur intensity for iPads/tablets */
   }
}

/* Adjustments for iPhone screens */
@media only screen and (max-width: 480px) {
   body::before {
      filter: blur(3px); /* Adjust the blur intensity for iPhone screens */
   }
}

</style>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<section class="form-container">
   <form action="" method="post">
      <h3>Registration</h3>
      <input type="text" name="name" required placeholder="Enter your full name" class="box" maxlength="50">
      <input type="email" name="email" required placeholder="Enter your email" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="number" name="number" required placeholder="Enter your number" class="box" min="0" max="9999999999" maxlength="11">
      <input type="password" name="pass" required placeholder="Enter your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="Confirm your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      
      <!-- Additional address fields -->
      <input type="text" class="box" placeholder="Purok/Street name" required maxlength="50" name="purok_street_name">
      <input type="text" class="box" placeholder="Barangay name" required maxlength="50" name="barangay_name">
      <input type="text" class="box" placeholder="Municipality/City name" required maxlength="50" name="municipality_city_name">
      <input type="number" class="box" placeholder="Zip code" required max="999999" min="0" maxlength="6" name="zip_code">
      
      <input type="submit" value="Register Now" name="submit" class="btn">
      <p>Already have an account? <a href="login.php">Login Now</a></p>
   </form>
</section>














</body>
</html>