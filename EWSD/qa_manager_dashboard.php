<?php
session_start();
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();

    // Check if the user is logged in
    if (!isset($_SESSION['user'])) {
        echo "<script> window.alert('Please Login First'); </script>";
        echo "<script> window.location= 'index.php'; </script>";
        exit(); // Stop further code execution
    }
    
    if (isset($_GET['msg']) && $_GET['msg'] == 'status_changed') {
        if (isset($_GET['status']) && $_GET['status'] == 'hide') {
            echo "<div class='popup-message'>Idea has been successfully hidden</div>";
        } elseif (isset($_GET['status']) && $_GET['status'] == 'active') {
            echo "<div class='popup-message'>Idea has been successfully unhidden</div>";
        }
    }

    $userName = $_SESSION['userName'];
    $userProfileImg = $_SESSION['userProfile'] ?? 'default-profile.jpg'; // Default image if none is found

    // category count
    $catSql = "SELECT COUNT(*) AS total_categories FROM maincategory WHERE status != 'inactive'";
    $catResult = mysqli_query($connection, $catSql);
    $catRow = mysqli_fetch_assoc($catResult);
    $categoryCount = $catRow['total_categories'];

// department count
$departmentSql = "SELECT COUNT(*) AS total_departments FROM departments";
$departmentResult = mysqli_query($connection, $departmentSql);
$departmentRow = mysqli_fetch_assoc($departmentResult);
$departmentCount = $departmentRow['total_departments'];

// hiddenIdea count
$hiddenIdeaSql = "SELECT COUNT(*) AS hidden_ideas FROM ideas WHERE status = 'hide'";
$hiddenIdeaResult = mysqli_query($connection, $hiddenIdeaSql);
$hiddenIdeaRow = mysqli_fetch_assoc($hiddenIdeaResult);
$hiddenIdeaCount = $hiddenIdeaRow['hidden_ideas'];

// 1. Top 3 Popular Ideas
$topQuery = "SELECT 
        i.idea_id,
        i.title,
        i.description,
        i.created_at,
        u.user_name, 
        i.status,
        mc.MainCategoryTitle,
        sc.SubCategoryTitle,
        dp.department_name,
        COUNT(DISTINCT c.ideacommentID) AS comment_count,
        COUNT(DISTINCT iv.ideavoteID) AS total_votes,
        SUM(CASE WHEN iv.votetype = 1 THEN 1 ELSE 0 END) AS upvotes, 
        SUM(CASE WHEN iv.votetype = 2 THEN 2 ELSE 0 END) AS downvotes,
        (COUNT(DISTINCT c.ideacommentID) + SUM(CASE WHEN iv.votetype = 1 THEN 1 ELSE 0 END)) AS popularity_score
    FROM ideas i
    LEFT JOIN idea_comment c ON i.idea_id = c.idea_id
    LEFT JOIN idea_vote iv ON i.idea_id = iv.idea_id
    LEFT JOIN subcategory sc ON sc.SubCategoryID = i.SubCategoryID
    LEFT JOIN maincategory mc ON mc.MainCategoryID = sc.MainCategoryID
    LEFT JOIN users u ON i.userID = u.user_id
    LEFT JOIN departments dp ON u.department_id = dp.department_id
    GROUP BY i.idea_id
    ORDER BY popularity_score DESC
    LIMIT 5";

    // $topResult = mysqli_query($connection, $topQuery);
    // $topIdeas = [];
    // while ($row = mysqli_fetch_assoc($topResult)) {
    //     $ideas[] = $row;
    // }
    $topResult = mysqli_query($connection, $topQuery);
    $ideas = [];
    
    while ($row = mysqli_fetch_assoc($topResult)) {
        $ideaId = $row['idea_id'];
    
        // Fetch comments for this idea
        $commentQuery = "SELECT ideacommentText, created_at FROM idea_comment WHERE idea_id = $ideaId ORDER BY created_at ASC";
        $commentResult = mysqli_query($connection, $commentQuery);
    
        $commentTexts = [];
        $commentDates = [];
    
        while ($comment = mysqli_fetch_assoc($commentResult)) {
            $commentTexts[] = $comment['ideacommentText'];
            $commentDates[] = $comment['created_at'];
        }
    
        // Add them to current row
        $row['ideacommentText'] = implode('||', $commentTexts);
        $row['comment_dates'] = implode('||', $commentDates);
    
        $ideas[] = $row;
    }
    // New SQL query to fetch department idea data with contribution percentage
    $query = "
        WITH department_idea_data AS (
        SELECT 
            d.department_id AS department_id,
            d.department_name AS department_name,
            COUNT(i.idea_id) AS total_ideas,
            COUNT(DISTINCT u.user_id) AS total_posters
        FROM departments d
        LEFT JOIN users u ON u.department_id = d.department_id
        LEFT JOIN ideas i ON i.userID = u.user_id
        GROUP BY d.department_id, d.department_name
        ),
        total_idea_count AS (
        SELECT COUNT(*) AS total_ideas FROM ideas
        )
        SELECT 
        did.department_id,
        did.department_name,
        did.total_ideas,
        did.total_posters,
        ROUND(CAST(did.total_ideas AS FLOAT) / tic.total_ideas * 100, 2) AS contribution_percentage
        FROM department_idea_data did
        CROSS JOIN total_idea_count tic
        ORDER BY did.total_ideas DESC;
        ";

