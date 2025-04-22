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

$userName = $_SESSION['userName'];
$userProfileImg = $_SESSION['userProfile'] ?? 'default-profile.jpg';

// Fetch all request ideas
$query = "SELECT requestIdea_id, title, description, closure_date, final_closure_date FROM request_ideas ORDER BY closure_date DESC";
$result = $connection->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Idea Reports | Admin Panel</title>
</head>

<body>
    <div class="admin-container">
        <div class="side-nav">
            <div class="logo text-center">
                <h2>LOGO</h2>
            </div>
            <a class="nav-link" href="qa_coordinator_home.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a class="nav-link" href="qa_coordinator_staff_list.php"><i class="fa-solid fa-users"></i> Staff List</a>
            <a class="nav-link" href="qa_coordinator_request_idea.php"><i class="fa-regular fa-comment"></i> Request Idea</a>
            <a class="nav-link-active" href="qa_coordinator_idea_report.php"><i class="fa-regular fa-lightbulb"></i> Idea Reports</a>
            <!-- <a class="nav-link" href="register.php"><b>User Registration</b></a>
            <a class="nav-link" href="change_password.php"><b>Change Password</b></a> -->
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
            <h6 class="dash-title">Idea Reports</h6>
            <div class="list-form">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="idea-list-form">
                        <div class="idea-box">
                            <div class="flex-box">
                                <span class="request-idea-title"><?php echo htmlspecialchars($row['title']); ?></span>
                                <span class="desc"><?php echo htmlspecialchars($row['description']); ?></span>
                            </div>
                            <div class="flex-box-1">
                                <span>Closure Date - <?php echo $row['closure_date']; ?></span>
                                <span>Final Closure Date - <?php echo $row['final_closure_date']; ?></span>
                            </div>
                        </div>
                        <!-- Fetch ideas related to this request -->
                        <?php
                        $requestIdea_id = $row['requestIdea_id'];
                        $ideasQuery = "SELECT title, description, anonymousSubmission FROM ideas WHERE requestIdea_id = ?";
                        $stmt = $connection->prepare($ideasQuery);
                        $stmt->bind_param("i", $requestIdea_id);
                        $stmt->execute();
                        $ideasResult = $stmt->get_result();
                        ?>

                        <div class="ideas-list">
                            <strong>Submitted Ideas:</strong>
                            <?php if ($ideasResult->num_rows > 0): ?>
                                <ul>
                                    <?php while ($idea = $ideasResult->fetch_assoc()): ?>
                                        <li>
                                            <strong><?php echo htmlspecialchars($idea['title']); ?></strong> -
                                            <?php echo htmlspecialchars($idea['description']); ?>
                                            <?php if ($idea['anonymousSubmission']): ?>
                                                <span class="anonymous-tag">(Anonymous Submission)</span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            <?php else: ?>
                                <div class="no-list">
                                    <img src="Images/no-list.png" alt="No-List">
                                    <h3>There is no ideas submitted yet!</h3>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    </div>
</body>

</html>