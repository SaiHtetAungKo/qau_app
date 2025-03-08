<?php
session_start();
include('connection.php');
include('functions.php'); // Include functions.php to use get_comments()

$connect = new Connect();
$connection = $connect->getConnection();

// Check whether the user is logged in
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

// For filtering "My Idea contribution" and "Most Liked Idea"
// I used post method here because if we used GET, the parameters would be visible in the URL (e.g., ?user_id=5&most_like=desc). POST keeps it hidden and is safer for handling sensitive user inputs.
//POST → Used for filtering (safer, hides parameters, avoids manipulation).
//GET → Used for fetching comments dynamically (faster, suitable for read-only operations).
$userID = $_POST['user_id'] ?? ''; // Default is empty
$sortMostLike = $_POST['most_like'] ?? ''; // Default is empty, no sorting yet

// Base SQL Query
$query = "
    SELECT 
        i.idea_id,
        i.userID,
        i.title,
        i.description,
        i.status,
        i.anonymousSubmission,
        i.created_at,
        u.user_name,
        u.user_profile,
        d.department_name,
        mc.MainCategoryTitle,
        sc.SubCategoryTitle,
        (SELECT COUNT(iv.ideavoteID) 
            FROM idea_vote AS iv 
            WHERE iv.idea_id = i.idea_id AND iv.votetype = 1) AS most_like, 
        (SELECT COUNT(iv.ideavoteID) 
            FROM idea_vote AS iv 
            WHERE iv.idea_id = i.idea_id AND iv.votetype = 2) AS unlike
    FROM ideas AS i
    INNER JOIN users AS u ON i.userID = u.user_id
    INNER JOIN request_ideas AS ri ON i.requestIdea_id = ri.requestIdea_id
    INNER JOIN departments AS d ON u.department_id = d.department_id
    INNER JOIN roles r ON u.role_id = r.role_id
    INNER JOIN subcategory sc ON i.SubCategoryID = sc.SubCategoryID
    INNER JOIN maincategory mc ON mc.MainCategoryID = sc.MainCategoryID
";

// Modify query if a specific user ID is selected
if (!empty($userID)) {
    $query .= " WHERE i.userID = ?";
}

// Set default sorting by idea_id and allow sorting by most_like if selected
if ($sortMostLike === 'asc' || $sortMostLike === 'desc') {
    $query .= " ORDER BY most_like " . ($sortMostLike === 'asc' ? 'ASC' : 'DESC')." LIMIT 10"; //Set the limit to 10, can change.
} else {
    // Default sort by idea_id, for all ideas
    $query .= " ORDER BY i.idea_id DESC";
}

// Prepare statement
$stmt = $connection->prepare($query);

if (!empty($userID)) {
    $stmt->bind_param("i", $userID);
}

$stmt->execute();
$result = $stmt->get_result();

// Fetch data
$ideas = [];
while ($row = $result->fetch_assoc()) {
    $ideas[] = $row;
}

$stmt->close();

// Handle AJAX request for fetching comments. 
// This is for fecthing-comment example. You can use if it is suitable with your front-end code
if (isset($_GET['idea_id'])) {
    $idea_id = $_GET['idea_id'];
    $comments = get_comments($idea_id, $connection);  // Call the get_comments() function from functions.php
    
    echo json_encode($comments);  // Return the comments as JSON
    exit();  // Terminate the script after returning the comments
}
?>


<!-- This is front-end for testing. You can delete the following and replace your front-end codes -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top 5 Ideas</title>
</head>

<body>
    <h2>Top 5 Ideas</h2>

    <!-- Filter Form (Using POST) -->
    <form method="POST" action="">
        <label for="user_id">Filter by User:</label>
        <input type="text" name="user_id" id="user_id" placeholder="Enter User ID" value="<?= htmlspecialchars($userID) ?>">

        <label for="most_like">Sort by Most Likes:</label>
        <select name="most_like" id="most_like">
            <option value="">-- Select --</option>
            <option value="desc" <?= ($sortMostLike === 'desc') ? 'selected' : ''; ?>>Highest to Lowest</option>
            <option value="asc" <?= ($sortMostLike === 'asc') ? 'selected' : ''; ?>>Lowest to Highest</option>
        </select>

        <button type="submit">Filter</button>
    </form>

    <!-- Display Ideas -->
    <table border="1">
        <tr>
            <th>Idea ID</th>
            <th>User ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Anonymous Submission</th>
            <th>Created At</th>
            <th>Department</th>
            <th>Main Category</th>
            <th>Sub Category</th>
            <th>Most Likes</th>
            <th>Comments</th> <!-- Add column for comments button -->
        </tr>
        <?php if (count($ideas) > 0): ?>
            <?php foreach ($ideas as $idea): ?>
                <tr>
                    <td><?= htmlspecialchars($idea['idea_id']) ?></td>
                    <td><?= htmlspecialchars($idea['userID']) ?></td>
                    <td><?= htmlspecialchars($idea['title']) ?></td>
                    <td><?= htmlspecialchars($idea['description']) ?></td>
                    <td><?= htmlspecialchars($idea['status']) ?></td>
                    <td><?= htmlspecialchars($idea['anonymousSubmission']) ?></td>
                    <td><?= htmlspecialchars($idea['created_at']) ?></td>
                    <td><?= htmlspecialchars($idea['department_name']) ?></td>
                    <td><?= htmlspecialchars($idea['MainCategoryTitle']) ?></td>
                    <td><?= htmlspecialchars($idea['SubCategoryTitle']) ?></td>
                    <td><?= htmlspecialchars($idea['most_like']) ?></td>
                    <td>
                        <button class="comment-btn" data-idea-id="<?= $idea['idea_id'] ?>">Show Comments</button>
                        <div id="comments-<?= $idea['idea_id'] ?>" class="comments-container" style="display: none;"></div> <!-- Comments container -->
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="12">No ideas found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <script>
        // Handle the comment button click event
        document.querySelectorAll('.comment-btn').forEach(button => {
            button.addEventListener('click', function() {
                const ideaId = this.getAttribute('data-idea-id');
                const commentsContainer = document.getElementById('comments-' + ideaId);

                // Make an AJAX request to fetch comments for this idea
                fetch('<?= $_SERVER['PHP_SELF']; ?>?idea_id=' + ideaId)
                    .then(response => response.json())  // Parse the response as JSON
                    .then(data => {
                        commentsContainer.innerHTML = ''; // Clear previous comments if any

                        if (data.length === 0) {
                            commentsContainer.innerHTML = '<p>No comments found for this idea.</p>';
                        } else {
                            // Loop through and display each comment
                            data.forEach(comment => {
                                const p = document.createElement('p');
                                p.textContent = comment.ideacommentText;  // Add each comment as a <p> element
                                commentsContainer.appendChild(p);
                            });
                        }

                        commentsContainer.style.display = 'block';  // Show the comments
                    })
                    .catch(error => {
                        console.error('Error fetching comments:', error);
                        commentsContainer.innerHTML = '<p>Error fetching comments. Please try again later.</p>';
                        commentsContainer.style.display = 'block';  // Show error message
                    });
            });
        });
    </script>
</body>

</html>
