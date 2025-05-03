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

// Filter by role if set
$roleFilter = isset($_POST['roleFilter']) ? $_POST['roleFilter'] : '';

// Fetch staff users from the database
$query = "
    SELECT u.user_id, u.user_profile, u.user_name, u.user_email, u.user_phone, r.role_type, 
           d.department_name, u.created_at
    FROM users u
    INNER JOIN roles r ON u.role_id = r.role_id
    INNER JOIN departments d ON u.department_id = d.department_id
    WHERE u.account_status != 'deactivate'
";

if (!empty($roleFilter)) {
    $safeRole = $connection->real_escape_string($roleFilter);
    $query .= " AND r.role_type = '$safeRole'";
}

$query .= " ORDER BY r.role_type ASC";
$result = $connection->query($query);

// Fetch role options for filter
$rolesQuery = "SELECT DISTINCT role_type FROM roles ORDER BY role_type ASC";
$rolesResult = $connection->query($rolesQuery);
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
            <a class="nav-link" href="admin_home.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a class="nav-link-active" href="staff_list.php"><i class="fa-solid fa-users"></i> Staff List</a>
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
                    <img src="<?php echo htmlspecialchars($userProfileImg); ?>" alt="Profile Image">
                    <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                </div>
            </header>
            <form method="POST">
                <div class="staff-header">
                    <h6 class="dash-title">Staff List</h6>

                    <div class="filter-box">
                    <label for="roleFilter">Filter by Role:</label>
                    <select id="roleFilter" name="roleFilter" onchange="this.form.submit()">
                        <option value="">All Roles</option>
                        <?php while($role = mysqli_fetch_assoc($rolesResult)): ?>
                        <option value="<?php echo htmlspecialchars($role['role_type']); ?>"
                            <?php if(isset($_POST['roleFilter']) && $_POST['roleFilter'] === $role['role_type']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($role['role_type']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                    </div>
                </div>
            </form>

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
                                <th>Role</th>
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
                                    <td><?php echo htmlspecialchars($row['role_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    <td>
                                        <a href="editstaff.php?id=<?php echo $row['user_id']; ?>" class="edit-btn"><i class="fa-solid fa-pen-to-square"></i></a> |
                                        <a href="deletestaff.php?id=<?php echo $row['user_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure, you want to delete this staff?')"><i class="fa-solid fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</body>

</html>
