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
$userID = $_SESSION['userID'];
// Fetch user data from session
$userName = $_SESSION['userName'];
$userProfileImg = $_SESSION['userProfile'] ?? 'default-profile.jpg'; // Default image if none is found
// 
// Get department ID from database
$query = "SELECT department_id FROM users WHERE user_ID = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  $departmentID = $row['department_id'];
  // You can now use $departmentID anywhere on this page
} else {
  // Handle error if no department found
  echo "<script>alert('User department not found');</script>";
}

// // Count for role_id = 2
// $sql_role2 = "SELECT COUNT(*) as role2_count FROM users WHERE role_id = 2";
// $result_role2 = mysqli_query($connection, $sql_role2);
// $row_role2 = mysqli_fetch_assoc($result_role2);
// $role2Count = $row_role2['role2_count'];

// // Count for role_id = 3
// $sql_role3 = "SELECT COUNT(*) as role3_count FROM users WHERE role_id = 3";
// $result_role3 = mysqli_query($connection, $sql_role3);
// $row_role3 = mysqli_fetch_assoc($result_role3);
// $role3Count = $row_role3['role3_count'];

// Count for role_id = 4
$sql_role4 = "SELECT COUNT(*) as role4_count FROM users WHERE role_id = 4 && department_id = $departmentID";
$result_role4 = mysqli_query($connection, $sql_role4);
$row_role4 = mysqli_fetch_assoc($result_role4);
$role4Count = $row_role4['role4_count'];



// Query count for department
$departmentsquery = "SELECT COUNT(*) as department_count FROM departments";
$result_departments = mysqli_query($connection, $departmentsquery);
$row_departments = mysqli_fetch_assoc($result_departments);
$departmentCount = $row_departments['department_count'];

// Ideas
$topQuery = "SELECT 
        mc.MainCategoryTitle,
        sc.SubCategoryTitle,
        i.idea_id,
        i.title AS idea_title,
        i.description AS idea_description,
        i.status AS idea_status,
        i.anonymousSubmission,
        i.created_at AS idea_created_at,
        i.updated_at AS idea_updated_at,
        dp.department_name AS department_name,
        COUNT(DISTINCT ic.ideacommentID) AS comment_count,
        COUNT(DISTINCT iv.ideavoteID) AS total_votes,
        SUM(CASE WHEN iv.votetype = 1 THEN 1 ELSE 0 END) AS upvotes,
        SUM(CASE WHEN iv.votetype = 2 THEN 2 ELSE 0 END) AS downvotes,
        GROUP_CONCAT(ic.ideacommentText SEPARATOR '||') AS comment_texts,
        GROUP_CONCAT(ic.created_at SEPARATOR '||') AS comment_dates
    FROM 
        maincategory mc
    JOIN 
        subcategory sc ON mc.MainCategoryID = sc.MainCategoryID
    LEFT JOIN 
        ideas i ON sc.SubCategoryID = i.SubCategoryID
    LEFT JOIN 
        idea_comment ic ON i.idea_id = ic.idea_id
    LEFT JOIN 
        idea_vote iv ON i.idea_id = iv.idea_id
    LEFT JOIN 
        users u ON u.user_id = i.userID
    LEFT JOIN 
        departments dp ON dp.department_id = u.department_id
    WHERE 
        dp.department_id = '$departmentID'
    GROUP BY 
        mc.MainCategoryTitle,
        sc.SubCategoryTitle,
        i.idea_id
    ORDER BY 
        -- mc.MainCategoryTitle, 
        -- sc.SubCategoryTitle, 
        i.created_at DESC";

$topResult = mysqli_query($connection, $topQuery);
$ideas = [];
while ($row = mysqli_fetch_assoc($topResult)) {
    $ideas[] = $row;
}

$ideaquey = "
SELECT i.*, COUNT(iv.ideavoteID) AS like_count
FROM ideas i
LEFT JOIN idea_vote iv ON i.idea_id = iv.idea_id AND iv.votetype = 1
LEFT JOIN 
        users u ON u.user_id = i.userID
        LEFT JOIN 
        departments dp ON dp.department_id = u.department_id
        WHERE 
        dp.department_id = '$departmentID'
GROUP BY i.idea_id
ORDER BY like_count DESC
LIMIT 4
";

