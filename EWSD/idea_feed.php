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
// $userID = $_POST['user_id'] ?? '';
$userID = $_POST['user_id'] == "contributions" ? $_SESSION['userID'] : '';
$sortMostLike = $_POST['most_like'] ?? ''; // Default is empty, no sorting yet

// Base SQL Query
$query = "
    SELECT 
        i.idea_id,
        i.userID,
        i.title,
        i.description,
        i.status,
        i.anonymousSubmission,i.img_path,
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
    LEFT JOIN users AS u ON i.userID = u.user_id
    LEFT JOIN request_ideas AS ri ON i.requestIdea_id = ri.requestIdea_id
    LEFT JOIN departments AS d ON u.department_id = d.department_id
    LEFT JOIN roles r ON u.role_id = r.role_id
    LEFT JOIN subcategory sc ON i.SubCategoryID = sc.SubCategoryID
    LEFT JOIN maincategory mc ON mc.MainCategoryID = sc.MainCategoryID
    WHERE i.status = 'active'
    
";

// Modify query if a specific user ID is selected
if (!empty($userID)) {
    $query .= " AND i.userID = ?";
}

// Set default sorting by idea_id and allow sorting by most_like if selected
if ($sortMostLike === 'asc' || $sortMostLike === 'desc') {
    $query .= " ORDER BY most_like " . ($sortMostLike === 'asc' ? 'ASC' : 'DESC')." LIMIT 5"; //Set the limit to 10, can change.
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

echo json_encode($ideas);
exit; // Stop further execution
?>



