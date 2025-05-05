<?php
session_start();
include('connection.php');

$connect = new Connect();
$connection = $connect->getConnection();

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first.']);
    exit();
}

$user_id = $_SESSION['userID'];
$idea_id = intval($_POST['idea_id']);
$votetype = $_POST['votetype']; // 1 = like, 2 = unlike

// Check if user already voted
$check = $connection->prepare("SELECT votetype FROM idea_vote WHERE idea_id = ? AND user_id = ?");
$check->bind_param("ii", $idea_id, $user_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $existing_vote = $row['votetype'];

    if ($existing_vote == $votetype) {
        // Same vote clicked again -> remove vote (toggle off)
        $delete = $connection->prepare("DELETE FROM idea_vote WHERE idea_id = ? AND user_id = ?");
        $delete->bind_param("ii", $idea_id, $user_id);
        $delete->execute();
        $current_vote = null;
    } else {
        // Switch vote type
        $update = $connection->prepare("UPDATE idea_vote SET votetype = ? WHERE idea_id = ? AND user_id = ?");
        $update->bind_param("sii", $votetype, $idea_id, $user_id);
        $update->execute();
        $current_vote = $votetype;
    }
} else {
    // New vote
    $insert = $connection->prepare("INSERT INTO idea_vote (idea_id, user_id, votetype) VALUES (?, ?, ?)");
    $insert->bind_param("iis", $idea_id, $user_id, $votetype);
    $insert->execute();
    $current_vote = $votetype;
}

// Get updated counts
$get_likes = $connection->prepare("SELECT COUNT(*) AS count FROM idea_vote WHERE idea_id = ? AND votetype = 1");
$get_likes->bind_param("i", $idea_id);
$get_likes->execute();
$like_result = $get_likes->get_result()->fetch_assoc();

$get_unlikes = $connection->prepare("SELECT COUNT(*) AS count FROM idea_vote WHERE idea_id = ? AND votetype = 2");
$get_unlikes->bind_param("i", $idea_id);
$get_unlikes->execute();
$unlike_result = $get_unlikes->get_result()->fetch_assoc();

$response = [
    'success' => true,
    'like_count' => $like_result['count'],
    'unlike_count' => $unlike_result['count'],
    'current_vote' => $current_vote
];

echo json_encode($response);
?>