$result = mysqli_query($connection, $query);
$departments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $departments[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>QA Manager Category and Idea Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: white;
            color: black;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar h2 {
            margin-bottom: 20px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: #ddd;
            border: none;
            color: black;
            cursor: pointer;
            text-align: center;
            font-size: 16px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .btn:hover {
            background: rgb(89, 64, 122);
            color: white;
        }

        .logout {
            margin-top: auto;
            background: #3c9a72;
            padding: 12px;
            color: white;
            border: none;
            width: 100%;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
        }

        .logout:hover {
            background: rgb(89, 64, 122);
        }

        .content {
            flex: 1;
            background: rgb(89, 64, 122);
            color: white;
            padding: 20px;
            overflow-y: auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 12px;
            width: 50%;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .user-info {
            text-align: right;
        }

        header a {
            color: white;
            text-decoration: underline;
        }

        .categories {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            /* 3 columns in one row */
            gap: 20px;
        }

        .category-card {
            background: white;
            color: black;
            padding: 30px 20px;
            border-radius: 10px;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .category-image {
            width: 40px;
            height: 40px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .arrow,
        .delete {
            position: absolute;
            right: 15px;
            bottom: 15px;
            font-size: 20px;
            cursor: pointer;
        }

        .delete {
            color: red;
        }

        /* Section visibility */
        #category-sections,
        #idea-report-section {
            display: none;
        }

        /* Download section */
        .download-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .download-section p {
            font-weight: bold;
        }

        .download-btn {
            background: #aee0d3;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .green-box {
            width: 40px;
            height: 40px;
            background-color: #90d5c9;
            border-radius: 12px;
            margin-bottom: 10px;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .category-count {
            background-color: #eee;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .back-btn {
            background-color: #A3E7D8;
            color: black;
            font-weight: 600;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            margin-bottom: 20px;
            text-decoration: none;
        }

        .logout {
            margin-top: auto;
            background: #3c9a72;
            padding: 12px;
            color: white;
            border: none;
            width: 100%;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
        }

        .logout:hover {
            background: rgb(89, 64, 122);
        }


        /* for successful disable staff acc msg */
        .popup-message {
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #3c9a72;
            color: white;
            padding: 30px 28px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            font-size: 18px;
            opacity: 0;
            animation: fadeInOut 2.5s ease forwards;
        }

        @keyframes fadeInOut {
            0% {
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        h2 span {
            font-weight: 600;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            color: black;
        }

        .user-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 50px;
            height: 50px;
            background: #ddd;
            border-radius: 50%;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dept-name {
            font-weight: 600;
            margin: 0;
            font-size: 13px;
            color:#797979;
            padding-bottom: 5px; 
        }

        .user-name {
            font-weight: 600;
            font-size: 16px;
            margin: 0;
            color:#313131;
            padding-bottom: 5px; 
            text-align: left;
        }

        .date {
            color: gray;
            font-size: 14px;
            margin: 0;
            padding-top: 2px;
            text-align: left;
        }

        .content {
            flex: 1;
            background: rgb(89, 64, 122);
            color: white;
            padding: 20px;
            overflow-y: auto;
        }

        .subcategory {
            background-color: #A3E7D8;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            color: white;
        }

        .idea-text {
            margin: 20px 0;
            font-size: 14px;
            line-height: 1.6;
        }

        .green-box {
            width: 40px;
            height: 40px;
            background-color: #90d5c9;
            border-radius: 12px;
            margin-bottom: 10px;
        }

        .reactions {
            display: flex;
            gap: 15px;
            justify-content: flex-start;
        }

        .reactions button {
            padding: 10px 20px;
            border: 2px solid #ccc;
            border-radius: 20px;
            background: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .reactions a {
            padding: 10px 20px;
            border: 2px solid #ccc;
            text-decoration: none;
            border-radius: 20px;
            background: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .reactions .hide-idea-btn {
            margin-left: auto;
            background-color: #59417B;
            border-color: #59417B;
            color: white;
        }

        .reactions .hide-idea-btn:hover {
            background-color: rgb(124, 91, 170);
            border-color: rgb(124, 91, 170);
        }
    </style>
</head>

<body>

    <div class="admin-container">
        <div class="side-nav">
            <div class="logo text-center">
                <img src="Images/logo.png" alt="logo" width="150px" style="margin: 8px 0px;">
            </div>
            <a class="nav-link-active" href="qa_manager_dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a class="nav-link" href="qa_manager_home.php"><i class="fa-solid fa-layer-group"></i> Categories</a>
            <a class="nav-link" href="qa_manager_idea_summary.php"><i class="fa-regular fa-lightbulb"></i> Idea Reports</a>
            <a class="nav-link" href="qa_manager_staff_list.php"><i class="fa-solid fa-users"></i> Staff List</a>
            <a class="nav-link" href="qa_manager_hidden_idea_list.php"><i class="fa-regular fa-eye-slash"></i> Hidden Idea List</a>
            <a class=" logout" href="logout.php" onclick="return confirm('Do You Want To Log Out?')">Log Out</a>
        </div>

        <main class="content">
            <!-- <header>
                <a href="qa_manager_idea_summary.php" class="back-btn">← Back</a>
                <div class="user-info">
                    <span><strong>Name</strong></span><br>
                    <span>QA Manager</span>
                </div>
                <a href="add_category.php">Add new category</a>
            </header> -->
            <div class="qa-manager-dash-section">
                <header class="qa-manager-dash-header">
                    <div class="qa-manager-search-input">
                        <input type="hidden" placeholder="Search" aria-label="Search">
                    </div>
                    <div class="qa-manager-user-display">
                        <img src="<?php echo htmlspecialchars($userProfileImg); ?>" alt="Profile Image">
                        <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                    </div>
                </header>
            </div>

            <!-- Category Section -->
            <div id="category-sections" style="display: block;">
                <h3>All Data</h3>
                <div class="categories">
                    <div class="category-card">
                        <div class="card-header">
                            <img src="images/dummy_category.png" alt="" class="category-image">
                            <span class="category-count"><?php echo $categoryCount; ?></span>
                        </div>
                        <h4>Categories</h4>
                        <p>View all categories</p> <!-- Replace 10 with dynamic count -->
                        <a href="qa_manager_all_cat_list.php"><span class="arrow">&rarr;</span></a>
                    </div>

                    <div class="category-card">
                        <div class="card-header">
                            <img src="images/department-icon.png" alt="" class="category-image">
                            <span class="category-count"><?php echo $departmentCount; ?></span>
                        </div>
                        <h4>Departments</h4>
                        <p>View all departments</p>
                        <a href="qa_manager_idea_summary.php"><span class="arrow">&rarr;</span></a>
                    </div>

                    <div class="category-card">
                        <div class="card-header">
                            <img src="images/hidden-idea.png" alt="" class="category-image">
                            <span class="category-count"><?php echo $hiddenIdeaCount; ?></span>
                        </div>

                        <h4>Hidden Ideas</h4>
                        <p>View hidden ideas</p>
                        <a href="qa_manager_hidden_idea_list.php"><span class="arrow">&rarr;</span></a>
                    </div>
                </div>
            </div>


            <h2>5 <span>Most Popular Idea Posts</span></h2>
            <?php foreach ($ideas as $idea): ?>
                <div class="card">
                    <div class="user-info">
                        <div class="user-left">
                            <div class="avatar">👤</div>
                            <div>
                                <p class="user-name"><?= htmlspecialchars($idea['user_name']) ?></p>
                                <p class="dept-name"><?= htmlspecialchars($idea['department_name']) ?></p>
                                <p class="date"><?= date("d.m.Y", strtotime($idea['created_at'])) ?></p>
                            </div>
                        </div>
                        <span class="subcategory"><?= htmlspecialchars($idea['SubCategoryTitle']) ?></span>
                    </div>
                    

                    <p class="idea-text"><?= htmlspecialchars($idea['description']) ?></p>

                    <div class="reactions">
                        <button><?= $idea['upvotes'] ?> 👍</button>
                        <button><?= $idea['downvotes'] ?> 👎</button>
                        <button onclick="openModal(<?= $idea['idea_id'] ?>)"><?= $idea['comment_count'] ?> 💬</button>

                        <?php

                        $idea_status = $idea['status']; // 'active' or 'hide'

                        if ($idea_status == 'hide') {
                            // Show Unhide button
                            echo '<a href="hide_idea.php?id=' . urlencode($idea['idea_id']) . '&category_name=' . urlencode($idea['department_name']) . '" class="hide-idea-btn">Unhide</a>';
                        } else {
                            // Show Hide button
                            echo '<a href="hide_idea.php?id=' . urlencode($idea['idea_id']) . '&category_name=' . urlencode($idea['department_name']) . '" class="hide-idea-btn">Hide</a>';
                        }
                        ?>
                    </div>

                </div>
            <?php endforeach; ?>
        </main>
    </div>
    
    <!-- comment modal -->
    <div id="commentModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; font-family:'Poppins', sans-serif;">
        <div style="background:white; width:600px; max-width:90%; border-radius:10px; overflow:hidden;">
            <div style="background:#1e1e1e; padding:20px; color:white;">
                <h3 style="margin:0; font-size:18px;">Comments</h3>
            </div>
            <div style="padding: 20px; max-height: 400px; overflow-y: auto;" id="commentContent">
                <!-- Comments will be injected here -->
            </div>
            <!-- <div style="border-top: 1px solid #ccc; display: flex; align-items: center; padding: 20px; gap: 10px;">
                <input type="text" placeholder="Leave your thoughts here" style="flex:1; padding: 14px; border: 1px solid #999; border-radius: 8px; font-family: 'Poppins', sans-serif;">
                <button style="border: none; background: none; font-size: 24px; cursor: pointer;">📤</button>
            </div> -->
            <div style="text-align:right; padding: 10px 20px;">
                <button onclick="closeModal()" style="padding: 8px 16px; border: none; background: #ccc; border-radius: 6px; font-weight: 600; cursor:pointer;">Close</button>
            </div>
        </div>
    </div>

    <script>
 
        document.addEventListener("DOMContentLoaded", () => {
            const deletes = document.querySelectorAll(".delete");

            deletes.forEach(btn => {
                btn.addEventListener("click", function() {
                    const categoryId = this.getAttribute("data-category-id");

                    if (confirm("Are you sure you want to delete this category?")) {
                        fetch('delete_category.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `category_id=${categoryId}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload(); // reload page
                            } else {
                                alert("Error deleting category: " + data.error);
                            }
                        });
                    }
                });
            }); 
 
            const ideaData = <?= json_encode($ideas ?? []) ?>;
            console.log(ideaData);
            
            // Modal functions can be declared here or globally
            window.openModal = function(ideaId) {
                const idea = ideaData.find(i => i.idea_id == ideaId);
                const comments = idea?.ideacommentText?.split('||') || [];
                const dates = idea?.comment_dates?.split('||') || [];

                const seen = new Set();
                const uniqueComments = [];
                const uniqueDates = [];

                comments.forEach((comment, index) => {
                    const commentText = comment?.trim();
                    const commentDate = dates[index]?.trim();

                    if (commentText && commentDate) {
                        const key = commentText + commentDate;
                        if (!seen.has(key)) {
                            seen.add(key);
                            uniqueComments.push(commentText);
                            uniqueDates.push(commentDate);
                        }
                    }
                });


                let html = `<h2 style="margin: 0 0 10px 0; color: black;">Comments</h2><hr><div style="max-height: 400px; overflow-y: auto; padding-right: 10px;">`;
                if (uniqueComments.length === 0) {
                    html += `<p style="color: #666;">No comments yet.</p>`;
                }
                uniqueComments.forEach((text, idx) => {
                    html += `
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin: 20px 0;">
                            <div style="display: flex; gap: 15px;">
                                <div style="width: 50px; height: 50px; background: #222; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">👤</div>
                                <div>
                                    <p style="margin: 0; font-weight: 600; color: #2e7166;">Department Name</p>
                                    <p style="margin: 0; font-size: 14px; color: #444;">${text}</p>
                                </div>
                            </div>
                            <p style="color: #666; font-size: 14px;">${new Date(uniqueDates[idx]).toLocaleDateString()}</p>
                        </div>`;
                });

                html += `</div>`;

                document.getElementById('commentContent').innerHTML = html;
                document.getElementById('commentModal').style.display = 'flex';
            };

            window.closeModal = function() {
                document.getElementById('commentModal').style.display = 'none';
            };
        });

        function toggleCategories() {
            document.getElementById("category-sections").style.display = "block";
            document.getElementById("idea-report-section").style.display = "none";
        }

        function toggleIdeaReports() {
            document.getElementById("category-sections").style.display = "none";
            document.getElementById("idea-report-section").style.display = "block";
        }

        function confirmLogout() {
            if (confirm('Do You Want To Log Out?')) {
                window.location.href = 'logout.php';
            }
        }
        
       
    </script>

</body>

</html>