$result2 = mysqli_query($connection, $ideaquey);

$votequery = "
SELECT i.*,
    SUM(CASE WHEN iv.votetype = 1 THEN 1 ELSE 0 END) AS like_count,
    SUM(CASE WHEN iv.votetype = 2 THEN 1 ELSE 0 END) AS dislike_count
FROM ideas i
LEFT JOIN idea_vote iv ON i.idea_id = iv.idea_id
LEFT JOIN 
        users u ON u.user_id = i.userID
        LEFT JOIN 
        departments dp ON dp.department_id = u.department_id
        WHERE 
        dp.department_id = '$departmentID'
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
                <img src="Images/logo.png" alt="logo" width="150px" style="margin: 8px 0px;">
            </div>
            <a class="nav-link-active" href="qa_coordinator_home.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a class="nav-link" href="qa_coordinator_staff_list.php"><i class="fa-solid fa-users"></i> Staff List</a>
            <a class="nav-link" href="qa_coordinator_idea_report.php"><i class="fa-regular fa-lightbulb"></i> Idea Reports</a>
            <a class="nav-link" href="qa_coordinator_annoucement.php"><i class="fa-regular fa-lightbulb"></i> Announcements</a>
           
            <a class=" logout" href="logout.php" onclick="return confirm('Do You Want To Log Out?')">Log Out</a>
        </div>
        <div class="dash-section">
            <header class="dash-header">
                <div class="search-input">
                    <input type="hidden" placeholder="Search" aria-label="Search">
                </div>
                <div class="user-display">
                    <img src="<?php echo htmlspecialchars($userProfileImg); ?>"
                        alt="Profile Image">
                    <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                </div>
            </header>
            <h6 class="dash-title">All Data</h6>
            <div class="data-box">
                <!-- <div class="box-card">
                    <h1 id="role2Count" class="text-center" data-count="<?php echo $role2Count; ?>">0</h1>
                    <span class="text-center">QA Manager</span>
                </div> -->

                <!-- QA Coordinators (Role 3) -->
                <!-- <div class="box-card">
                    <h1 id="role3Count" class="text-center" data-count="<?php echo $role3Count; ?>">0</h1>
                    <span class="text-center">QA Coordinators</span>
                </div> -->

                <!-- Jobseekers (Role 4) -->
                <div class="box-card">
                    <h1 id="role4Count" class="text-center" data-count="<?php echo $role4Count; ?>"><?php echo $role4Count; ?></h1>
                   
                    <span class="text-center">Staff</span>
                </div>
                <!-- Departments (Optional) -->
                <!-- <div class="box-card">
                    <h1 id="departmentCount" class="text-center" data-count="<?php echo $departmentCount; ?>">0</h1>
                    <span class="text-center">Departments</span>
                </div> -->

            </div>
            <h6 class="dash-title">Posted Request Data</h6>
            <div class="idea-show">
            <?php foreach ($ideas as $idea): ?>
                    <div class="idea-card">
                        <div class="idea-card-head">
                     
                            <span class="idea-card-title"><?php echo htmlspecialchars($idea['department_name']); ?></span>
                            <!-- <span class="idea-card-title"><?php echo htmlspecialchars($idea['SubCategoryTitle']); ?></span> -->
                            <!-- <div class="idea-card-date">
                                <?php echo date('d/m/Y', strtotime($idea['created_at'])); ?>
                            </div> -->
                        </div>
                        <p class="idea-desc">
                            <?php echo htmlspecialchars(substr($idea['idea_title'], 0, 150)); ?>...
                        </p>
                        <div class="idea-show-bottom">
                            <a href="#" onclick="openOverlay(<?php echo $idea['idea_id']; ?>)">Detail</a>
                            <div class="vote-show">
                                <span class="vote" id="like-<?php echo $idea['idea_id']; ?>" onclick="handleVote(<?php echo $idea['idea_id']; ?>, 1)">
                                    <?php echo $idea['upvotes']; ?> 👍
                                </span>
                                <span class="vote" id="unlike-<?php echo $idea['idea_id']; ?>" onclick="handleVote(<?php echo $idea['idea_id']; ?>, 2)">
                                    <?php echo $idea['downvotes']; ?> 👎
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
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