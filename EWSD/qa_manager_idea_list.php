<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Idea by Department</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #4B3574;
            color: white;
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

        .reactions {
            display: flex;
            gap: 15px;
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

<div class="container">
    <a href="#" class="back-btn">← Back</a>
    <h2>Idea by <span>Department</span></h2>

    <?php
    $ideas = [
        ["date" => "22.02.2025", "likes" => 100, "dislikes" => 10, "comments" => 5],
    ];

    foreach ($ideas as $idea): ?>
        <div class="card">
            <div class="user-info">
                <div class="user-left">
                    <div class="avatar">👤</div>
                    <div>
                        <p class="dept-name">Department Name</p>
                        <p class="date"><?= $idea['date'] ?></p>
                    </div>
                </div>
                <span class="subcategory">Sub Category</span>
            </div>

            <p class="idea-text">
                xxxxxxxx – xxxxxx – xxxxxx – xxxxx – xxxxx – xxxx – xxx – xxxx xxxxxxxx – xxxxx – xxxxxx – xxxx – xx – xxxx xxxxxxxx
            </p>

            <div class="reactions">
                <button><?= $idea['likes'] ?> 👍</button>
                <button><?= $idea['dislikes'] ?> 👎</button>
                <button onclick="openModal()"><?= $idea['comments'] ?> 💬</button>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="footer-section">
        <p class="note">You can download only after final closure date</p>
        <button class="download-btn">⬇️ Download</button>
    </div>
</div>

<!-- MODAL -->
<div id="commentModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; font-family:'Poppins', sans-serif;">
    <div style="background:white; width:600px; max-width:90%; border-radius:10px; overflow:hidden;">
        <div style="background:#1e1e1e; padding:20px; color:white;">
            <h3 style="margin:0; font-size:18px;">Comment Dialogue</h3>
        </div>
        <div style="padding: 20px;">
            <h2 style="margin:0 0 10px 0; color:black;">Comments <span style="float:right; font-weight:400;">{finalClosureDate}</span></h2>
            <hr>

            <?php for ($i = 0; $i < 5; $i++): ?>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin: 20px 0;">
                    <div style="display: flex; gap: 15px;">
                        <div style="width: 50px; height: 50px; background: #222; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">👤</div>
                        <div>
                            <p style="margin: 0; font-weight: 600; color: #2e7166;">Department Name</p>
                            <p style="margin: 0; font-size: 14px; color: #444;">Comment, Comment, Comment, Comment, Comment</p>
                        </div>
                    </div>
                    <p style="color: #666; font-size: 14px;">17.2.2025</p>
                </div>
            <?php endfor; ?>
        </div>

        <div style="border-top: 1px solid #ccc; display: flex; align-items: center; padding: 20px; gap: 10px;">
        <input type="text" placeholder="Leave your thoughts here" style="flex:1; padding: 14px; border: 1px solid #999; border-radius: 8px; font-family: 'Poppins', sans-serif;">

            <button style="border: none; background: none; font-size: 24px; cursor: pointer;">📤</button>
        </div>
        <div style="text-align:right; padding: 10px 20px;">
            <button onclick="closeModal()" style="padding: 8px 16px; border: none; background: #ccc; border-radius: 6px; font-weight: 600; cursor:pointer;">Close</button>
        </div>
    </div>
</div>

<!-- JS for modal -->
<script>
    function openModal() {
        document.getElementById('commentModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('commentModal').style.display = 'none';
    }
</script>

</body>
</html>
