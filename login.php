<?php
session_start();
include 'log1.php';
$conn = mysqli_connect("localhost", "root", "", "projectmanagement");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $Email = trim($_POST['Email']);
    $password = trim($_POST['password']);

    // === Handle Login ===
    if (isset($_POST['login'])) {
        $query = "SELECT u.UserID, u.Email, u.password, ui.userinfo_ID, ui.firstname, ui.middlename, ui.lastname, ui.PROFILE_PIC
          FROM userin u
          JOIN userinfo ui ON u.Email = ui.EMAIL
          WHERE u.Email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $Email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            if ($password === $row['password']) {
                $_SESSION['Email'] = $row['EMAIL'];
                $_SESSION['userinfo_ID'] = $row['userinfo_ID'];
                $fullname = $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'];
                $_SESSION['username'] = trim($fullname); // ✅ Now matches what's stored in assignment_students
                $_SESSION['profile_pic'] = $row['PROFILE_PIC'];
                logTransaction('user', $Email, 'LOGIN', 'User successfully logged in.');
                header('Location: dashboard.php');
                exit();
            } else {
                echo "<script>alert('Incorrect password.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('No user found with this email.'); window.history.back();</script>";
        }
    }

    // === Handle Registration ===
    elseif (isset($_POST['submit'])) {
        $confirm_password = trim($_POST['Conpassword']); // ✅ fixed typo

        if ($password !== $confirm_password) {
            echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        } else {
            $check_query = "SELECT * FROM userin WHERE Email = ?";
            $stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($stmt, "s", $Email);
            mysqli_stmt_execute($stmt);
            $check_result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($check_result) > 0) {
                echo "<script>alert('Email already registered. Please use a different one.'); window.history.back();</script>";
            } else {
                $sql = "INSERT INTO userin (Email, password) VALUES (?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ss", $Email, $password); // ❗ no hashing here — use in real apps!

                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['Email'] = $Email;
                    header("Location: userinfo.php"); // to collect more info
                    exit();
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>




<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-compatibel" content="IE-edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title> DreamBoard </title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="login.css">
</head>

<body>

<header>
<header>
    <div class="navbar">
      <img src="logo.png" alt="Logo" />
      <p>DreamBoard</p>
    </div>
  </header>
</header>

<div class="container">
  <!--signin-->

  <div class="signin-signup">
            
  <form method="POST" action="login.php" class="sign-in-form">
    <h2 class="title">LOG IN</h2>

    <div class= "input-field">
      <i class='bx bx-user-circle'></i>
      <input type="text" name="Email" placeholder="Email" required>
    </div> 

   <div class= "input-field">
     <i class='bx bxs-lock'></i>
     <input type="password" name="password" placeholder="password" required>
   </div> 
                
  <button type="submit" name="login" class="btn">lOGIN</button>

  <p class="social-text"> or sign in using: </p>

  <div class="social-media">

    <a href="https://www.facebook.com/" class="social-icon">
      <i class='bx bxl-facebook-circle' ></i>
    </a>

    <a href="https://www.instagram.com/accounts/login/?hl=en" class="social-icon">
      <i class='bx bxl-instagram-alt' ></i>
    </a>

   <a href="https://accounts.google.com.ph/" class="social-icon">
      <i class='bx bxl-google' ></i>
    </a>

    </div>

        <p class="account-text"> Dont have an acc? <a href="#" id="sign-up-btn2">Sign Up</a></p>
               
    </form>
               
               <!--signup-->
    <form method="POST" action="login.php" class="sign-up-form">
      <h2 class="title">SIGN UP</h2>

      <div class= "input-field">
        <i class='bx bx-user-circle'></i>
        <input type="text" name="Email" placeholder="Email" required>
      </div> 

      <div class= "input-field">
        <i class='bx bxs-lock' ></i>
        <input type="password" name="password" placeholder="password" required>
      </div> 

      <div class= "input-field">
        <i class='bx bxs-lock'></i>
        <input type="password" name="Conpassword" placeholder="Confirm password" required>
      </div> 

      <button type="submit" class="btn" name="submit">SIGN UP</button>

      <p class="social-text"> or sign in using: </p>

    <div class="social-media">
      <a href="https://www.facebook.com/" class="social-icon">
        <i class='bx bxl-facebook-circle' ></i>
      </a>

    <a href="https://www.instagram.com/accounts/login/?hl=en" class="social-icon">
      <i class='bx bxl-instagram-alt' ></i>
    </a>

    <a href="https://accounts.google.com.ph/" class="social-icon">
      <i class='bx bxl-google' ></i>
    </a>

    </div>
                 
    <p class="account-text"> already have an acc? <a href="#" id="sign-in-btn2">Sign In</a></p>
                 
    </form>

  </div>

  <div class="panels-container">

   <div class="panel left-panel">

     <div class="content">
     <center><img src="DreamBoard (1).png" height="150px" width="350px"></center>
       <h3> one of us?</h3>
       <p>Welcome back, traveler!  Come and see what new recipes are waiting for you!saddle up and 
          feast your way through a world of mouthwatering aromas tantalizing tastes, and delightful 
          recipes around the world await.
      </p>
      <button class="btn" id="sign-in-btn"> log in</button>
    </div>
    </div>

    <div class="panel right-panel">

      <div class="content">
        <center><img src="DreamBoard (1).png" height="150px" width="350px"></center>
        <h3> want to be one of us?</h3>
        <p>Welcome to our vibrant online menu, Hungry for more than just delicious dishes? 
          Create your free account today and feast your way through a world of 
          mouthwatering aromas   tantalizing tastes, and delightful recipes around the world await.</p>
        <button class="btn" id="sign-up-btn">  Sign Up</button>
      </div>
                
            </div>
   


  </div>


</div>
<script src="app.js"></script>
</body>

</html>