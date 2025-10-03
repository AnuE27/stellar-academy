<?php
session_start();
if(session_destroy()) {
    if (isset($_COOKIE['login'])) {
        setcookie("login", "", time() - 3600, "/");
    }
    header("Location: login.php");
}
?>
