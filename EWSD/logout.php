<?php
    session_start();
    include('connection.php');
    session_destroy();
    header("Location: index.php?success=logout");     // Redirect to login page
    exit();
?>