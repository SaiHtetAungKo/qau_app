<?php
    session_start();
    include('connection.php');
    $connect = new Connect(); 
    $connection = $connect->getConnection(); 

    if (!isset($_SESSION['user'])) {
        echo "<script> window.alert ('Please Login First') </script>";
        echo "<script> window.location= 'index.php' </script>";
    }

    $user_id = $_SESSION['user']['id']; // Assuming user ID is stored in session
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Home</title>
    <link rel="stylesheet" href="staff_style.css">
    <link rel="stylesheet" href="ideas_list.css">
    <script>
        function loadIdeas(filter) {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "ideas_list.php?filter=" + filter, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById("idea-list").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        document.addEventListener("DOMContentLoaded", function () {
            loadIdeas('all'); // Load all ideas by default
        });
    </script>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">Logo <br> <strong>AppName</strong></div>
            <nav>
                <ul>
                    <li class="active"><a href="#" onclick="loadIdeas('all'); return false;">Idea Feed</a></li>
                    <li><a href="#" onclick="loadIdeas('contributions'); return false;">Contributions</a></li>
                    <li><a href="#" onclick="loadIdeas('most_liked'); return false;">Most Liked</a></li>
                    <li><a href="#">Announcements</a></li>
                </ul>
            </nav>
            <div class="user-profile">
                <div class="avatar"></div>
                <div class="user-info">
                    <strong><?php echo $_SESSION['user']['name']; ?></strong>
                    <p><?php echo $_SESSION['user']['department']; ?></p>
                </div>
                <a href="logout.php" onclick="return confirm('Do You Want To Log Out?')">Log Out</a>
            </div>
        </aside>
        <main class="content">
            <header>
                <div class="search-bar">
                    <input type="text" placeholder="Which idea would you like to submit">
                </div>
            </header>
            <section id="idea-list">
                <!-- Ideas will be dynamically loaded here -->
            </section>
        </main>
    </div>
</body>
</html>
