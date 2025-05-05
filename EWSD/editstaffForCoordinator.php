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
        if (isset($_FILES['user_profile']) && $_FILES['user_profile']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['user_profile']['tmp_name'];
            $fileName = $_FILES['user_profile']['name'];
            $folder = "user_images/";
            $user_profile = $folder . "_" . basename($fileName);
 
            if (!move_uploaded_file($fileTmpPath, $user_profile)) {
                echo "<script>alert('Error uploading profile image');</script>";
                exit();
            }
        } else {
            // Use existing image if no new one is uploaded
            $user_profile = $staff['user_profile'];
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
            header('Location: qa_coordinator_staff_list.php');  // Redirect back to staff list
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
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f6f8;
        margin: 0;
        padding: 20px;
    }

    .form-container {
        max-width: 600px;
        margin: auto;
        background: #fff;
        padding: 25px 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-radius: 8px;
    }

    .form-container h2 {
        margin-bottom: 20px;
        color: #333;
        text-align: center;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 6px;
    }

    .form-group input,
    .form-group select {
        width: 570px;
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    .form-group select {
        width: 600px;
    }

    .form-group input[type="file"] {
        padding: 6px;
        width: 585px;
    }

    .form-group img {

        width: 100px;
        border-radius: 6px;
    }

    .form-actions {
        text-align: center;
    }

    .form-actions button {
        background: #007bff;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
    }

    .form-actions button:hover {
        background: #0056b3;
    }
</style>

<body>
    <div class="form-container">
        <h2>Edit Staff</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <div style="display: flex;
        justify-content: center; margin-bottom:1rem;">
                    <?php if ($staff['user_profile']) : ?>
                        <img src="<?php echo htmlspecialchars($staff['user_profile']); ?>" alt="Current Profile">
                    <?php endif; ?>
                </div>
                <label for="user_profile">Profile Picture</label>
                <input type="file" name="user_profile">

            </div>
            <div class="form-group">
                <label for="user_name">Name</label>
                <input type="text" name="user_name" value="<?php echo htmlspecialchars($staff['user_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="user_email">Email</label>
                <input type="email" name="user_email" value="<?php echo htmlspecialchars($staff['user_email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="user_phone">Phone</label>
                <input type="text" name="user_phone" value="<?php echo htmlspecialchars($staff['user_phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="department_id">Department</label>
                <select name="department_id" required>
                    <option value="">Select Department</option>
                    <?php
                    $department_sql = "SELECT * FROM departments";
                    $department_result = mysqli_query($connection, $department_sql);
                    while ($department = mysqli_fetch_assoc($department_result)) {
                        $selected = $staff['department_id'] == $department['department_id'] ? 'selected' : '';
                        echo "<option value='{$department['department_id']}' $selected>" . htmlspecialchars($department['department_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>



            <div class="form-actions">
                <button type="submit" name="update"><i class="fa-solid fa-floppy-disk"></i> Update Staff</button>
            </div>
        </form>
    </div>
</body>

</html>