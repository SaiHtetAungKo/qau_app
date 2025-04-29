<?php
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // First, delete the staff's profile picture if exists (optional, if you want to clean up the uploaded file).
    $get_profile_sql = "SELECT user_profile FROM users WHERE user_id = $user_id";
    $get_profile_result = mysqli_query($connection, $get_profile_sql);
    $staff = mysqli_fetch_assoc($get_profile_result);
    if ($staff && $staff['user_profile'] && file_exists('uploads/' . $staff['user_profile'])) {
        unlink('uploads/' . $staff['user_profile']);  // Delete the old profile picture
    }

    // Delete staff record
    $delete_sql = "DELETE FROM users WHERE user_id = $user_id";
    if (mysqli_query($connection, $delete_sql)) {
        header('Location: staff_list.php');  // Redirect back to the staff list
    } else {
        echo "Error deleting record: " . mysqli_error($connection);
    }
}
