<?php
    session_start();
    include('connection.php');
    $connect = new Connect(); 
    $connection = $connect->getConnection(); 

    if (!isset($_SESSION['user'])) {
        echo "<script> window.alert ('Please Login First') </script>";
        echo "<script> window.location= 'index.php' </script>";
    }

    // Mock data for categories
    $categories = [
        'popular' => [
            ['title' => 'Cleaning & Maintenance', 'details' => ['Restroom Cleaning', 'Classroom Cleaning', 'Office Cleaning', 'Hallways & Staircase Cleaning', 'Outdoor Cleaning & Gardening']],
            ['title' => 'IT & Technology Support', 'details' => ['Internet & Wi-Fi Issues', 'Software Requests', 'Software Updates & Installation', 'Online Learning Platforms', 'Data Security & Privacy,System Access & Password Issues']],
            ['title' => 'Parking & Transportation', 'details' => ['Parking Issues', 'Ferry Transportation']]
        ],
        'unused' => [
            ['title' => 'Cleaning & Maintenance', 'details' => ['Restroom Cleaning', 'Classroom Cleaning', 'Office Cleaning', 'Hallways & Staircase Cleaning', 'Outdoor Cleaning & Gardening']],
            ['title' => 'IT & Technology Support', 'details' => ['Internet & Wi-Fi Issues', 'Software Requests', 'Software Updates & Installation', 'Online Learning Platforms', 'Data Security & Privacy,System Access & Password Issues']],
            ['title' => 'Parking & Transportation', 'details' => ['Parking Issues', 'Ferry Transportation']]
        ]
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QA Manager Category List Page</title>
    <link rel="stylesheet" href="styles.css">
    <style>
   @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background-color: white;
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
    padding: 12px; /* Slightly increased padding for a more comfortable look */
    margin: 10px 0;
    background: #ddd;
    border: none;
    color: black;
    cursor: pointer;
    text-align: center; /* Center the text inside the button */
    font-family: 'Poppins', sans-serif; /* Apply Poppins font to buttons */
    font-size: 16px; /* Adjust font size for readability */
    border-radius: 10px; /* Set the corner radius to 10px */
    transition: background 0.3s, color 0.3s;
}

.btn:hover {
    background: rgb(89, 64, 122);
    color: white;
}

.logout {
    margin-top: auto;
    background: #3c9a72;
    padding: 12px; /* Consistent padding */
    border: none;
    color: white;
    cursor: pointer;
    width: 100%;
    text-align: center; /* Center the text inside the button */
    font-family: 'Poppins', sans-serif; /* Apply Poppins font */
    font-size: 16px; /* Adjust font size */
    border-radius: 10px; /* Set the corner radius to 10px */
    transition: background 0.3s, color 0.3s;
}

.logout:hover {
    background: rgb(89, 64, 122); /* Change color on hover */
    color: white;
}


.content {
    flex: 1;
    padding: 20px;
    background: rgb(89, 64, 122);
    color: white;
    overflow-y: auto; /* Prevents any content from overflowing the container */
    box-sizing: border-box; /* Ensures padding is included within the container's size */
}


header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap; /* Allow the elements to wrap and not overflow */
}

input[type="text"] {
    padding: 10px;
    width: 50%;
    border-radius: 5px;
    border: 1px solid #ccc;
}

/* Styling for the user info section */
/* User Info Section: Stack Name and QA Manager vertically and center-align */
/* User Info Section: Stack Name and QA Manager vertically and center-align */
.user-info {
    display: flex;
    flex-direction: column; /* Stack Name and QA Manager vertically */
    align-items: center; /* Center the text horizontally in the vertical stack */
    gap: 5px; /* Add space between 'Name' and 'QA Manager' */
    margin-right: 21px; /* Adjusted margin to 21px */
}



.categories {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.category-card {
    background: #fff;
    padding: 52px 91px 46px 36px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    position: relative;
    color: black;
    overflow: hidden; /* To ensure no image spills out */
}

.category-image {
    width: 50px; /* Set width to 50px */
    height: 50px; /* Set height to 50px */
    object-fit: cover; /* Ensure the image covers the space without distortion */
    border-radius: 5px; /* Optional: rounded corners for the image */
}



.arrow {
    position: absolute;
    bottom: 10px;
    right: 10px;
    font-size: 20px;
    cursor: pointer;
}

.delete {
    position: absolute;
    bottom: 10px;
    right: 10px;
    font-size: 20px;
    color: red;
    cursor: pointer;
}

.popular-categories, .unused-categories {
    margin-bottom: 25px;
}

input[type="text"] {
    padding: 12px; /* Increased padding for more space */
    width: 50%;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-family: 'Poppins', sans-serif;
    font-size: 16px; /* Increased font size for better readability */
    box-sizing: border-box; /* Ensures padding doesn't mess with the width */
    transition: border-color 0.3s ease; /* Smooth transition for border color */
}

input[type="text"]:focus {
    border-color: #3c9a72; /* Highlight border color on focus */
    outline: none; /* Remove default outline */
}

header a {
    display: inline-block;
    color: white; /* Set the text color */
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    text-decoration: underline; /* Underline the text */
    padding: 0; /* Remove padding since we no longer need it */
    background: none; /* Remove background */
    text-align: center;
    transition: color 0.3s; /* Transition color on hover */
}

</style>

</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>Logo</h2>
            <button class="btn">Categories</button>
            <button class="btn">Idea Reports</button>
            <button class="logout">Log Out</button>
        </aside>
        <main class="content">
        <header>
    <input type="text" placeholder="Search Categories">
    <div class="user-info">
        <span>Name</span>
        <span>QA Manager</span>
    </div>
    <a href="#">Add new category</a>
</header>
<section>
        <h3>Popular Categories</h3>
        <div class="categories">
            <?php foreach ($categories['popular'] as $category) { ?>
                <div class="category-card">
                    <!-- Image Integration -->
                    <img src="images/dummy_category.png" alt="Category Image" class="category-image">
                    
                    <h4><?php echo $category['title']; ?></h4>
                    <p><?php echo implode(', ', $category['details']); ?></p>
                    <span class="arrow">&rarr;</span>
                </div>
            <?php } ?>
        </div>
        <h3>Unused Categories</h3>
        <div class="categories">
            <?php foreach ($categories['unused'] as $category) { ?>
                <div class="category-card">
                    <!-- Image Integration -->
                    <img src="images/dummy_category.png" alt="Category Image" class="category-image">
                    <h4><?php echo $category['title']; ?></h4>
                    <p><?php echo implode(', ', $category['details']); ?></p>
                    <span class="delete">&#128465;</span>
                </div>
            <?php } ?>
        </div>
    </section>
        </main>
    </div>
</body>
</html>
