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

// Count for role_id = 2
$sql_role2 = "SELECT COUNT(*) as role2_count FROM users WHERE role_id = 2";
$result_role2 = mysqli_query($connection, $sql_role2);
$row_role2 = mysqli_fetch_assoc($result_role2);
$role2Count = $row_role2['role2_count'];

// Count for role_id = 3
$sql_role3 = "SELECT COUNT(*) as role3_count FROM users WHERE role_id = 3";
$result_role3 = mysqli_query($connection, $sql_role3);
$row_role3 = mysqli_fetch_assoc($result_role3);
$role3Count = $row_role3['role3_count'];

// Count for role_id = 4
$sql_role4 = "SELECT COUNT(*) as role4_count FROM users WHERE role_id = 4";
$result_role4 = mysqli_query($connection, $sql_role4);
$row_role4 = mysqli_fetch_assoc($result_role4);
$role4Count = $row_role4['role4_count'];

// Query count for department
$departmentsquery = "SELECT COUNT(*) as department_count FROM departments";
$result_departments = mysqli_query($connection, $departmentsquery);
$row_departments = mysqli_fetch_assoc($result_departments);
$departmentCount = $row_departments['department_count'];

$ideaquey = "
SELECT i.*, COUNT(iv.ideavoteID) AS like_count
FROM ideas i
LEFT JOIN idea_vote iv ON i.idea_id = iv.idea_id AND iv.votetype = 1
GROUP BY i.idea_id
ORDER BY like_count DESC
LIMIT 4
";

$result = mysqli_query($connection, $ideaquey);

$votequery = "
SELECT i.*,
    SUM(CASE WHEN iv.votetype = 1 THEN 1 ELSE 0 END) AS like_count,
    SUM(CASE WHEN iv.votetype = 2 THEN 1 ELSE 0 END) AS dislike_count
FROM ideas i
LEFT JOIN idea_vote iv ON i.idea_id = iv.idea_id
GROUP BY i.idea_id
ORDER BY like_count DESC
LIMIT 4
";

$result = mysqli_query($connection, $votequery);
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
<style>
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999;
    }

    .overlay-content {
        background: white;
        padding: 30px;
        border-radius: 10px;
        width: 70%;
        max-height: 90%;
        overflow-y: auto;
        position: relative;
    }

    .close-btn {
        position: absolute;
        right: 20px;
        top: 20px;
        cursor: pointer;
        font-size: 24px;
    }
</style>

<body>
    <div class="admin-container">

        <div class="side-nav">
            <div class="logo text-center">
                <img src="Images/logo.png" alt="logo" width="150px" style="margin: 8px 0px;">
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
                    <h2 class="welcome-text">Welcome to Open Gate University</h2>
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
                    <h1 id="role2Count" class="text-center" data-count="<?php echo $role2Count; ?>">0</h1>
                    <span class="text-center">QA Manager</span>
                </div>

                <!-- QA Coordinators (Role 3) -->
                <div class="box-card">
                    <h1 id="role3Count" class="text-center" data-count="<?php echo $role3Count; ?>">0</h1>
                    <span class="text-center">QA Coordinators</span>
                </div>

                <!-- Jobseekers (Role 4) -->
                <div class="box-card">
                    <h1 id="role4Count" class="text-center" data-count="<?php echo $role4Count; ?>">0</h1>
                    <span class="text-center">Staff</span>
                </div>
                <!-- Departments (Optional) -->
                <div class="box-card">
                    <h1 id="departmentCount" class="text-center" data-count="<?php echo $departmentCount; ?>">0</h1>
                    <span class="text-center">Departments</span>
                </div>

            </div>
            <h6 class="dash-title">Posted Request Data</h6>
            <div class="idea-show">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="idea-card">
                        <div class="idea-card-head">
                            <span class="idea-card-title"><?php echo htmlspecialchars($row['title']); ?></span>
                            <div class="idea-card-date">
                                <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                            </div>
                        </div>
                        <p class="idea-desc">
                            <?php echo htmlspecialchars(substr($row['description'], 0, 150)); ?>...
                        </p>
                        <div class="idea-show-bottom">
                            <a href="#" onclick="openOverlay(<?php echo $row['idea_id']; ?>)">Detail</a>
                            <div class="vote-show">
                                <span class="vote" id="like-<?php echo $row['idea_id']; ?>" onclick="handleVote(<?php echo $row['idea_id']; ?>, 1)">
                                    <?php echo $row['like_count']; ?> 👍
                                </span>
                                <span class="vote" id="unlike-<?php echo $row['idea_id']; ?>" onclick="handleVote(<?php echo $row['idea_id']; ?>, 2)">
                                    <?php echo $row['dislike_count']; ?> 👎
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div id="ideaOverlay" class="overlay" style="display:none;">
                <div class="overlay-content">
                    <span class="close-btn" onclick="closeOverlay()">×</span>
                    <div id="ideaDetailContent">
                        <!-- Idea detail will be loaded here with JS -->
                    </div>
                </div>
            </div>


        </div>

    </div>
    <script>
        function animateCount(id, duration = 1000) {
            const counter = document.getElementById(id);
            const target = +counter.getAttribute('data-count');
            const startTime = performance.now();

            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                counter.innerText = Math.floor(progress * target);

                if (progress < 1) {
                    requestAnimationFrame(update);
                } else {
                    counter.innerText = target;
                }
            }

            requestAnimationFrame(update);
        }

        document.addEventListener('DOMContentLoaded', function() {
            animateCount('role2Count', 500); // Clients
            animateCount('role3Count', 500); // QA Coordinators
            animateCount('role4Count', 500); // Jobseekers
            animateCount('departmentCount', 500); // Departments
        });
    </script>
    <script>
        function openOverlay(idea_id) {
            // Show overlay
            document.getElementById('ideaOverlay').style.display = 'flex';

            // Fetch idea detail using AJAX
            fetch('idea_detail.php?idea_id=' + idea_id)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('ideaDetailContent').innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        }

        function closeOverlay() {
            document.getElementById('ideaOverlay').style.display = 'none';
        }
    </script>




</body>

</html>