<?php

session_start();
include('connection.php');

$connect = new Connect();
$conn = $connect->getConnection();

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first.']);
    exit();
}


// Load or Submit Action
$user_id = $_SESSION['userID'];
$action = $_POST['action'] ?? '';
$idea_id = intval($_POST['idea_id'] ?? 0);

if ($action === 'load') {
    $sql = "SELECT ic.ideacommentText, ic.user_id, ic.created_at, ic.anonymousSubmission,d.department_name
            FROM idea_comment ic 
            LEFT JOIN users u ON ic.user_id = u.user_id
            LEFT JOIN departments d ON u.department_id = d.department_id
            WHERE ic.idea_id = $idea_id
            ORDER BY ic.created_at DESC";
    $result = $conn->query($sql);

    while($row = $result->fetch_assoc()) {
        
        $user = ($row['anonymousSubmission'] == 1) ? 'Anonymous' : $row['department_name'];
        $text = htmlspecialchars($row['ideacommentText']);
        $date = date('j.n.Y', strtotime($row['created_at']));
        echo "
        <div class='comment-entry'>
            <div class='profile-icon'>👤</div>
            <div class='flex-grow-1'>
                <div class='dept-name'>$user</div>
                <div>$text</div>
            </div>
            <div class='date'>$date</div>
        </div>";
    }
}

if ($action === 'submit') {
    $text = $conn->real_escape_string($_POST['comment']);
    // $user_id = intval($_POST['user_id']);
    $anon = intval($_POST['anonymous']);
    $now = date('Y-m-d H:i:s');

    $insert = "INSERT INTO idea_comment (ideacommentText, user_id, idea_id, anonymousSubmission, created_at, updated_at)
               VALUES ('$text', $user_id, $idea_id, $anon, '$now', '$now')";
    $conn->query($insert);

    // Load and return just the newly added comment
    $user = $anon ? 'Anonymous' : 'Your Department'; // You can fetch real department name too
    echo "
    <div class='comment-entry'>
        <div class='profile-icon'>👤</div>
        <div class='flex-grow-1'>
            <div class='dept-name'>$user</div>
            <div>" . htmlspecialchars($text) . "</div>
        </div>
        <div class='date'>" . date('j.n.Y') . "</div>
    </div>";
}
?>
