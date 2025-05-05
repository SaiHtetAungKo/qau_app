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
        $referer = $_SERVER['HTTP_REFERER'] ?? 'qa_manager_idea_list.php';
    
        // Append success message to the referer URL
        $parsed_url = parse_url($referer);
        $query_string = isset($parsed_url['query']) ? $parsed_url['query'] . '&' : '';
        $redirect_url = $parsed_url['scheme'] . '://' . $parsed_url['host'] . $parsed_url['path'] . '?' . $query_string . 'msg=status_changed&status=' . $new_status;
    
        header("Location: $redirect_url");
        exit();
    }else {
        echo "Error updating idea status.";
    }
} else {
    echo "Invalid request.";
}
?>