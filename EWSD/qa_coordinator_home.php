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

// Fetch user data from session
$userName = $_SESSION['userName'];
$userProfileImg = $_SESSION['userProfile'] ?? 'default-profile.jpg'; // Default image if none is found
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Quality Assurance | Admin Home</title>
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
            <a class="nav-link-active" href="qa_coordinator_home.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a class="nav-link" href="qa_coordinator_staff_list.php"><i class="fa-solid fa-users"></i> Staff List</a>
            <!-- <a class="nav-link" href="qa_coordinator_request_idea.php"><i class="fa-regular fa-comment"></i> Request Idea</a> -->
            <!-- <a class="nav-link" href="qa_coordinator_idea_report.php"><i class="fa-regular fa-lightbulb"></i> Idea Reports</a> -->
            <a class="nav-link" href="qa_coordinator_annoucement.php"><i class="fa-regular fa-lightbulb"></i> Annoucement</a>   
            <!-- <a class="nav-link" href="register.php"><b>User Registration</b></a> -->
            <!-- <a class="nav-link" href="change_password.php"><b>Change Password</b></a> -->
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
            <h6 class="dash-title">All Data</h6>
            <div class="data-box">
                <div class="box-card">
                    <h3 class="text-center">Count</h3>
                    <span class="text-center">Hello</span>
                </div>
                <div class="box-card">
                    <h3 class="text-center">Count</h3>
                    <span class="text-center">Hello</span>
                </div>
                <div class="box-card">
                    <h3 class="text-center">Count</h3>
                    <span class="text-center">Hello</span>
                </div>
                <div class="box-card">
                    <h3>Count</h3>
                    <span>Hello</span>
                </div>

            </div>
            <h6 class="dash-title">Posted Request Data</h6>
            <div class="idea-show">
                <div class="idea-card">
                    <div class="idea-card-head">
                        <span class="idea-card-title">Title</span>
                        <div class="idea-card-date">
                            11/11/2025
                        </div>
                    </div>
                    <p class="idea-desc">Lorem ipsum dolor sit amet consectetur adipisicing elit. Pariatur,
                        molestias excepturi dignissimos praesentium odit officiis eius adipisci vero deleniti rerum asperiores,
                        amet alias suscipit porro consequuntur saepe laudantium dicta! Distinctio.</p>
                    <a href="#">Detail</a>
                </div>
                <div class="idea-card">
                    <div class="idea-card-head">
                        <span class="idea-card-title">Title</span>
                        <div class="idea-card-date">
                            11/11/2025
                        </div>
                    </div>
                    <p class="idea-desc">Lorem ipsum dolor sit amet consectetur adipisicing elit. Pariatur,
                        molestias excepturi dignissimos praesentium odit officiis eius adipisci vero deleniti rerum asperiores,
                        amet alias suscipit porro consequuntur saepe laudantium dicta! Distinctio.</p>
                    <a href="#">Detail</a>
                </div>
                <div class="idea-card">
                    <div class="idea-card-head">
                        <span class="idea-card-title">Title</span>
                        <div class="idea-card-date">
                            11/11/2025
                        </div>
                    </div>
                    <p class="idea-desc">Lorem ipsum dolor sit amet consectetur adipisicing elit. Pariatur,
                        molestias excepturi dignissimos praesentium odit officiis eius adipisci vero deleniti rerum asperiores,
                        amet alias suscipit porro consequuntur saepe laudantium dicta! Distinctio.</p>
                    <a href="#">Detail</a>
                </div>
                <div class="idea-card">
                    <div class="idea-card-head">
                        <span class="idea-card-title">Title</span>
                        <div class="idea-card-date">
                            11/11/2025
                        </div>
                    </div>
                    <p class="idea-desc">Lorem ipsum dolor sit amet consectetur adipisicing elit. Pariatur,
                        molestias excepturi dignissimos praesentium odit officiis eius adipisci vero deleniti rerum asperiores,
                        amet alias suscipit porro consequuntur saepe laudantium dicta! Distinctio.</p>
                    <a href="#">Detail</a>
                </div>

            </div>

        </div>

    </div>
</body>

</html>