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

// 1. Top 3 Popular Categories
$topQuery = "SELECT 
                mc.MainCategoryID,
                mc.MainCategoryTitle,
                COUNT(i.idea_id) AS idea_count
             FROM ideas i
             JOIN subcategory sc ON i.SubCategoryID = sc.SubCategoryID
             JOIN maincategory mc ON sc.MainCategoryID = mc.MainCategoryID
             GROUP BY mc.MainCategoryID, mc.MainCategoryTitle
             ORDER BY idea_count DESC
             LIMIT 3";

$topResult = mysqli_query($connection, $topQuery);
$topCategories = [];
while ($row = mysqli_fetch_assoc($topResult)) {
    $topCategories[] = $row;
}

// 2. Unused Categories (No ideas at all)
$unusedQuery = "SELECT 
                   mc.MainCategoryID,
                   mc.MainCategoryTitle
                FROM maincategory mc
                LEFT JOIN subcategory sc ON mc.MainCategoryID = sc.MainCategoryID
                LEFT JOIN ideas i ON sc.SubCategoryID = i.SubCategoryID
                WHERE i.idea_id IS NULL
                GROUP BY mc.MainCategoryID, mc.MainCategoryTitle";

$unusedResult = mysqli_query($connection, $unusedQuery);
$unusedCategories = [];
while ($row = mysqli_fetch_assoc($unusedResult)) {
    $unusedCategories[] = $row;
}

// Mock department data
$departments = [
    ['name' => 'Department A', 'ideas' => 100, 'percentage' => '80%', 'contributors' => 18],
    ['name' => 'Department B', 'ideas' => 120, 'percentage' => '75%', 'contributors' => 21],
    ['name' => 'Department C', 'ideas' => 90, 'percentage' => '85%', 'contributors' => 14],
    ['name' => 'Department D', 'ideas' => 110, 'percentage' => '78%', 'contributors' => 20],
    ['name' => 'Department E', 'ideas' => 95, 'percentage' => '88%', 'contributors' => 17],
    ['name' => 'Department F', 'ideas' => 80, 'percentage' => '65%', 'contributors' => 10],
    ['name' => 'Department G', 'ideas' => 80, 'percentage' => '65%', 'contributors' => 10],
    ['name' => 'Department H', 'ideas' => 80, 'percentage' => '65%', 'contributors' => 10]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QA Manager Category and Idea Report</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

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
        .categories { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
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
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>Logo</h2>
            <button class="btn" onclick="toggleCategories()">Categories</button>
            <button class="btn" onclick="toggleIdeaReports()">Idea Reports</button>
            <button class="logout" onclick="confirmLogout()">Log Out</button>
        </aside>

        <main class="content">
            <header>
                <input type="text" placeholder="Search">
                <div class="user-info">
                    <span><strong>Name</strong></span><br>
                    <span>QA Manager</span>
                </div>
                <a href="add_category.php">Add new category</a>
            </header>

            <!-- Category Section -->
            <div id="category-sections" style="display: block;">
                <h3>🔥 Top 3 Popular Categories</h3>
                <div class="categories">
                    <?php foreach ($topCategories as $cat) { ?>
                        <div class="category-card">
                            <img src="images/dummy_category.png" alt="" class="category-image">
                            <h4>
                            <a href="qa_manager_idea_list.php?category_name=<?php echo urlencode($cat['MainCategoryTitle']); ?>">
    <?php echo htmlspecialchars($cat['MainCategoryTitle']); ?>
</a>

    </h4>
                            <p>Total Ideas: <?php echo $cat['idea_count']; ?></p>
                            <span class="arrow">&rarr;</span>
                        </div>
                    <?php } ?>
                </div>

                <h3>🗑️ Unused Categories</h3>
                <div class="categories">
                    <?php foreach ($unusedCategories as $cat) { ?>
                        <div class="category-card unused">
                            <img src="images/dummy_category.png" alt="" class="category-image">
                            <h4>
        <a href="qa_manager_idea_list.php?category_id=<?php echo $cat['MainCategoryID']; ?>">
            <?php echo htmlspecialchars($cat['MainCategoryTitle']); ?>
        </a>
    </h4>
                            <p>No ideas submitted</p>
                            <span class="delete">&#128465;</span>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Idea Report Section -->
            <div id="idea-report-section">
                <h2>Idea Reports by Each Department</h2>
                <div class="categories">
                    <?php foreach ($departments as $dept) { ?>
                        <div class="category-card">
                            <div class="green-box"></div>
                            <h4><?php echo htmlspecialchars($dept['name']); ?></h4>
                            <p>
                                Total Number of ideas: <?php echo $dept['ideas']; ?><br>
                                Percentage of ideas: <?php echo $dept['percentage']; ?><br>
                                Number of contributors: <?php echo $dept['contributors']; ?>
                            </p>
                            <span class="arrow">&rarr;</span>
                        </div>
                    <?php } ?>
                </div>

                <div class="download-section">
                    <p>You can download only after final closure date</p>
                    <button class="download-btn">&#8681; Download</button>
                </div>
            </div>
        </main>
    </div>

    <script>
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
