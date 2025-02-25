<?php
    session_start();
    include('connection.php');
    $connect = new Connect(); 
    $connection = $connect->getConnection(); 

     // check whether user login
    if (!isset($_SESSION['user'])) 
    {
        echo "<script> window.alert ('Please Login First') </script>";
        echo "<script> window.location= 'index.php' </script>";
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quality Assurance | Admin Home</title> 
</head>
<body>
    <h1>Hello Admin Home</h1>
    <a href="logout.php" onclick="return confirm('Do You Want To Log Out?')">Log Out</a> <br>
    <a href="register.php"><b>User Registration </b></a>    <br> 
    <a href="change_password.php"><b>Change Password</b></a>   <br>
</body>
</html>