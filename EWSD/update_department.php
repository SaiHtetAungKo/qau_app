<?php
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) $_POST['id'];
    $name = trim($_POST['name']);
    $location = trim($_POST['location']);
    $now = date('Y-m-d H:i:s');

    if (!empty($name) && !empty($location)) {
        $stmt = $connection->prepare("UPDATE departments SET department_name = ?, department_location = ?, updated_at = ? WHERE department_id = ?");
        $success = $stmt->execute([$name, $location, $now, $id]);

        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false]);
    }
}
