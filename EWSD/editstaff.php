<?php
// Include your connection
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Fetch the staff's current details
    $sql = "SELECT * FROM users WHERE user_id = $user_id";
    $result = mysqli_query($connection, $sql);
    $staff = mysqli_fetch_assoc($result);

    if (!$staff) {
        die("Staff not found.");
    }

    // Check if form is submitted
    if (isset($_POST['update'])) {
        $user_name = mysqli_real_escape_string($connection, $_POST['user_name']);
        $user_email = mysqli_real_escape_string($connection, $_POST['user_email']);
        $user_phone = mysqli_real_escape_string($connection, $_POST['user_phone']);
        $department_id = intval($_POST['department_id']);
        $user_profile = $_FILES['user_profile']['name'] ? $_FILES['user_profile']['name'] : $staff['user_profile'];

        // If profile picture is updated
        if ($_FILES['user_profile']['name']) {
            move_uploaded_file($_FILES['user_profile']['tmp_name'], 'uploads/' . $user_profile);
        }

        $update_sql = "
            UPDATE users SET
            user_name = '$user_name',
            user_email = '$user_email',
            user_phone = '$user_phone',
            department_id = $department_id,
            user_profile = '$user_profile'
            WHERE user_id = $user_id
        ";
        if (mysqli_query($connection, $update_sql)) {
            header('Location: staff_list.php');  // Redirect back to staff list
        } else {
            echo "Error updating record: " . mysqli_error($connection);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff</title>
</head>

<body>
    <h2>Edit Staff</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="user_name">Name:</label>
        <input type="text" name="user_name" value="<?php echo htmlspecialchars($staff['user_name']); ?>" required><br><br>

        <label for="user_email">Email:</label>
        <input type="email" name="user_email" value="<?php echo htmlspecialchars($staff['user_email']); ?>" required><br><br>

        <label for="user_phone">Phone:</label>
        <input type="text" name="user_phone" value="<?php echo htmlspecialchars($staff['user_phone']); ?>" required><br><br>

        <label for="department_id">Department:</label>
        <select name="department_id" required>
            <option value="">Select Department</option>
            <?php
            // Fetch departments
            $department_sql = "SELECT * FROM departments";
            $department_result = mysqli_query($connection, $department_sql);
            while ($department = mysqli_fetch_assoc($department_result)) {
                echo "<option value='" . $department['department_id'] . "' " . ($staff['department_id'] == $department['department_id'] ? 'selected' : '') . ">" . htmlspecialchars($department['department_name']) . "</option>";
            }
            ?>
        </select><br><br>

        <label for="user_profile">Profile Picture:</label>
        <input type="file" name="user_profile"><br><br>

        <button type="submit" name="update">Update Staff</button>
    </form>
</body>

</html>