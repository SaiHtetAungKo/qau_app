<?php
session_start();
include('connection.php');
include('users_table.php');

$connect = new Connect();
$connection = $connect->getConnection();
// check whether user login
if (!isset($_SESSION['user'])) {
    echo "<script> window.alert ('Please Login First') </script>";
    echo "<script> window.location= 'index.php' </script>";
}

$userID = $_SESSION['userID'];
if (isset($_POST['btnChangePsw'])) {
    $oldPsw = $_POST['txtOldPsw'];
    $newPsw = $_POST['txtNewPsw'];
    $confirmPsw = $_POST['txtConfirmPsw'];

    // Fetch user data from session
    $userName = $_SESSION['userName'];
    $userProfileImg = $_SESSION['userProfile'] ?? 'default-profile.jpg'; // Default image if none is found

    $userTable = new UsersTable(new Connect);
    $checkOldPsw = $userTable->checkUserOldPassword($userID, $oldPsw); // to validate whether old password is correct
    $checkNewPsw = $userTable->checkUserNewPassword($newPsw, $confirmPsw); // to validate whether new password and confirm password are the same

    if (!$checkOldPsw) {
        echo "<script> alert('Your Old Password Is Incorrect'); window.location='change_password.php'; </script>";
    } elseif (!$checkNewPsw) {
        echo "<script> alert('New Password and Confirm Password Do Not Match'); window.location='change_password.php'; </script>";
    } else {
        // If old and new passwords are correct, update the password
        $hashNewPassword = password_hash($newPsw, PASSWORD_DEFAULT);
        $updatedPsw = $userTable->updateNewPassword($userID, $hashNewPassword);

        if ($updatedPsw) {
            session_unset();
            session_destroy();
            echo "<script> alert('Password changed successfully'); window.location='index.php'; </script>";
        } else {
            echo "<script> alert('Something went wrong, Cannot Change Password'); window.location='change_password.php'; </script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <title>Quality Assurance | Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="qa_script.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    .password-form {
        max-width: 500px;
        margin: 30px auto;
        padding: 30px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        font-family: 'Segoe UI', sans-serif;
        position: relative;
    }

    .password-form h3 {
        text-align: center;
        margin-bottom: 10px;
        color: #333;
    }

    .password-form p {
        text-align: center;
        margin-bottom: 25px;
        font-size: 14px;
        color: #777;
    }

    .input-wrapper {
        max-width: 600px;
        position: relative;
        margin-bottom: 20px;
    }

    .input-wrapper input {
        width: 480px;
        padding: 12px 0px 12px 15px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 14px;
    }

    .input-wrapper i {
        position: absolute;
        top: 50%;
        right: 17px;
        transform: translateY(-50%);
        color: #999;
        cursor: pointer;
    }

    .btnChangePsw {
        width: 100%;
        padding: 12px;
        background-color: #0066cc;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .btnChangePsw:hover {
        background-color: #004b99;
    }

    .back-link {
        display: block;
        text-align: center;
        margin-top: 15px;
        color: #0066cc;
        text-decoration: none;
        font-weight: bold;
    }

    .back-link:hover {
        text-decoration: underline;
    }
</style>

<body>
    <form class="password-form" action="change_password.php" method="POST">
        <h3>Change Password</h3>
        <p>Please remember your password</p>
        <div style="display: flex; flex-direction:column;">
            <div class="input-wrapper">
                <input type="password" id="oldPswInput" name="txtOldPsw" placeholder="Old Password" required />
                <i class="fa-solid fa-eye-slash" onclick="passwordVisibility('oldPswInput', this)"></i>
            </div>

            <div class="input-wrapper">
                <input type="password" id="newPswInput" name="txtNewPsw" placeholder="New Password" required />
                <i class="fa-solid fa-eye-slash" onclick="passwordVisibility('newPswInput', this)"></i>
            </div>

            <div class="input-wrapper">
                <input type="password" id="confirmPswInput" name="txtConfirmPsw" placeholder="Confirm Password" required />
                <i class="fa-solid fa-eye-slash" onclick="passwordVisibility('confirmPswInput', this)"></i>
            </div>
        </div>
        <button id="btnChangePsw" name="btnChangePsw" class="btnChangePsw">
            Change Password
        </button>

        <a href="admin_home.php" class="back-link">← Back to Dashboard</a>
    </form>

</body>

</html>