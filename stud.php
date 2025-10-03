<?php
session_start();
include 'db.php';

if (isset($_SESSION['login']) && isset($_COOKIE['login'])) {
    $_SESSION['login'] = $_COOKIE['login'];
}

if (!isset($_SESSION['login'])) {
    header("location: login.php");
    die();
}

// Check admission fee status only if the alert has not been shown before
if (!isset($_SESSION['admission_fee_checked'])) {
    $email = $_SESSION['login'];
    
    // Query to check if the admission fee (F_ID = 8001) is unpaid
    $query = "SELECT s.S_ID
              FROM tbl_stud s
              LEFT JOIN tbl_feepayment f ON s.S_ID = f.S_ID AND f.F_ID = 8001
              WHERE s.Email = '$email' AND f.F_ID IS NULL";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        echo "<script>alert('Your admission fee is unpaid. Please pay it as soon as possible.');</script>";
    }

    // Set session variable to avoid showing the alert again
    $_SESSION['admission_fee_checked'] = true;
}

?>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="admincss.css">
</head>
<body>
    <!-- Top banner with title and logout button -->
    <div class="banner">
        <a id='title' href='stud.php'>Student Dashboard</a>
        <a class="logout" href="logout.php">Log Out</a>
    </div>
    <div class="main-container">
        <div class="link-container">
            <a href="?page=attend_view">Attendance Overview</a>
        </div>
        <div class="link-container">
            <a href="?page=perform_view">Performance Overview</a>
        </div>
        <div class="link-container">
            <a href="?page=schedule_view">Schedule Overview </a>
        </div>
        <div class="link-container">
            <a href="?page=fee_pay">Fee Payment</a>
        </div>
    </div>
    <?php if (isset($_GET['page'])): ?>
        <div class="content-container">
            <?php
            // Determine which content to include based on the 'page' parameter
            switch ($_GET['page']) {
                case 'attend_view':
                    include 'attend_view.php';
                    break;
                case 'perform_view':
                    include 'perform_view.php';
                    break;
                case 'schedule_view':
                    include 'schedule_view.php';
                    break;
                case 'fee_pay':
                    include 'fee_pay.php';
                    break;
                default:
                    echo "<p>Page not found.</p>";
                    break;
            }
            ?>
        </div>
    <?php endif; ?>
</body>
</html>
