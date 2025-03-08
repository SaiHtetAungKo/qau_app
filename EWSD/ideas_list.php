<?php
session_start();
$user_id = $_SESSION['user']['id']; // Simulating logged-in user ID

// Dummy data array
$ideas = [
    [
        'id' => 1,
        'department_name' => 'Marketing',
        'created_at' => '2025-03-01',
        'idea_description' => 'Launch a social media campaign to increase brand awareness.',
        'likes' => 15,
        'dislikes' => 2,
        'comments_count' => 5,
        'user_id' => 1
    ],
    [
        'id' => 2,
        'department_name' => 'IT',
        'created_at' => '2025-02-28',
        'idea_description' => 'Implement AI-based customer support to enhance user experience.',
        'likes' => 30,
        'dislikes' => 1,
        'comments_count' => 10,
        'user_id' => 2
    ],
    [
        'id' => 3,
        'department_name' => 'HR',
        'created_at' => '2025-03-02',
        'idea_description' => 'Introduce a flexible work schedule for better work-life balance.',
        'likes' => 25,
        'dislikes' => 3,
        'comments_count' => 7,
        'user_id' => 3
    ],
    [
        'id' => 4,
        'department_name' => 'Finance',
        'created_at' => '2025-03-03',
        'idea_description' => 'Optimize budget allocation to improve cost efficiency.',
        'likes' => 10,
        'dislikes' => 5,
        'comments_count' => 2,
        'user_id' => 4
    ],
    [
        'id' => 5,
        'department_name' => 'Operations',
        'created_at' => '2025-03-04',
        'idea_description' => 'Implement a lean workflow process to reduce waste.',
        'likes' => 40,
        'dislikes' => 0,
        'comments_count' => 12,
        'user_id' => 5
    ]
];

// Get filter from URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Filter ideas based on user selection
if ($filter == 'contributions') {
    $ideas = array_filter($ideas, function ($idea) use ($user_id) {
        return $idea['user_id'] == $user_id; // Show only the logged-in user's ideas
    });
} elseif ($filter == 'most_liked') {
    usort($ideas, function ($a, $b) {
        return $b['likes'] - $a['likes']; // Sort by most likes
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ideas List</title>
    <link rel="stylesheet" href="comments_modal.css">
</head>
<body>

<div class="ideas-container">
    <?php foreach ($ideas as $idea) { ?>
        <div class="idea-card">
            <div class="idea-header">
                <div class="avatar"></div>
                <div class="idea-info">
                    <strong><?php echo htmlspecialchars($idea['department_name']); ?></strong>
                    <p><?php echo date('d.m.Y', strtotime($idea['created_at'])); ?></p>
                </div>
                <div class="idea-categories">
                    <span class="main-category">Main Category</span>
                    <span class="sub-category">Sub Category</span>
                </div>
            </div>
            <div class="idea-content">
                <p><?php echo nl2br(htmlspecialchars($idea['idea_description'])); ?></p>
            </div>
            <div class="idea-actions">
                <button class="like-btn">👍 <?php echo $idea['likes']; ?></button>
                <button class="dislike-btn">👎 <?php echo $idea['dislikes']; ?></button>
                <button class="comment-btn" data-idea-id="<?php echo $idea['id']; ?>">💬 <?php echo $idea['comments_count']; ?></button>
            </div>
        </div>
    <?php } ?>
</div>

<!-- Comment Modal -->
<div id="commentModal" class="modal" style="display: none;">
    <div class="modal-content">
        <header>
            <h2>Comments</h2>
            <span id="finalClosureDate">{finalClosureDate}</span>
            <span class="close-btn" id="closeCommentModal">&times;</span>
        </header>
        <hr>
        <div id="comments-section">
            <!-- Comments will be loaded dynamically here -->
        </div>
        <hr>
        <div class="comment-input">
            <input type="text" id="commentText" placeholder="Leave your thoughts here">
            <button class="send-btn">🚀</button>
        </div>
    </div>
</div>

<script>
    console.log("Script is running!");

document.addEventListener("DOMContentLoaded", function () {
    document.body.addEventListener("click", function (event) {
        // Check if the clicked element has the class 'comment-btn'
        if (event.target.classList.contains("comment-btn")) {
            const ideaId = event.target.getAttribute("data-idea-id");
            openCommentModal(ideaId);
        }
    });
});

function openCommentModal(ideaId) {
    console.log("Opening modal for idea ID:", ideaId); // Debugging line
    const modal = document.getElementById("commentModal");
    modal.style.display = "block";

    // Fetch comments dynamically
    fetch(`fetch_comments.php?idea_id=${ideaId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById("comments-section").innerHTML = data;
        })
        .catch(error => console.error("Error fetching comments:", error));
}

function closeCommentModal() {
    document.getElementById("commentModal").style.display = "none";
}
alert("✅ Script is running!");
console.log("✅ Script is running!");

document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ DOM fully loaded!");
});

</script>
</body>
</html>
