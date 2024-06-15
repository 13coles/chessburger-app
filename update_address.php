<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
};

if(isset($_POST['submit'])){

   $purokStreetName = $_POST['purok/street_name'];
   $barangayName = $_POST['barangay_name'];
   $municipalityCityName = $_POST['municipality/city_name'];
   $zipCode = $_POST['zip_code'];

   // HTML special characters encoding
   $purokStreetName = htmlspecialchars($purokStreetName, ENT_QUOTES, 'UTF-8');
   $barangayName = htmlspecialchars($barangayName, ENT_QUOTES, 'UTF-8');
   $municipalityCityName = htmlspecialchars($municipalityCityName, ENT_QUOTES, 'UTF-8');
   $zipCode = htmlspecialchars($zipCode, ENT_QUOTES, 'UTF-8');

   $address = $purokStreetName . ', ' . $barangayName . ', ' . $municipalityCityName . ' - ' . $zipCode;

   $update_address = $conn->prepare("UPDATE `users` SET address = ? WHERE id = ?");
   $update_address->execute([$address, $user_id]);

   $message[] = 'address saved!';

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update address</title>
       <!-- fav-con  -->
       <link rel="icon" type="image/x-icon" href="images/chessburger logo.jpg">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="libraries/fontawesome-free/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<style>
   h3{
      margin-bottom: 10px;
   }
   .form-container {
    max-width: 600px;
    margin: 50px auto;
    padding: 20px;
    background-color: #f5f5f5;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.form-container h3 {
    text-align: center;
    color: #333;
}

.box {
    width: 100%;
    margin-bottom: 15px;
    padding: 10px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

.btn {
    width: 100%;
    padding: 10px;
    box-sizing: border-box;
    background-color: #e312ea;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn:hover {
    background-color: #e312ea;
}
</style>
<body>
   
<?php include 'components/user_header.php' ?>

<section class="form-container">

   <form action="" method="post">
      <h3>Your Address</h3>
      <input type="text" class="box" placeholder="purok/street name" required maxlength="50" name="purok/street name">
      <input type="text" class="box" placeholder="barangay name" required maxlength="50" name="barangay name">
      <input type="text" class="box" placeholder="municipality/city name" required maxlength="50" name="municipality/city name">
      <input type="number" class="box" placeholder="zip code" required max="999999" min="0" maxlength="6" name="zip_code">
      <input type="submit" value="save address" name="submit" class="btn">
   </form>

</section>










<?php include 'components/footer.php' ?>







<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>