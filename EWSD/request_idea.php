<?php
session_start();
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();

// Check whether user is logged in
if (!isset($_SESSION['userID'])) {
    echo "<script>
        alert('Please Login First');
        window.location = 'index.php';
    </script>";
    exit();
}

if (isset($_POST['btnPost'])) {
    $title = $_POST['txtTitle'];
    $closuredate = $_POST['closuredate'];
    $finalclosuredate = $_POST['finalclosuredate'];
    $desc = $_POST['txtDesc']; { // if email is not in use, save the user data in db
        $insert = "INSERT INTO request_ideas(title, description, closure_date, final_closure_date) 
                    VALUES (?,?,?,?)";
        $stmt = mysqli_prepare($connection, $insert);
        mysqli_stmt_bind_param($stmt, "ssss", $title, $desc, $closuredate, $finalclosuredate);


        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            echo "<script> alert('Request Idea is successfully Posted'); window.location='request_idea.php'; </script>";
        } else {
            echo "<script> alert('Error in user registration'); </script>";
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
}

// Fetch user data from session
$userName = $_SESSION['userName'];
$userProfileImg = $_SESSION['userProfile'] ?? 'default-profile.jpg'; // Default image if none is found
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Staff List | Admin Panel</title>
</head>

<body>
    <div class="admin-container">
        <div class="side-nav">
            <div class="logo text-center">
                <h2>LOGO</h2>
            </div>
            <a class="nav-link" href="admin_home.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a class="nav-link" href="staff_list.php"><i class="fa-solid fa-users"></i> Staff List</a>
            <a class="nav-link-active" href="request_idea.php">Request Idea</a>
            <a class="nav-link" href="idea_report.php"><i class="fa-regular fa-lightbulb"></i> Idea Reports</a>
            <a class="nav-link" href="register.php"><b>User Registration</b></a>
            <a class="nav-link" href="change_password.php"><b>Change Password</b></a>
            <a class="logout" href="logout.php" onclick="return confirm('Do You Want To Log Out?')">Log Out</a>
        </div>
        <div class="dash-section">
            <header class="dash-header">
                <div class="search-input">
                    <input type="search" placeholder="Search" aria-label="Search">
                </div>
                <div class="user-display">
                    <img src="<?php echo htmlspecialchars($userProfileImg); ?>" alt="Profile Image">
                    <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                </div>
            </header>
            <h6 class="dash-title">All Data</h6>
            <div class="request-form">
                <div class="form-title">
                </div>
                <form action="request_idea.php" method="POST">
                    <div class="input-flex-box">
                        <div class="input-box">
                            <label for="closure-date">Closure Date</label>
                            <input type="date" name="closuredate" placeholder="Closer Date">
                        </div>

                        <div class="input-box">
                            <label for="final-closure-date">Final Closure Date</label>
                            <input type="date" name="finalclosuredate" placeholder="Final Closer Date">
                        </div>
                    </div>
                    <div class="input-box">
                        <label for="title">Title</label>
                        <input type="text" name="txtTitle" placeholder="Closer Date">
                    </div>
                    <div class="input-box">
                        <label for="desc">Description</label>
                        <textarea type="text" name="txtDesc" placeholder="Closer Date" rows="7"></textarea>
                    </div>
                    <div class="action-btns">
                        <button type="reset" class="btn-reset">Reset</button>
                        <button type="submit" name="btnPost" class="post-btn">Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>


</html>