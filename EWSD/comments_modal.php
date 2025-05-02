<?php
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();

$idea_id = $_GET['idea_id'] ?? 0; // Get idea ID from request
$query = "SELECT * FROM comments WHERE idea_id = $idea_id ORDER BY created_at DESC";
$result = $connection->query($query);


?>

<div id="commentModal" class="modal">
    <div class="modal-content">
        <header>
            <h2>Comments</h2>
            <span id="finalClosureDate"><?php echo htmlspecialchars($final_closure_date); ?></span>
        </header>
        <hr>
        <div class="comments-section">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="comment">
                    <div class="avatar"></div>
                    <div class="comment-details">
                        <strong><?php echo htmlspecialchars($row['department_name']); ?></strong>
                        <p><?php echo htmlspecialchars($row['comment_text']); ?></p>
                    </div>
                    <span class="comment-date"><?php echo date('d.m.Y', strtotime($row['created_at'])); ?></span>
                </div>
            <?php } ?>
        </div>
        <hr>
        <div class="comment-input">
            <input type="text" placeholder="Leave your thoughts here">
            <button class="send-btn">🚀</button>
        </div>
    </div>
</div>