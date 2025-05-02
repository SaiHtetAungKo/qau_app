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
if (isset($_GET['msg']) && $_GET['msg'] === 'disabled' && isset($_GET['name'])) {
    $name = htmlspecialchars($_GET['name']);
    echo "<div class='popup-message'>$name's account has been successfully disabled.</div>";
}
if (isset($_GET['msg']) && $_GET['msg'] === 'enabled') {
    $name = htmlspecialchars($_GET['name']);
    echo "<div class='popup-message'>$name's account has been successfully enabled.</div>";
}

// Fetch user data from session
$userName = $_SESSION['userName'];
$userProfileImg = $_SESSION['userProfile'] ?? 'default-profile.jpg'; // Default image if none is found

// Fetch staff users from the database using INNER JOIN
$query = "
        SELECT u.user_id, u.user_profile, u.user_name, u.user_email, u.user_phone, u.account_status,
            d.department_name, u.created_at
        FROM users u
        INNER JOIN roles r ON u.role_id = r.role_id
        INNER JOIN departments d ON u.department_id = d.department_id
        WHERE r.role_type = 'staff' AND u.account_status != 'deactivate'
    ";
$result = $connection->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Staff List | QA Manager</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: white;
            color: black;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar h2 {
            margin-bottom: 20px;
        }

        .logout {
            margin-top: auto;
            background: #3c9a72;
            padding: 12px;
            color: white;
            border: none;
            width: 100%;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
        }

        .logout:hover {
            background: rgb(89, 64, 122);
        }

        /* for successful disable staff acc msg */
        .popup-message {
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #4BB543;
            color: white;
            padding: 16px 28px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            font-size: 18px;
            opacity: 0;
            animation: fadeInOut 2s ease forwards;
        }

        @keyframes fadeInOut {
            0% {
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <div class="side-nav">
            <div class="logo text-center">
                <img src="Images/logo.png" alt="logo" width="150px" style="margin: 8px 0px;">
            </div>
            <a class="nav-link" href="qa_manager_dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a class="nav-link" href="qa_manager_home.php"><i class="fa-solid fa-layer-group"></i> Categories</a>
            <a class="nav-link" href="qa_manager_idea_summary.php"><i class="fa-regular fa-lightbulb"></i> Idea Reports</a>
            <a class="nav-link-active" href="qa_manager_staff_list.php"><i class="fa-solid fa-users"></i> Staff List</a>
            <a class="nav-link" href="qa_manager_hidden_idea_list.php"><i class="fa-regular fa-eye-slash"></i> Hidden Idea List</a>
            <a class="logout" href="logout.php" onclick="return confirm('Do You Want To Log Out?')">Log Out</a>
        </div>
        <div class="dash-section">
            <header class="dash-header">
                <div class="search-input">
                    <input type="hidden" placeholder="Search" aria-label="Search">
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
                                        <?php if ($row['account_status'] === 'disabled') { ?>
                                            <!-- Enable button -->
                                            <a href="enable_staff.php?id=<?php echo $row['user_id']; ?>"
                                                class="enable-staff-btn" title="Enable Account">
                                                Enable
                                            </a>
                                        <?php } else { ?>
                                            <!-- Disable button -->
                                            <a href="disable_staff.php?id=<?php echo $row['user_id']; ?>"
                                                class="disable-staff-btn" title="Disable Account">
                                                Disable
                                            </a>
                                        <?php } ?>
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