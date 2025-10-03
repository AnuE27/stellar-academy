<?php
session_start();
include 'db.php';


// Check if there's an error message to display
$error_message = '';
if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']); // Clear the error message after displaying it
}


if (isset($_COOKIE['login'])) 
{
  $email = $_COOKIE['login'];

  $sql_stud = "SELECT * FROM tbl_stud WHERE Email = '$email' AND adm_status='1'";
  $result_stud = mysqli_query($conn, $sql_stud);

  if ($result_stud->num_rows == 1) 
  {
      
      $_SESSION['login'] = $email;
      header("Location: stud.php");
      exit;
  }

  $sql_staff = "SELECT * FROM tbl_staff WHERE Email = '$email'";
  $result_staff = mysqli_query($conn, $sql_staff);

  if ($result_staff->num_rows == 1) {
      
      $_SESSION['login'] = $email;
      $row = $result_staff->fetch_assoc();
      if ($row['Role'] == 'Admin') {
          header("Location: admin.php");
      } else {
          header("Location: teach.php");
      }
      exit; 
  }
}

?>
<html>
   <head>
     <title>Login Page</title>
     <link rel="stylesheet" href="loginstyle.css">
   </head>
   <body>
   <header>
        <h2 class="logo">Stellar Tuition Academy</h2>
        <nav class="navigation">
            <a href="home2.php">Home</a>
            <a href="aboutus.html">About Us</a>
            <a href="http://localhost/mini%20project/admission.php">Admission</a>
           
            <button class="btnLogin-popup" onclick="window.location.href='login.php';">Login</button>
        </nav>
    </header>
    <div class="wrapper">
      <div class="form-box login">
        <h2>Login</h2>
        <form action="logcheck.php" method='post'>
          <div class="input-box">
            <input type="email" name='email' required>
            <label>Email ID</label>
            <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
          </div>
          <div class="input-box">
            <input type="password" name='password' required>
            <label>Password</label>
            <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
          </div>
          <div class="remeber-forgot">
            <label><input type="checkbox" name='remember'>Remember me</label>&nbsp;
            <a href="forgotpass.php">Forgot Password</a>
          </div>
          <button type="submit" name='login' class="btn">Login</button>
          <div class="login-register">
            <p>Not a student?<a href="admission.php" class="register-link">Apply</a></p>
          </div>
        </form>
      </div>
    </div>
     <!-- Display the error message if it exists -->
     <?php if (!empty($error_message)): ?>
                <p style="color: red;"><?php echo $error_message; ?></p>
            <?php endif; ?>
         <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
         <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
   </body>
</html>