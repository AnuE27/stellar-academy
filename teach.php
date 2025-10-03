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
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="admincss.css">
</head>
<body>
    <!-- Top banner with title and logout button -->
    <div class="banner">
        <a id='title' href='teach.php'>Teacher Dashboard</a>
        <a class="logout" href="logout.php">Log Out</a>
    </div>

    <!-- Main content with the links in separate containers -->
    <div class="main-container">
        <div class="link-container">
            <a href="?page=att_management">Attendance Management</a>
        </div>
        <div class="link-container">
            <a href="?page=mark_management">Student Performance Management</a>
        </div>
        <div class="link-container">
            <a href="?page=schedule">View Schedule</a>
        </div>
    </div>

    <!-- Content area for displaying the pages -->
    <?php if (isset($_GET['page'])): ?>
        <div class="content-container">
            <?php
            // Determine which content to include based on the 'page' parameter
            switch ($_GET['page']) {
                case 'att_management':
                    include 'teach_attmgmt.php';
                    break;
                case 'mark_management':
                    include 'teach_markmgmt.php';
                    break;
                    case 'schedule':
                        include 'teach_sch.php';
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
