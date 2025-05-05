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
$userName = $_SESSION['userName'];
$userProfileImg = $_SESSION['userProfile'] ?? 'default-profile.jpg';

// Fetch user's department ID
$userQuery = "SELECT department_id FROM users WHERE user_id = ?";
$stmt = $connection->prepare($userQuery);
$stmt->bind_param('i', $userID);
$stmt->execute();
$userResult = $stmt->get_result();
$userData = $userResult->fetch_assoc();
$userDepartmentId = $userData['department_id'];

// Fetch staff users from the same department
$query = "
    SELECT u.user_id, u.user_profile, u.user_name, u.user_email, u.user_phone, 
           d.department_name, u.created_at
    FROM users u
    INNER JOIN roles r ON u.role_id = r.role_id
    INNER JOIN departments d ON u.department_id = d.department_id
    WHERE r.role_type = 'staff' AND u.department_id = ? AND u.account_status != 'deactivate'
    
";
$stmt = $connection->prepare($query);
$stmt->bind_param('i', $userDepartmentId);
$stmt->execute();
$result = $stmt->get_result();
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
                <img src="Images/logo.png" alt="logo" width="150px" style="margin: 8px 0px;">
            </div>
            <a class="nav-link" href="qa_coordinator_home.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a class="nav-link-active" href="qa_coordinator_staff_list.php"><i class="fa-solid fa-users"></i> Staff List</a>
            <a class="nav-link" href="qa_coordinator_idea_report.php"><i class="fa-regular fa-lightbulb"></i> Idea Reports</a>
            <!-- <a class="nav-link" href="qa_coordinator_request_idea.php"><i class="fa-regular fa-comment"></i> Request Idea</a> -->
            <!-- <a class="nav-link" href="qa_coordinator_idea_report.php"><i class="fa-regular fa-lightbulb"></i> Idea Reports</a> -->
            <a class="nav-link" href="qa_coordinator_annoucement.php"><i class="fa-regular fa-lightbulb"></i> Annoucement</a>
            <a class="logout" href="logout.php" onclick="return confirm('Do You Want To Log Out?')">Log Out</a>
        </div>
        <div class="dash-section">
            <header class="dash-header">
            <div class="search-input">
                    <h2 class="welcome-text">Dear Coordinator, Welcome to Open Gate University</h2>
                </div>  
                <div class="user-display">
                    <img src="<?php echo htmlspecialchars($userProfileImg); ?>" alt="Profile Image">
                    <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                </div>
            </header>
            <h6 class="dash-title">Staff List</h6>
            <div class="staff-table">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Department</th>
                                <th>Entry Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                    <td><img src="<?php echo htmlspecialchars($row['user_profile'] ?: 'default-profile.jpg'); ?>" alt="Profile" width="50"></td>
                                    <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['user_phone']); ?></td>
                                    <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    <td>
                                        <a href="editstaffForCoordinator.php?id=<?php echo $row['user_id']; ?>" class="edit-btn"><i class="fa-solid fa-pen-to-square"></i></a> |
                                        <a href="deleteStaffForCordinator.php?id=<?php echo $row['user_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')"><i class="fa-solid fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php if ($result->num_rows === 0) { ?>
                        <p>No staff found in your department.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>