<?php
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $departmentId = intval($_POST['id']);
    $newStatus = $_POST['status'];

    // Update the department status in the database
    $query = "UPDATE departments SET status = '$newStatus' WHERE department_id = $departmentId";

    if ($connection->query($query) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
}
