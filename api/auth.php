<?php
session_start();

if (!isset($_SESSION['id'])) {
    // If no session, force back to login
    header("Location: login.php");
    exit();
}
?>
