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

// $categoryId = $_GET['category_name'];
$categoryName = mysqli_real_escape_string($connection, $_GET['category_name']);



// 1. Top 3 Popular Categories
$topQuery = "SELECT 
        mc.MainCategoryTitle,
        sc.SubCategoryTitle,
        i.idea_id,
        i.title AS idea_title,
        i.description AS idea_description,
        i.status AS idea_status,
        i.anonymousSubmission,
        i.created_at AS idea_created_at,
        i.updated_at AS idea_updated_at,
        dp.department_name AS department_name,
        COUNT(DISTINCT ic.ideacommentID) AS comment_count,
        COUNT(DISTINCT iv.ideavoteID) AS total_votes,
        SUM(CASE WHEN iv.votetype = 1 THEN 1 ELSE 0 END) AS upvotes,
        SUM(CASE WHEN iv.votetype = 2 THEN 2 ELSE 0 END) AS downvotes,
        GROUP_CONCAT(ic.ideacommentText SEPARATOR '||') AS comment_texts,
        GROUP_CONCAT(ic.created_at SEPARATOR '||') AS comment_dates
    FROM 
        maincategory mc
    JOIN 
        subcategory sc ON mc.MainCategoryID = sc.MainCategoryID
    LEFT JOIN 
        ideas i ON sc.SubCategoryID = i.SubCategoryID
    LEFT JOIN 
        idea_comment ic ON i.idea_id = ic.idea_id
    LEFT JOIN 
        idea_vote iv ON i.idea_id = iv.idea_id
    LEFT JOIN 
        users u ON u.user_id = i.userID
    LEFT JOIN 
        departments dp ON dp.department_id = u.department_id
    WHERE 
        dp.department_name = '$categoryName'
    GROUP BY 
        mc.MainCategoryTitle,
        sc.SubCategoryTitle,
        i.idea_id
    ORDER BY 
        -- mc.MainCategoryTitle, 
        -- sc.SubCategoryTitle, 
        i.created_at DESC";

$topResult = mysqli_query($connection, $topQuery);
$ideas = [];
while ($row = mysqli_fetch_assoc($topResult)) {
    $ideas[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Idea</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: auto;
            padding: 40px 20px;
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
        }

        .date {
            color: gray;
            font-size: 14px;
            margin: 0;
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

        .footer-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
        }

        .note {
            color: #DCD0F4;
            font-weight: 600;
        }

        .download-btn {
            background-color: #A3E7D8;
            color: black;
            font-weight: 600;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="admin-container">
        <div class="side-nav">
            <div class="logo text-center">
                <img src="Images/logo.png" alt="logo" width="150px" style="margin: 8px 0px;">
            </div>
            <a class="nav-link" href="qa_coordinator_home.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a class="nav-link" href="qa_coordinator_staff_list.php"><i class="fa-solid fa-users"></i> Staff List</a>
            <a class="nav-link-active" href="qa_coordinator_idea_report.php"><i class="fa-regular fa-lightbulb"></i> Idea Reports</a>
            <a class="nav-link" href="qa_coordinator_annoucement.php"><i class="fa-regular fa-lightbulb"></i> Announcements</a>
            <a class=" logout" href="logout.php" onclick="return confirm('Do You Want To Log Out?')">Log Out</a>
        </div>
        <main class="content">
            <a href="qa_coordinator_idea_report.php" class="back-btn">← Back</a>
            <h2>Ideas</h2>
            <?php foreach ($ideas as $idea): ?>
                <div class="card">
                    <div class="user-info">
                        <div class="user-left">
                            <div class="avatar">👤</div>
                            <div>
                                <p class="dept-name"><?= htmlspecialchars($idea['department_name']) ?></p>
                                <p class="date"><?= date("d.m.Y", strtotime($idea['idea_created_at'])) ?></p>
                            </div>
                        </div>
                        <span class="subcategory"><?= htmlspecialchars($idea['SubCategoryTitle']) ?></span>
                    </div>

                    <p class="idea-text"><?= htmlspecialchars($idea['idea_description']) ?></p>


                    <div class="reactions">
                        <button><?= $idea['upvotes'] ?> 👍</button>
                        <button><?= $idea['downvotes'] ?> 👎</button>
                        <button onclick="openModal(<?= $idea['idea_id'] ?>)"><?= $idea['comment_count'] ?> 💬</button>

                        <?php

                        // $idea_status = $idea['idea_status']; // 'active' or 'hide'

                        // if ($idea_status == 'hide') {
                        //     // Show Unhide button
                        //     echo '<a href="hide_idea.php?id=' . urlencode($idea['idea_id']) . '&category_name=' . urlencode($idea['department_name']) . '" class="hide-idea-btn">Unhide</a>';
                        // } else {
                        //     // Show Hide button
                        //     echo '<a href="hide_idea.php?id=' . urlencode($idea['idea_id']) . '&category_name=' . urlencode($idea['department_name']) . '" class="hide-idea-btn">Hide</a>';
                        // }
                        ?>
                    </div>

                </div>
            <?php endforeach; ?>
            <!-- MODAL -->
            <!-- MODAL -->
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
            <!-- <div class="footer-section">
                <p class="note">You can download only after final closure date</p>
                <button class="download-btn">⬇️ Download</button>
            </div> -->
        </main>

    </div>


    <!-- JS for modal -->
    <script>
        const ideaData = <?= json_encode($ideas) ?>;

        function openModal(ideaId) {
            const idea = ideaData.find(i => i.idea_id == ideaId);
            const comments = idea.comment_texts?.split('||') || [];
            const dates = idea.comment_dates?.split('||') || [];

            // Filter out duplicates based on comment + date combo
            const seen = new Set();
            const uniqueComments = [];
            const uniqueDates = [];

            comments.forEach((comment, index) => {
                const key = comment.trim() + dates[index]?.trim();
                if (!seen.has(key)) {
                    seen.add(key);
                    uniqueComments.push(comment);
                    uniqueDates.push(dates[index]);
                }
            });

            // Start building HTML
            let html = `
            <h2 style="margin: 0 0 10px 0; color: black;">Comments</h2>
            <hr>
            <div style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
        `;

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
                </div>
            `;
            });

            html += `</div>`; // close scrollable div

            document.getElementById('commentContent').innerHTML = html;
            document.getElementById('commentModal').style.display = 'flex';
        }


        function closeModal() {
            document.getElementById('commentModal').style.display = 'none';
        }
    </script>

</body>

</html>