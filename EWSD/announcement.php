<?php
session_start();
include('connection.php');
include('functions.php'); // Include functions.php to use get_comments()

$connect = new Connect();
$conn = $connect->getConnection();

if (!isset($_SESSION['userID'])) {
    echo "<script>
        alert('Please Login First');
        window.location = 'index.php';
    </script>";
    exit();
}

// Fetch user data from session
$userName = $_SESSION['userName'];
$userProfileImg = $_SESSION['userProfile'] ?? 'default-profile.jpg'; // Default image if none is found

$requestSql = "SELECT * FROM request_ideas ORDER BY requestIdea_id DESC LIMIT 1";
$requestResult = $conn->query($requestSql);
$requestIdea = $requestResult && $requestResult->num_rows > 0 ? $requestResult->fetch_assoc() : null;

$announcements = [];
$announceSql = "SELECT a.*, d.department_name FROM annoucement a LEFT JOIN departments d ON a.department_id = d.department_id ORDER BY announce_id DESC";
$announceResult = $conn->query($announceSql);
if ($announceResult && $announceResult->num_rows > 0) {
    while ($row = $announceResult->fetch_assoc()) {
        $announcements[] = $row;
    }
}

echo json_encode([
    'request_idea' => $requestIdea,
    'announcements' => $announcements
]);

$conn->close();


?>