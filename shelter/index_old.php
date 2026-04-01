<?php
session_start();

// Check if user is logged in and is a shelter
if (!isset($_SESSION['userName']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login__.php");
    exit();
}

// Redirect to shelter_home.php
header("Location: shelter_home.php");
exit();
?>
