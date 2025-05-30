<?php
session_start();
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();

// check whether user login
if (!isset($_SESSION['user'])) {
    echo "<script> window.alert ('Please Login First') </script>";
    echo "<script> window.location= 'index.php' </script>";
}

// to choose user role from dropdown box
$select = "SELECT role_id, role_type FROM roles";
$query = mysqli_query($connection, $select);

$roleOptions = "";

while ($data = mysqli_fetch_array($query)) {
    $roleID = $data['role_id'];
    $roleType = $data['role_type'];
    $roleOptions .= "<option value='$roleID'>$roleType</option>";
}

// to choose department from dropdown box
$selectDep = "SELECT department_id, department_name FROM departments";
$queryDep = mysqli_query($connection, $selectDep);

$depOptions = "";

while ($dataDep = mysqli_fetch_array($queryDep)) {
    $depID = $dataDep['department_id'];
    $depName = $dataDep['department_name'];
    $depOptions .= "<option value='$depID'>$depName</option>";
}

// save user data 
if (isset($_POST['btnUserRegister'])) {
    $name = $_POST['txtName'];

    // **Handle File Upload Safely**
    if (isset($_FILES['userProfileImg']) && $_FILES['userProfileImg']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['userProfileImg']['tmp_name'];
        $fileName = $_FILES['userProfileImg']['name'];
        $folder = "user_images/";
        $userImg = $folder . "_" . basename($fileName);

        if (move_uploaded_file($fileTmpPath, $userImg)) {
            echo "<p>Profile uploaded successfully</p>";
        } else {
            echo "<p>Cannot Upload Profile</p>";
            exit();
        }
    } else {
        echo "<p>No file uploaded or an error occurred.</p>";
        exit();
    }

    $email = $_POST['txtEmail'];
    $phone = $_POST['txtPhone'];
    $cboRole = $_POST['cboRole'];
    $cboDep = $_POST['cboDep'];
    $password = $_POST['txtPassword'];
    $status = 'active';

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // hash the password for security

    // check that whether user email is already register
    $checkEmail = "SELECT * FROM users WHERE user_email= ?";
    $stmt = mysqli_prepare($connection, $checkEmail);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $countEmail = mysqli_num_rows($result);

    if ($countEmail > 0) {
        echo "<script> alert('This email is already registered'); window.location='register.php'; </script>";
    } else { // if email is not in use, save the user data in db
        $insert = "INSERT INTO users(role_id, department_id, user_name, user_email, user_phone, user_password, user_profile, account_status) 
                    VALUES (?,?,?,?,?,?,?,?)";
        $stmt = mysqli_prepare($connection, $insert);
        mysqli_stmt_bind_param($stmt, "iissssss", $cboRole, $cboDep, $name, $email, $phone, $hashedPassword, $userImg, $status);


        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            echo "<script> alert('User is successfully registered'); window.location='register.php'; </script>";
        } else {
            echo "<script> alert('Error in user registration'); </script>";
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quality Assurance | User Registration</title>
    <link rel="stylesheet" href="Style.css">
    <style>
        .valid {
            color: green;
        }

        .invalid {
            color: red;
        }

        .form-style {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 500px;
            background: #59417B;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 10px auto;
        }

        .form-style input[type="text"],
        .form-style input[type="email"],
        .form-style input[type="file"],
        .form-style input[type="password"],
        .form-style select {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-style label {
            font-weight: bold;
            color: #ffffff;
        }

        .form-style input {
            margin-bottom: 5px;
        }

        .btnUserRegister {
            background-color:rgb(111, 193, 171);
            color: #fff;
            font-weight: 600;
            padding: 12px;
            margin-top: 15px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btnUserRegister:hover {
            background-color:rgb(113, 217, 189);
        }

        .message {
            color: #eee;
            margin-top: 10px;
        }
        .admin-dash-link{
            color: rgb(114, 197, 235);
        }
        .admin-dash-link:hover{
            color: rgb(59, 184, 241);
        }
    </style>
    <script src="qa_script.js"></script>
</head>

<body>


    <form action="register.php" method="POST" enctype="multipart/form-data" class="form-style">
        <h3 style="margin: 7px 0px; color: #ffffff;">User Registration</h3>
        <p style="margin: 5px 0px; color: #e1e1e1;">Please fill in the form to register a new user.</p>
        <label>Name</label>
        <input type="text" name="txtName" placeholder="Full Name" required />

        <label>Profile Image</label>
        <input type="file" id="file" name="userProfileImg" required>

        <label>Email</label>
        <input type="email" name="txtEmail" placeholder="Email" required onkeyup="validateEmailFormat()">
        <p id="emailValidation"></p>

        <label>Phone</label>
        <input type="text" name="txtPhone" placeholder="Phone Number" required />

        <label>User Role</label>
        <select name="cboRole" class="register-cbo" required>
            <?php echo $roleOptions; ?>
        </select>

        <label>Department</label>
        <select name="cboDep" class="register-cbo" required>
            <?php echo $depOptions; ?>
        </select>

        <label>Password</label>
        <input type="password" name="txtPassword" placeholder="Password" required />

        <button type="submit" name="btnUserRegister" class="btnUserRegister">Register</button>
        <p class="message">Back to <a href="admin_home.php" class="admin-dash-link"><b>Admin Dashboard</b></a></p>
    </form>
</body>

</html>