<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:index.php');
    exit();
}

$message = [];

if (isset($_POST['submit'])) {
    $name = htmlspecialchars($_POST['name']);
    $password = htmlspecialchars($_POST['pass']);
    $cpassword = htmlspecialchars($_POST['cpass']);
    $role = htmlspecialchars($_POST['role']);

    try {
        // Check for duplicate username
        $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
        $select_admin->execute([$name]);

        if ($select_admin->rowCount() > 0) {
            $message['name'] = 'user name already exists!';
        } else {
            if ($password !== $cpassword) {
                $message['cpass'] = 'Confirm password not matched!';
            } else {
                $insert_admin = $conn->prepare("INSERT INTO `admin` (name, password, role) VALUES (?, ?, ?)");
                $insert_admin->execute([$name, sha1($cpassword), $role]);
                $message[] = 'New admin registered!';
           }
       }
   } catch (PDOException $e) {
       $message['general'] = 'Error: ' . $e->getMessage();
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
     <!-- fav-con  -->
     <link rel="icon" type="image/x-icon" href="../images/chessburger logo.jpg">
    <!-- Font Awesome CDN link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="libraries/fontawesome-free/css/all.min.css">

    <!-- Custom CSS file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

    <style>
        .registration {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            position: relative;
        }
        .registration::before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: url('../images/chessburger_bg.jpg') center center/cover no-repeat; 
            filter: blur(5px);
            z-index: -1;
        }
        form {
            width: 300px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .input-register {
            margin-bottom: 15px;
            position: relative;
        }

        label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
        }

        .box {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .warning {
            font-size: 12px;
            color: #cc0000;
            margin-top: 10px;
        }

        .input-register .btn {
            width: 100%;
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
            background-color: #bd0cb4;
        }

        .password-toggle {
            font-size: 12px;
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
        }

        .role-container {
            position: relative;
            margin-top: 15px;
        }

        .role-container select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            appearance: none;
        }

        .role-icon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            pointer-events: none;
        }
        i{
            margin-top: 10px;
        }

        .error {
            font-size: 12px;
            color: #cc0000; 
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- Register admin section starts -->
<section class="registration">
    <form action="" method="POST">
        <h2>Register New Account</h2>
        <div class="input-register">
            <label for="name">Username:</label>
            <input type="text" name="name" maxlength="20" required placeholder="Enter your username" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        </div>
                                <?php
                                    if (isset($message['name'])) {
                                        echo '<span class="error">' . $message['name'] . '</span>';
                                    }

                                 ?>

        <div class="input-register">
            <label for="pass">Password:</label>
            <input type="password" name="pass" id="password" maxlength="20" required placeholder="Enter your password" class="box">
            <i class="fas fa-eye-slash password-toggle" onclick="togglePassword()"></i>
        </div>

        <div class="input-register">
            <label for="cpass">Confirm Password:</label>
            <input type="password" name="cpass" maxlength="20" required placeholder="Confirm your password" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        </div>
                                    <?php
                                        if (isset($message['cpass'])) {
                                            echo '<span class="error">' . $message['cpass'] . '</span>';
                                        }
                                    ?>
        <div class="input-register role-container">
            <label for="role">Role:</label>
            <select name="role" required>
                <option value="admin">Admin</option>
                <option value="chief">Chief</option>
                <option value="cashier">Cashier</option>
                <option value="waiter">Waiter</option>
            </select>
            <div class="role-icon">
                <i class="fas fa-caret-down"></i>
            </div>
        </div>

        <div class="input-register">
            <input type="submit" value="Register Now" name="submit" class="btn">
        </div>
    </form>
</section>
<!-- Register admin section ends -->

<script>
    function togglePassword() {
        const passwordInput = document.getElementById("password");
        const icon = document.querySelector(".password-toggle i");

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
