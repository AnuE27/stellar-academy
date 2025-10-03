<?php
session_start();
include 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer classes
require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

// Initialize the step flag
if (!isset($_SESSION['step'])) {
    $_SESSION['step'] = 1; // Step 1: Email form
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'])) {
        // Step 1: Handle email submission and OTP generation
        $email = $_POST['email'];

        // Check if the email exists in either of the two tables
        $result_user = mysqli_query($conn, "SELECT * FROM tbl_stud WHERE Email = '$email'");
        $result_admin = mysqli_query($conn, "SELECT * FROM tbl_staff WHERE Email = '$email'");

        if (mysqli_num_rows($result_user) > 0 || mysqli_num_rows($result_admin) > 0) {
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $email;
            if (mysqli_num_rows($result_user) > 0) {
                $_SESSION['table'] = 'tbl_stud';
            } elseif (mysqli_num_rows($result_admin) > 0) {
                $_SESSION['table'] = 'tbl_staff';
            }

            // Send OTP to user's email
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'anu.22ubc215@mariancollege.org'; // Replace with your email address
                $mail->Password   = 'orll ixfi szjc agbq';    // Replace with your app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Recipients
                $mail->setFrom('anu.22ubc215@mariancollege.org', 'Stellar Tuition Academy');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = "Your OTP for Password Reset";
                $mail->Body    = "Greetings from Stellar Tuition Academy!!
                Your OTP for resetting your password is: " . $_SESSION['otp'];

                // Send the email
                $mail->send();
                echo "An OTP has been sent to your email address.";
                $_SESSION['step'] = 2; // Move to Step 2: OTP validation
            } catch (Exception $e) {
                echo "Failed to send OTP. Mailer Error: {$mail->ErrorInfo}";
            }

        } else {
            echo "Email not found!";
        }
    } elseif (isset($_POST['otp'])) {
        // Step 2: Handle OTP validation
        $entered_otp = $_POST['otp'];
        if ($entered_otp == $_SESSION['otp']) {
            $_SESSION['otp_validated'] = true;
            $_SESSION['step'] = 3; // Move to Step 3: New password
        } else {
            echo "Invalid OTP!";
        }
    } elseif (isset($_POST['password']) && isset($_SESSION['otp_validated'])) {
        // Step 3: Handle password reset
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        if ($password == $confirm_password) {
            $email = $_SESSION['email'];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Update the password in the correct table
            if ($_SESSION['table'] == 'tbl_stud') {
                mysqli_query($conn, "UPDATE tbl_stud SET password = '$hashed_password' WHERE Email = '$email'");
            } elseif ($_SESSION['table'] == 'tbl_staff') {
                mysqli_query($conn, "UPDATE tbl_staff SET password = '$hashed_password' WHERE Email = '$email'");
            }

            echo "Password reset successful! Go back to <a href='login.php'>Login</a>";
            session_destroy(); // Clear session data
        } else {
            echo "Passwords do not match!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <title>Password Reset</title>
    <link rel="stylesheet" href="forgotcss.css">
</head>
<body>
<header>
        <h2 class="logo">Stellar Tuition Academy</h2>
        <nav class="navigation">
            <a href="home2.html">Home</a>
            <a href="aboutus.html">About Us</a>
            <a href="http://localhost/mini%20project/admission.php">Admission</a>
           
            <button class="btnLogin-popup" onclick="window.location.href='login.php';">Login</button>
        </nav>
    </header>

    <div class="wrapper form-box">
    <?php


// Determine the step based on session variable
if ($_SESSION['step'] == 1) {
    echo '
        
        <form action="" method="POST">
        <h2>Forgot Password</h2>
            <div class="input-box">
                <label for="email">Enter your email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <input type="submit" value="Send OTP" class="btn">
        </form>
    ';
} elseif ($_SESSION['step'] == 2) {
    echo '
        
        <form action="" method="POST">
        <h2>Enter OTP</h2>
            <div class="input-box">
                <label for="otp">Enter the OTP sent to your email:</label>
                <input type="text" id="otp" name="otp" required>
            </div>
            <input type="submit" value="Validate OTP" class="btn">
        </form>
    ';
} elseif ($_SESSION['step'] == 3) {
    echo '
        
        <form action="" method="POST" onsubmit="return validateResetPassword()">
        <h2>Reset Password</h2>
            <div class="input-box">
                <label for="password">Enter new password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-box">
                <label for="confirm_password">Confirm new password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <input type="submit" value="Reset Password" class="btn">
        </form>
    ';
}
?>

    </div>

    <script>
        function validateResetPassword() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;

            var hasUpperCase = /[A-Z]/.test(password);
            var hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
            var hasNumber = /\d/.test(password);
            var hasMinimumLength = password.length >= 8;

            if (!(hasUpperCase && hasSpecialChar && hasNumber && hasMinimumLength)) {
                alert("Invalid Password. Password must contain a capital letter, special character, a number, and be at least 8 characters long.");
                return false;
            }

            if (password !== confirmPassword) {
                alert("Passwords do not match");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
