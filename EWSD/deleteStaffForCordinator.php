<?php
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Optional: delete profile image if you still want to clean up files when disabling
    $get_profile_sql = "SELECT user_profile FROM users WHERE user_id = $user_id";
    $get_profile_result = mysqli_query($connection, $get_profile_sql);
    $staff = mysqli_fetch_assoc($get_profile_result);
    if ($staff && $staff['user_profile'] && file_exists('uploads/' . $staff['user_profile'])) {
        unlink('uploads/' . $staff['user_profile']);  // Delete the old profile picture
    }

    // Update user's account_status to 'disable' instead of deleting
    $disable_sql = "UPDATE users SET account_status = 'deactivate' WHERE user_id = $user_id";

    if (mysqli_query($connection, $disable_sql)) {
        header('Location: qa_coordinator_staff_list.php');  // Redirect back to staff list
        exit();
    } else {
        echo "Error updating status: " . mysqli_error($connection);
    }
}