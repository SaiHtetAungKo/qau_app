<?php
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) $_POST['id'];
    $status = $_POST['status'];
    $now = date('Y-m-d H:i:s');

    if (in_array($status, ['Active', 'Closed', 'Deactivated'])) {
        $stmt = $connection->prepare("UPDATE departments SET status = ?, updated_at = ? WHERE department_id = ?");
        if ($stmt->execute([$status, $now, $id])) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
}

echo json_encode(['success' => false]);
