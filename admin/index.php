<?php
include '../components/connect.php';

session_start();

$message = [];

if(isset($_POST['submit'])){
   $name = $_POST['name'];
   $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
   $pass = sha1($_POST['pass']);
   $pass = htmlspecialchars($pass, ENT_QUOTES, 'UTF-8');

   $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
   $select_admin->execute([$name]);

   if($select_admin->rowCount() > 0){
      $fetch_admin = $select_admin->fetch(PDO::FETCH_ASSOC);

      if($fetch_admin['password'] === $pass) {
         $_SESSION['admin_id'] = $fetch_admin['id'];
         header('location:dashboard.php');
      } else {
         $message['pass'] = 'Incorrect password!';
      }
   } else {
      $message['name'] = 'Incorrect admin name!';
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
    <link rel="icon" type="image/x-icon" href="../images/chessburger logo.jpg">
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="libraries/fontawesome-free-6.4.2-web/css/all.min.css">
   <link rel="stylesheet" href="libraries/fontawesome-free/css/all.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
       body {
            font-family: Arial, sans-serif;
            position: relative;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-image: url('../images/chessburger_bg.jpg');
            background-size: cover;
            background-position: center;
            filter: blur(5px); /* Adjust the blur intensity as needed */
        }

        .form-container {
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            width: 300px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        h3 {
            text-align: center;
            color: #333;
        }

        .input-container {
            position: relative;
        }

        .icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 10px;
            color: #aaa;
        }

        .box {
            margin: 10px 0;
            padding: 10px 30px 10px 40px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            width: 227px;
        }

        .btn {
            background-color: #e312ea;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color:  #bd0cb4;
        }

        span.error {
            font-size: 12px;
            color: #cc0000; /* Red text color for error */
            margin-top: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<!-- admin login form section starts  -->

<section class="form-container">
    <form action="" method="POST">
        <h3>Welcome Back Admin!</h3>
        <div class="input-container">
            <i class="icon fas fa-user"></i>
            <input type="text" name="name" maxlength="20" required placeholder="Admin" class="box">
          
        </div>
             <?php
                if(isset($message['name'])){
                    echo '<span class="error">'.$message['name'].'</span>';
                }
            ?>

        <div class="input-container">
            <i id="togglePassword" class="icon fas fa-eye-slash" onclick="togglePassword()"></i>
            <input type="password" id="password" name="pass" maxlength="20" required placeholder="Password" class="box">
        </div>
        <?php
             if(isset($message['pass'])){
                echo '<span class="error">'.$message['pass'].'</span>';
             }
            ?>
        <input type="submit" value="Login Now" name="submit" class="btn">
    </form>
</section>

<!-- admin login form section ends -->
      <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const icon = document.getElementById("togglePassword");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        }
      </script>

</body>
</html>
