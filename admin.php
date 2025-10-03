<?php
session_start();

if (isset($_SESSION['login']) && isset($_COOKIE['login'])) {
    $_SESSION['login'] = $_COOKIE['login'];
}

if (!isset($_SESSION['login'])) {
    header("location: login.php");
    die();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admincss.css">
</head>
<body>
    <!-- Top banner with title and logout button -->
    <div class="banner">
        <a id='title' href='admin.php'>Admin Dashboard</a>
        <a class="logout" href="logout.php">Log Out</a>
    </div>

    <!-- Main content with the links in separate containers -->
    <div class="main-container">
        <div class="link-container">
            <a href="?page=student_management">Student Management</a>
        </div>
        <div class="link-container">
            <a href="?page=teacher_management">Teacher Management</a>
        </div>
        <div class="link-container">
            <a href="?page=fee_management">Fee Management</a>
        </div>
        <div class="link-container">
            <a href="?page=schedule_management">Schedule Management</a>
        </div>
    </div>

    <!-- Content area for displaying the pages -->
    <?php if (isset($_GET['page'])): ?>
        <div class="content-container">
            <?php
            // Determine which content to include based on the 'page' parameter
            switch ($_GET['page']) {
                case 'student_management':
                    include 'admin_studmgmt.php';
                    break;
                case 'teacher_management':
                    include 'admin_staffmgmt.php';
                    break;
                case 'fee_management':
                    include 'admin_feemgmt.php';
                    break;
                case 'schedule_management':
                    include 'admin_schmgmt.php';
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
