<?php

session_start();
include('connection.php');

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
    $sql = "SELECT ic.ideacommentText, ic.user_id, ic.created_at, ic.anonymousSubmission,d.department_name,u.user_name,u.user_profile
            FROM idea_comment ic 
            LEFT JOIN users u ON ic.user_id = u.user_id
            LEFT JOIN departments d ON u.department_id = d.department_id
            WHERE ic.idea_id = $idea_id
            ORDER BY ic.created_at DESC";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {

        $user = ($row['anonymousSubmission'] == 1) ? 'Anonymous' : $row['department_name'];
        $userName = ($row['anonymousSubmission'] == 1) ? '' : $row['user_name'];
        $userProfile = ($row['anonymousSubmission'] == 1) ? 'Images/Default-avatar.png' : htmlspecialchars($row['user_profile']);
        $text = htmlspecialchars($row['ideacommentText']);
        $date = date('j.n.Y', strtotime($row['created_at']));
        echo "
        <div class='comment-entry'>
            <div class='avatar'>
                <img src='$userProfile' alt='Profile Image'>
            </div>
            <div class='flex-grow-1'>
                <div class='dept-name'>$user</div>
                <div class='dept-name'>$userName</div>
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

    $idea_id = intval($idea_id); // sanitize input
    $getPostOwnerQuery = "SELECT userID FROM ideas WHERE idea_id = $idea_id";
    $postOwnerResult = mysqli_query($conn, $getPostOwnerQuery);

    if ($postOwnerResult && mysqli_num_rows($postOwnerResult) > 0) {
        $postOwnerrow = mysqli_fetch_assoc($postOwnerResult);
        $postOwnerID = $postOwnerrow['userID'];
        
        $secondQuery = "SELECT * FROM users WHERE user_id = $postOwnerID";
        $secondResult = mysqli_query($conn, $secondQuery);
        
        if ($secondResult && mysqli_num_rows($secondResult) > 0) {
            
            $userRow = mysqli_fetch_assoc($secondResult);
            $useremail = $userRow['user_email']; // make sure 'email' matches your DB column name 
        }else{
            
            $useremail = null;
        }
    }
    
    if ($useremail) {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();  // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';  // Set the SMTP server
            $mail->SMTPAuth = true;  // Enable SMTP authentication
            $mail->Username = 'opengate171@gmail.com';  // Your Gmail address
            $mail->Password = 'mnsh lxzg txel skbr';  // Your Gmail password or app password
            $mail->SMTPSecure = 'tls';  // Enable TLS encryption
            $mail->Port = 587;  // TCP port to connect to

            //Recipients
            $mail->setFrom('opengate171@gmail.com', 'Idea Submission System');
            $mail->addAddress($useremail);  // QA Coordinator's email

            //Content
            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = 'New Comment Submitted: ';
            $mail->Body    = "Hello, Your Post has a new comment";
            $mail->send();
            echo "<script>alert('Commented successfully! An email has been sent to the Postowner.');</script>";
        } catch (Exception $e) {
            echo "<script>alert('Commented successfully, but email could not be sent.');</script>";
        }
    } else {
        echo "<script>alert('Commented successfully, but no Post owner email found.');</script>";
    }
}
