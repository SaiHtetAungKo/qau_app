<?php
session_start();
include('connection.php');
include('users_table.php');

$connect = new Connect();
$connection = $connect->getConnection();

if (!isset($_POST['txtEmail']) || !isset($_POST['txtPassword'])) {
    header("location: index.php?error=empty");
    exit();
}

$email = trim($_POST['txtEmail']);
$password = trim($_POST['txtPassword']);

$userTable = new UsersTable(new Connect);
$user = $userTable->checkEmailandPassword($email, $password);

$select = "SELECT * FROM users u, roles r, departments d 
           WHERE u.role_id=r.role_id AND u.department_id=d.department_id 
           AND user_email='$email'";
$query = mysqli_query($connection, $select);
$data = mysqli_fetch_array($query);

if ($data) {
    $userID = $data['user_id'];
    $userEmail = $data['user_email'];
    $userName = $data['user_name'];
    $userProfileImg = $data['user_profile'];
    $userDepartment = $data['department_name'];
    $roleID = $data['role_id'];
    $userRole = $data['role_type'];

    if ($user) {
        $_SESSION['user'] = $user;
        $_SESSION['userID'] = $userID;
        $_SESSION['userEmail'] = $userEmail;
        $_SESSION['userName'] = $userName;
        $_SESSION['userDepartment'] = $userDepartment;
        $_SESSION['userRole'] = $userRole;
        $_SESSION['userProfile'] = $userProfileImg;

        if ($roleID == '1') {
            header("location: admin_home.php");
        } elseif ($roleID == '2') {
            header("location: qa_manager_dashboard.php");
        } elseif ($roleID == '3') {
            header("location: qa_coordinator_home.php");
        } elseif ($roleID == '4') {
            header("location: staff_home_2.php");
        }
    } else {
        // Incorrect password
        header("location: index.php?error=password&email=" . urlencode($email));
        exit();
    }
} else {
    // Email doesn't exist
    header("location: index.php?error=email&email=" . urlencode($email));
    exit();
}
