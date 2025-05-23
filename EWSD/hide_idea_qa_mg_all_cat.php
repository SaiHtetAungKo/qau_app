<?php
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();

if (isset($_GET['id']) && isset($_GET['category_name'])) {
    $idea_id = $_GET['id'];
    $category_name = $_GET['category_name'];

    // Get the current status of the idea
    $query = "SELECT status FROM ideas WHERE idea_id = $idea_id";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);
    $current_status = $row['status'];

    // Toggle the status (if it's 'hide', set it to 'active', else set it to 'hide')
    if ($current_status == 'hide') {
        $new_status = 'active';  // Unhide the idea
    } else {
        $new_status = 'hide';  // Hide the idea
    }

    // Update the status of the idea
    $query = "UPDATE ideas SET status = '$new_status' WHERE idea_id = $idea_id";
    $result = mysqli_query($connection, $query);

    if ($result) {
        header('Location: qa_manager_idea_list.php?category_name=' . urlencode($category_name) . '&msg=status_changed&status=' . $new_status);
        exit();
    } else {
        echo "Error updating idea status.";
    }
} else {
    echo "Invalid request.";
}
?>