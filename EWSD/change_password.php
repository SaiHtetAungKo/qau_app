<?php
    session_start();
    include('users_table.php');
    // check whether user login
    if (!isset($_SESSION['user'])) 
    {
        echo "<script> window.alert ('Please Login First') </script>";
        echo "<script> window.location= 'index.php' </script>";
    }

    $userID = $_SESSION['userID'];
    if (isset($_POST['btnChangePsw'])) {
        $oldPsw = $_POST['txtOldPsw'];
        $newPsw = $_POST['txtNewPsw'];
        $confirmPsw = $_POST['txtConfirmPsw'];
                
        $userTable = new UsersTable(new Connect);
        $checkOldPsw=$userTable->checkUserOldPassword($userID, $oldPsw); // to validate whether old password is correct
        $checkNewPsw=$userTable->checkUserNewPassword($newPsw, $confirmPsw); // to validate whether new password and confirm password are the same

        if (!$checkOldPsw) {        
            echo "<script> alert('Your Old Password Is Incorrect'); window.location='change_password.php'; </script>";
        } 
        elseif (!$checkNewPsw) {     
            echo "<script> alert('New Password and Confirm Password Do Not Match'); window.location='change_password.php'; </script>";
        } 
        else {
            // If old and new passwords are correct, update the password
            $hashNewPassword = password_hash($newPsw, PASSWORD_DEFAULT);
            $updatedPsw = $userTable->updateNewPassword($userID, $hashNewPassword);

            if ($updatedPsw) {    
                session_unset();  
                session_destroy();
                echo "<script> alert('Password changed successfully'); window.location='index.php'; </script>";
            } 
            else {
                echo "<script> alert('Something went wrong, Cannot Change Password'); window.location='change_password.php'; </script>";
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quality Assurance | Change Password</title>  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="qa_script.js"></script>
</head>
<body>   
    <h3>Change Password</h3>
    <p>Please remember your password</p>

    <form action="change_password.php" method="POST">
        <input type="password" id="oldPswInput" name="txtOldPsw" placeholder="Old Password" required/>
        <i class="fa-solid fa-eye-low-vision" onclick="passwordVisibility('oldPswInput', this)"></i>

        <input type="password" id="newPswInput" name="txtNewPsw" placeholder="New Password" required/>
        <i class="fa-solid fa-eye-low-vision" onclick="passwordVisibility('newPswInput', this)"></i>

        <input type="password" id="confirmPswInput" name="txtConfirmPsw" placeholder="Confirm Password" required/>
        <i class="fa-solid fa-eye-low-vision" onclick="passwordVisibility('confirmPswInput', this)"></i>
                
        <button id="btnChangePsw" name="btnChangePsw" class="btnChangePsw">
            Change 
        </button>
       
        <a href="admin_home.php"><b> Back to Home </b></a>
    </form>    
</body>