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
$userName = $_SESSION['userName'];
$userProfileImg = $_SESSION['userProfile'] ?? 'default-profile.jpg'; // Default image if none is found

// all Categories

$catQuery = "SELECT 
                mc.MainCategoryID,
                mc.MainCategoryTitle,
                sc.SubCategoryTitle,
                COUNT(i.idea_id) AS idea_count
            FROM maincategory mc
            LEFT JOIN subcategory sc ON sc.MainCategoryID = mc.MainCategoryID
            LEFT JOIN ideas i ON i.SubCategoryID = sc.SubCategoryID
            GROUP BY mc.MainCategoryID, mc.MainCategoryTitle
            ORDER BY idea_count DESC";

$catResult = mysqli_query($connection, $catQuery);
$allCategories = [];
while ($row = mysqli_fetch_assoc($catResult)) {
    $allCategories[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QA Manager Category and Idea Report</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; }
        .container { display: flex; height: 100vh; }
        .sidebar { width: 250px; background: white; color: black; padding: 20px; display: flex; flex-direction: column; align-items: center; }
        .sidebar h2 { margin-bottom: 20px; }
        .btn { width: 100%; padding: 12px; margin: 10px 0; background: #ddd; border: none; color: black; cursor: pointer; text-align: center; font-size: 16px; border-radius: 10px; transition: 0.3s; }
        .btn:hover { background: rgb(89, 64, 122); color: white; }
        .logout { margin-top: auto; background: #3c9a72; padding: 12px; color: white; border: none; width: 100%; border-radius: 10px; cursor: pointer; font-size: 16px; }
        .logout:hover { background: rgb(89, 64, 122); }
        .content { flex: 1; background: rgb(89, 64, 122); color: white; padding: 20px; overflow-y: auto; }
        header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 20px; }
        input[type="text"] { padding: 12px; width: 50%; border-radius: 10px; border: 1px solid #ccc; font-size: 16px; }
        .user-info { text-align: right; margin-right: 21px; }
        header a { color: white; text-decoration: underline; }
        .categories {
            display: grid;
            grid-template-columns: repeat(3, 1fr);  /* 3 columns in one row */
            gap: 20px;
        }
        .category-card { background: white; color: black; padding: 30px 20px; border-radius: 10px; position: relative; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
        .category-image { width: 40px; height: 40px; border-radius: 5px; margin-bottom: 10px; }
        .arrow, .delete { position: absolute; right: 15px; bottom: 15px; font-size: 20px; cursor: pointer; }
        .delete { color: red; }
        /* Section visibility */
        #category-sections, #idea-report-section { display: none; }
        /* Download section */
        .download-section { margin-top: 40px; display: flex; justify-content: space-between; align-items: center; }
        .download-section p { font-weight: bold; }
        .download-btn { background: #aee0d3; padding: 12px 20px; border: none; border-radius: 10px; font-size: 16px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 10px; }
        .green-box { width: 40px; height: 40px; background-color: #90d5c9; border-radius: 12px; margin-bottom: 10px; }
        
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
    </style>
</head>
<body>

    <div class="admin-container">
        <div class="side-nav">
            <div class="logo text-center">
                <h2>LOGO</h2>
            </div>
            <a class="nav-link" href="qa_manager_dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a class="nav-link-active" href="qa_manager_home.php"><i class="fa-solid fa-layer-group"></i> Categories</a>
            <a class="nav-link" href="qa_manager_idea_summary.php"><i class="fa-regular fa-lightbulb"></i> Idea Reports</a>
            <a class="nav-link" href="qa_manager_staff_list.php"><i class="fa-solid fa-users"></i> Staff List</a>
            <a class="nav-link" href="qa_manager_hidden_idea_list.php"><i class="fa-regular fa-eye-slash"></i> Hidden Idea List</a>
            <a class=" logout" href="logout.php" onclick="return confirm('Do You Want To Log Out?')">Log Out</a>
        </div>

        <main class="content">           
            <div class="qa-manager-dash-section">
                <header class="qa-manager-dash-header">
                    <a href="qa_manager_home.php" class="back-btn">← Back</a>
                    <input type="text" placeholder="Search">                   
                    <div class="qa-manager-user-display">
                        <img src="<?php echo htmlspecialchars($userProfileImg); ?>" alt="Profile Image">
                        <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                    </div>
                </header>            
            </div>
            <!-- Category Section -->
            <div id="category-sections" style="display: block;">

                <div style="display: flex; justify-content: space-between; align-items: center; margin: 10px 0;">
                    <h3>All Categories</h3>                     
                    <a href="add_category.php" class="view-all-cat">
                        <i class="fa-solid fa-plus"></i> Add
                    </a>
                </div>

                <div class="categories">
                    <?php foreach ($allCategories as $cat) { ?>
                        <div class="category-card">
                            <img src="images/dummy_category.png" alt="" class="category-image">
                            <h4>                           
                                <?php echo htmlspecialchars($cat['MainCategoryTitle']); ?>
                            </h4>
                            <h4>               
                                <?php echo htmlspecialchars($cat['SubCategoryTitle']); ?>
                            </h4>
                            <p>Total Ideas: <?php echo $cat['idea_count']; ?></p>
                            <a href="qa_manager_idea_list.php?category_name=<?php echo urlencode($cat['MainCategoryTitle']); ?>">
                            <span class="arrow">&rarr;</span>
                            </a>
                        </div>
                    <?php } ?>
                </div>               
            </div>

        </main>
    </div>

    <script>

        document.addEventListener("DOMContentLoaded", () => {
            const deletes = document.querySelectorAll(".delete");

            deletes.forEach(btn => {
                btn.addEventListener("click", function () {
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
                                // Reload just the unused categories section
                                location.reload(); // Or use AJAX to reload the section
                            } else {
                                alert("Error deleting category: " + data.error);
                            }
                        });
                    }
                });
            });
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
