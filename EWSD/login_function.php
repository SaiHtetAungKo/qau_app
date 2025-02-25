<?php
    session_start();
    include('connection.php'); 
    include('users_table.php');
    $connect = new Connect(); 
    $connection = $connect->getConnection(); 
    if (isset($_POST['txtEmail']) && isset($_POST['txtPassword'])) {
        $email = $_POST['txtEmail'];
        $password = $_POST['txtPassword'];
    } else {
        echo "<script>alert('Please enter your email and password.'); window.location='index.php';</script>";
        exit();
    }
    

    $userTable = new UsersTable(new Connect);
    $user=$userTable->checkEmailandPassword($email, $password);

    $select="SELECT * FROM users u, roles r, departments d 
            WHERE u.role_id=r.role_id AND u.department_id=d.department_id 
            AND user_email='$email'";
    $query=mysqli_query($connection,$select);
    $data=mysqli_fetch_array($query);  
    $userID=$data['user_id'];
    $userEmail=$data['user_email'];
    $userName=$data['user_name'];
    $userDepartment=$data['department_name'];
    $roleID=$data['role_id'];
    $userRole=$data['role_type'];

    if ($user) {  
        $_SESSION['user']=$user;
        $_SESSION['userID']=$userID;
        $_SESSION['userEmail']=$userEmail;
        $_SESSION['userName']=$userName;
        $_SESSION['userDepartment']=$userDepartment;
        $_SESSION['userRole']=$userRole;

        if ($roleID == '1') {
            echo "<script>alert('Successful Login!'); window.location='admin_home.php';</script>";
        }

        elseif ($roleID == '2')
        {
            echo "<script>alert('Successful Login!'); window.location='qa_manager_home.php';</script>";
        }

        elseif ($roleID == '3')
        {
            echo "<script>alert('Successful Login!'); window.location='qa_coordinator_home.php';</script>";
        }

        elseif ($roleID == '4')
        {
            echo "<script>alert('Successful Login!'); window.location='staff_home.php';</script>";
        } 
    } else {  
        header("location: index.php?fail=login");
    }


?>

