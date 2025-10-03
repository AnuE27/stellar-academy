<?php
session_start();
include 'db.php'; 

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql_stud = "SELECT * FROM tbl_stud WHERE Email = '$email' AND adm_status='1'";
    $result_stud = mysqli_query($conn, $sql_stud);

    if ($result_stud->num_rows == 1) {
        $row = $result_stud->fetch_assoc();
        if (password_verify($password, $row['Password'])) {
            $_SESSION['login'] = $email; 
            if (!empty($_POST["remember"])) {
                setcookie("login", $email, time() + (86400 * 30), "/");
            } else {
                setcookie("login", "", time() - 3600, "/");
            }
            header("Location: stud.php");
            exit;
        } else {
            $_SESSION['error'] = "Invalid password.";
            header("Location: login.php");
            exit;
        }
    }

    $sql_staff = "SELECT * FROM tbl_staff WHERE Email = '$email'";
    $result_staff = mysqli_query($conn, $sql_staff);

    if ($result_staff->num_rows == 1) {
        $row = $result_staff->fetch_assoc();
        if (password_verify($password, $row['Password'])) {
            $_SESSION['login'] = $email;
            if (!empty($_POST["remember"])) {
                setcookie("login", $email, time() + (86400 * 30), "/");
            } else {
                setcookie("login", "", time() - 3600, "/");
            }
            if ($row['Role'] == 'Admin') {
                header("Location: admin.php");
            } else {
                header("Location: teach.php");
            }
            exit;
        } else {
            $_SESSION['error'] = "Invalid password.";
            header("Location: login.php");
            exit;
        }
    }

    $_SESSION['error'] = "No user found.";
    header("Location: login.php");
    exit;
}

mysqli_close($conn);
?>
