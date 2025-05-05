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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $deptName = trim($_POST['department_name'] ?? '');
    $deptLocation = trim($_POST['department_location'] ?? '');
    $status = 'Active';
    $now = date('Y-m-d H:i:s');

    if (!empty($deptName) && !empty($deptLocation)) {
        try {
            $stmt = $connection->prepare("INSERT INTO departments (department_name, department_location, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$deptName, $deptLocation, $status, $now, $now]);

            echo "<script>
                alert('Department Added Successfully');
                window.location = 'department.php';
            </script>";
            exit();
        } catch (PDOException $e) {
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    } else {
        echo "<script>alert('Please fill in all fields');</script>";
    }
}

// // Handle status toggle
// if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['toggle_status'])) {
//     $deptId = (int) $_POST['department_id'];
//     $currentStatus = $_POST['current_status'] === 'Active' ? 'Closed' : 'Active';
//     $now = date('Y-m-d H:i:s');

//     $query = "UPDATE departments SET status = '$currentStatus', updated_at = '$now' WHERE department_id = $deptId";
//     $connection->query($query);
//     // Redirect to prevent form resubmission
//     header("Location: department.php");
//     exit();
// }
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
        <div class="side-nav">
            <div class="logo text-center">
                <img src="Images/logo.png" alt="logo" width="150px" style="margin: 8px 0px;">
            </div>
            <a class="nav-link" href="admin_home.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a class="nav-link" href="staff_list.php"><i class="fa-solid fa-users"></i> Staff List</a>
            <a class="nav-link" href="request_idea.php"><i class="fa-regular fa-comment"></i> Request Idea</a>
            <a class="nav-link" href="idea_report.php"><i class="fa-regular fa-lightbulb"></i> Idea Reports</a>
            <a class="nav-link" href="register.php">User Registration</a>
            <a class="nav-link" href="change_password.php">Change Password</a>
            <a class="nav-link-active" href="department.php">Department</a>
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
            <h6 class="dash-title">Department</h6>
            <div class="dep-container">
                <form action="department.php" method="POST" class="dep-form">
                    <div class="input-box">
                        <label for="department_name">Department Name</label><br>
                        <input type="text" id="department_name" name="department_name" placeholder="E.g Science Department" required>
                    </div>
                    <div class="input-box">
                        <label for="department_location">Department Location</label><br>
                        <input type="text" id="department_location" name="department_location" placeholder="Location of Department" required>
                    </div>
                    <button type="submit" class="submit-btn">Add Department</button>
                </form>
            </div>
            <div class="dep-container" id="edit-form-container" style="display: none;">
                <p id="edit-success-msg" style="display:none; background-color: #28a745;
            color: white; text-align: center; padding: 8px;
            margin-bottom: 10px;
            border-radius: 5px;">Department updated successfully!</p>
                <form method="POST" id="edit-department-form" class="dep-form">
                    <input type="hidden" name="edit_department_id" id="edit_department_id">
                    <div class="input-box">
                        <label for="edit_department_name">Department Name</label><br>
                        <input type="text" id="edit_department_name" name="edit_department_name" required>
                    </div>
                    <div class="input-box">
                        <label for="edit_department_location">Department Location</label><br>
                        <input type="text" id="edit_department_location" name="edit_department_location" required>
                    </div>
                    <button type="submit" class="submit-btn">Update Department</button>
                </form>

            </div>
            <h6 class="dash-title">Department List</h6>
            <div class="dep-list-container">
                <?php
                $query = "SELECT * FROM departments WHERE status != 'Deactivated' ORDER BY department_id ASC";

                $result = $connection->query($query);

                if ($result && $result->num_rows > 0): ?>
                    <table class="department-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Department Name</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($dept = $result->fetch_assoc()): ?>
                                <tr id="status-<?php echo $dept['department_id']; ?>">
                                    <td><?php echo htmlspecialchars($dept['department_id']); ?></td>
                                    <td><?php echo htmlspecialchars($dept['department_name']); ?></td>
                                    <td><?php echo htmlspecialchars($dept['department_location']); ?></td>
                                    <td class="status">
                                        <form method="POST" action="department.php#status-<?php echo $dept['department_id']; ?>" style="display:inline;">
                                            <input type="hidden" name="department_id" value="<?php echo $dept['department_id']; ?>">
                                            <input type="hidden" name="current_status" value="<?php echo $dept['status']; ?>">
                                            <button type="button"
                                                class="status-toggle-btn"
                                                data-id="<?php echo $dept['department_id']; ?>"
                                                data-status="<?php echo $dept['status']; ?>"
                                                style="padding: 4px 8px; border: none; border-radius: 5px;
                                        background-color: <?php echo ($dept['status'] === 'Active') ? '#28a745' : '#dc3545'; ?>;
                                        color: white; cursor: pointer;">
                                                <?php echo $dept['status'] === 'Active' ? 'Active' : 'Closed'; ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="action">
                                        <a href="#" class="edit-btn"><i class="fa-solid fa-pen-to-square"></i></a>|
                                        <a href="#" class="delete-btn" style="text-decoration: none;"
                                            data-id="<?php echo $dept['department_id']; ?>"
                                            data-status="Deactivated">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>

                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No departments found.</p>
                <?php endif; ?>
            </div>
        </div>
        <script>
            document.querySelectorAll('.delete-btn').forEach(deleteBtn => {
                deleteBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const deptId = this.dataset.id;
                    const newStatus = 'Deactivated';

                    if (confirm('Are you sure you want to deactivate this department?')) {
                        fetch('toggle_department_status.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `id=${deptId}&status=${newStatus}`
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Remove the row from table
                                    const row = document.querySelector(`#status-${deptId}`);
                                    if (row) {
                                        row.remove();
                                    }

                                    // Optionally show a success message
                                    const successMsg = document.createElement('div');
                                    successMsg.textContent = "Department deactivated successfully!";
                                    successMsg.style.cssText = "position:sticky; top:0; background: #28a745; color: white; padding: 10px; margin-bottom: 10px; border-radius: 5px; text-align: center;";
                                    document.querySelector('.dep-list-container').prepend(successMsg);
                                    setTimeout(() => successMsg.remove(), 3000);
                                } else {
                                    alert('Failed to deactivate department.');
                                }
                            });
                    }
                });
            });
        </script>
        <script>
            // Handle Edit Button Click
            document.querySelectorAll('.edit-btn').forEach(editBtn => {
                editBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const row = this.closest('tr');
                    const deptId = row.querySelector('td:first-child').textContent.trim();
                    const name = row.querySelector('td:nth-child(2)').textContent.trim();
                    const location = row.querySelector('td:nth-child(3)').textContent.trim();

                    // Fill the edit form with existing data
                    document.getElementById('edit_department_id').value = deptId;
                    document.getElementById('edit_department_name').value = name;
                    document.getElementById('edit_department_location').value = location;

                    // Store the row for updating later
                    document.getElementById('edit-department-form').dataset.rowId = deptId;

                    // Show edit form, hide add form
                    document.querySelector('.dep-container').style.display = 'none';
                    document.getElementById('edit-form-container').style.display = 'block';
                });
            });

            // Handle Edit Form Submission via AJAX
            document.getElementById('edit-department-form').addEventListener('submit', function(e) {
                e.preventDefault();

                const deptId = document.getElementById('edit_department_id').value;
                const name = document.getElementById('edit_department_name').value;
                const location = document.getElementById('edit_department_location').value;

                fetch('update_department.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `id=${deptId}&name=${encodeURIComponent(name)}&location=${encodeURIComponent(location)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update the row in the table
                            const row = document.querySelector(`#status-${deptId}`).closest('tr');
                            row.querySelector('td:nth-child(2)').textContent = name;
                            row.querySelector('td:nth-child(3)').textContent = location;

                            // Show success message
                            document.getElementById('edit-success-msg').style.display = 'block';

                            // Reset view after 3s
                            setTimeout(() => {
                                document.getElementById('edit-success-msg').style.display = 'none';
                                document.getElementById('edit-form-container').style.display = 'none';
                                document.querySelector('.dep-container').style.display = 'block';
                            }, 3000);
                        } else {
                            alert('Update failed.');
                        }
                    });
            });
        </script>

        <script>
            document.querySelectorAll('.status-toggle-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const departmentId = this.dataset.id;
                    let currentStatus = this.dataset.status;
                    const buttonElement = this;

                    // Toggle the status
                    const newStatus = currentStatus === 'Active' ? 'Closed' : 'Active';

                    // Make the AJAX request to toggle status in DB
                    fetch('toggle_department_status.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id=${departmentId}&status=${newStatus}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update the button text and color
                                buttonElement.textContent = newStatus === 'Active' ? 'Active' : 'Closed';
                                buttonElement.style.backgroundColor = newStatus === 'Active' ? '#28a745' : '#dc3545';
                                buttonElement.dataset.status = newStatus;
                            } else {
                                alert('Failed to update status.');
                            }
                        });
                });
            });
        </script>
</body>

</html>