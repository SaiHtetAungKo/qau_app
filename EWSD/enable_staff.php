<?php
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();

$id = $_GET['id'];
$query = "SELECT user_name FROM users WHERE user_id = '$id'";
$result = mysqli_query($connection, $query);
$row = mysqli_fetch_assoc($result);

  // Get id from URL
  if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // update active status
    $query = "UPDATE users SET account_status = 'active' WHERE user_id = $user_id";
    $result = mysqli_query($connection, $query);

    if ($result) {
        $name = urlencode($row['user_name']); 
        header("Location: qa_manager_staff_list.php?msg=enabled&name=$name");
        exit();
    } else {
        echo "Error disabling account.";
    }
} else {
    echo "Invalid request.";
}
?>