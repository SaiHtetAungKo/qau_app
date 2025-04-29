<?php
session_start();
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();
$idea_id = intval($_GET['idea_id']);

// Fetch idea detail
$idea_sql = "
SELECT i.*, u.user_name, d.department_name
FROM ideas i
LEFT JOIN users u ON i.userID = u.user_id
LEFT JOIN departments d ON u.department_id = d.department_id
WHERE i.idea_id = $idea_id
";

$idea_result = mysqli_query($connection, $idea_sql);
$idea = mysqli_fetch_assoc($idea_result);

if ($idea) {
    // Prepare posted user
    $poster_name = ($idea['anonymousSubmission'] == 1) ? 'Anonymous' : htmlspecialchars($idea['user_name']);
    $department = ($idea['anonymousSubmission'] == 1) ? '' : htmlspecialchars($idea['department_name']);

    // Format date
    $posted_datetime = date('d F Y \a\t h:ia', strtotime($idea['created_at']));

    echo "<h2>" . htmlspecialchars($idea['title']) . "</h2>";
    echo "<p><strong>Posted On:</strong> $posted_datetime</p>";
    echo "<p><strong>Posted By:</strong> $poster_name</p>";
    if ($department) {
        echo "<p><strong>Department:</strong> $department</p>";
    }

    echo "<p>" . nl2br(htmlspecialchars($idea['description'])) . "</p>";

    // 🔥 Fetch Likes and Dislikes
    $vote_sql = "
    SELECT 
        SUM(CASE WHEN votetype = 1 THEN 1 ELSE 0 END) AS likes,
        SUM(CASE WHEN votetype = 2 THEN 1 ELSE 0 END) AS dislikes
    FROM idea_vote
    WHERE idea_id = $idea_id
    ";

    $vote_result = mysqli_query($connection, $vote_sql);
    $vote = mysqli_fetch_assoc($vote_result);

    $likes = $vote['likes'] ?? 0;
    $dislikes = $vote['dislikes'] ?? 0;

    // 🔥 Show Like/Dislike counts
    echo "<div class='idea-votes'>
            <span class='btn btn-outline-success' disabled>👍 $likes Likes</span> |
            <span class='btn btn-outline-danger' disabled>👎 $dislikes Dislikes</span>
          </div>";

    // 🔥 Fetch comments
    $comment_sql = "
    SELECT ic.*, u.user_name
    FROM idea_comment ic
    LEFT JOIN users u ON ic.user_id = u.user_id
    WHERE ic.idea_id = $idea_id
    ORDER BY ic.created_at ASC
    ";

    $comment_result = mysqli_query($connection, $comment_sql);
    $comment_count = mysqli_num_rows($comment_result);

    echo "<hr><h3>Comments ($comment_count)</h3>";
    echo "<div class='idea-comments'>";
    if ($comment_count > 0) {
        while ($comment = mysqli_fetch_assoc($comment_result)) {
            $commenter = ($comment['anonymousSubmission'] == 1) ? 'Anonymous' : htmlspecialchars($comment['user_name']);
            $comment_text = htmlspecialchars($comment['ideacommentText']);
            $comment_datetime = date('d F Y \a\t h:ia', strtotime($comment['created_at']));

            echo "<div style='margin-bottom:15px;'>
                    <p><strong>$commenter</strong><br> <span style='font-size:14px;'>($comment_datetime)<span></p>
                    <p>$comment_text</p>
                  </div>";
        }
    } else {
        echo "<p>No comments yet.</p>";
    }
    echo "</div>";
}
