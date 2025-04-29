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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quality Assurance | Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="qa_script.js"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="admin-container">


        <!-- <div class="nav flex-column gap-3 px-2 py-5 mh-100">
            <div class="logo text-center">
                <h2>LOGO</h2>
            </div>
            <div class="d-flex flex-column">
                <a class="nav-link" href="register.php"><b>User Registration</b></a>
                <a class="nav-link" href="change_password.php"><b>Change Password</b></a>

                <a class="nav-link mt-auto bg-danger text-white rounded text-center" href="logout.php" onclick="return confirm('Do You Want To Log Out?')">Log Out</a>
            </div>
        </div> -->
        <div class="side-nav">
            <div class="logo text-center">
                <h2>LOGO</h2>
            </div>
            <a class="nav-link-active" href="admin_home.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a class="nav-link" href="staff_list.php"><i class="fa-solid fa-users"></i> Staff List</a>
            <a class="nav-link" href="request_idea.php"><i class="fa-regular fa-comment"></i> Request Idea</a>
            <a class="nav-link" href="idea_report.php"><i class="fa-regular fa-lightbulb"></i> Idea Reports</a>
            <a class="nav-link" href="register.php">User Registration</a>
            <a class="nav-link" href="change_password.php">Change Password</a>
            <a class="nav-link" href="department.php">Department</a>
            <a class=" logout" href="logout.php" onclick="return confirm('Do You Want To Log Out?')">Log Out</a>
        </div>
        <div class="dash-section">
            <header class="dash-header">
                <div class="search-input">
                    <input type="search" placeholder="Search" aria-label="Search">
                </div>
                <div class="user-display">
                    <img src="<?php echo htmlspecialchars($userProfileImg); ?>"
                        alt="Profile Image">
                    <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                </div>
            </header>
            <h3>Change Password</h3>
            <p>Please remember your password</p>

            <form action="change_password.php" method="POST">
                <input type="password" id="oldPswInput" name="txtOldPsw" placeholder="Old Password" required />
                <i class="fa-solid fa-eye-low-vision" onclick="passwordVisibility('oldPswInput', this)"></i>

                <input type="password" id="newPswInput" name="txtNewPsw" placeholder="New Password" required />
                <i class="fa-solid fa-eye-low-vision" onclick="passwordVisibility('newPswInput', this)"></i>

                <input type="password" id="confirmPswInput" name="txtConfirmPsw" placeholder="Confirm Password" required />
                <i class="fa-solid fa-eye-low-vision" onclick="passwordVisibility('confirmPswInput', this)"></i>

                <button id="btnChangePsw" name="btnChangePsw" class="btnChangePsw">
                    Change
                </button>

                <a href="admin_home.php"><b> Back to Home </b></a>
            </form>
        </div>
    </div>
</body>