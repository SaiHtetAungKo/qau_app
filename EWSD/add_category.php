<?php
session_start();
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();

// if (!isset($_SESSION['user'])) {
//     echo "<script> alert('Please Login First'); window.location = 'index.php'; </script>";
//     exit;
// }
if (isset($_POST['btnsubmit'])) {
  $MainCategoryTitle = $_POST['mainCategory'];
  $Description = $_POST['Description'];
  $Status = $_POST['Status'];
  $subCategory = $_POST['subCategory'];
  $subdescription = $_POST['Sub-Description'];



  $query = "SELECT * FROM MainCategory
			WHERE MainCategoryTitle='$MainCategoryTitle'";
  $ret = mysqli_query($connection, $query);
  $count = mysqli_num_rows($ret);

  if ($count > 0) {
    echo "<script>window.alert('Main Category Title already exist !');</script>";
    echo "<script>window.location='add_category.php'</script>";
  } else {
    $Insert = "INSERT INTO MainCategory (MainCategoryTitle, Description, Status, created_at, updated_at)
      VALUES ('$MainCategoryTitle', '$Description', '$Status', NOW(), NOW())";
    $ret = mysqli_query($connection, $Insert);

    if ($ret) {
      $last_id = mysqli_insert_id($connection);

      $Insert1 = "INSERT INTO SubCategory (MainCategoryID, SubCategoryTitle, Description, created_at, updated_at)
        VALUES ('$last_id', '$subCategory[0]', '$subdescription', NOW(), NOW())";

      mysqli_query($connection, $Insert1);

      echo "<script>window.alert('Main Category and Sub Category added successfully!');</script>";
      echo "<script>window.location='add_category.php'</script>";
    } else {
      echo "<script>window.alert('Failed to insert main category.');</script>";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>New Category</title>
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
      max-width: 600px;
      margin-left: 60px;
      padding: 50px 20px;
    }


    h1 {
      font-size: 2.5rem;
      margin-bottom: 40px;
      font-weight: 700;
    }

    label {
      display: block;
      font-size: 1.2rem;
      margin: 20px 0 10px;
      font-weight: 600;
    }

    input[type="text"] {
      width: 100%;
      padding: 15px;
      font-size: 1rem;
      border: none;
      border-radius: 10px;
      box-sizing: border-box;
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

    .add-sub-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #9de5d2;
      color: white;
      font-size: 1.1rem;
      font-weight: 600;
      padding: 12px;
      margin-top: 25px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
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
      margin-right: 21px;
    }

    .add-sub-btn::before {
      content: "+";
      font-size: 1.5rem;
      margin-right: 10px;
    }

    .create-btn {
      display: block;
      margin-top: 40px;
      padding: 15px 40px;
      font-size: 1.2rem;
      font-weight: 600;
      background-color: white;
      color: black;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      margin-left: auto;
    }

    .top-right {
      position: absolute;
      top: 20px;
      right: 30px;
      text-align: right;
    }

    .top-right .name {
      font-weight: bold;
    }

    .top-right .role {
      font-size: 0.9rem;
      margin-top: 2px;
    }

    .back-button {
      display: inline-block;
      margin: 30px 0 0 60px;
      font-size: 16px;
      color: white;
      background-color: #5f4b8b;
      padding: 10px 18px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 500;
      transition: background-color 0.3s ease;
    }

    .content {
      flex: 1;
      background: rgb(89, 64, 122);
      color: white;
      padding: 20px;
      overflow-y: auto;
    }

    .back-button:hover {
      background-color: #4a3a6b;
    }
  </style>
</head>

<body>

  <div class="admin-container">
    <div class="side-nav">
      <div class="logo text-center">
        <<img src="Images/logo.png" alt="logo" width="150px" style="margin: 8px 0px;">
      </div>
      <a class="nav-link-active" href="qa_manager_home.php"><i class="fa-solid fa-house"></i> Categories</a>
      <a class="nav-link" href="qa_manager_idea_summary.php"><i class="fa-solid fa-users"></i> Idea Reports</a>
      <a class=" logout" href="logout.php" onclick="return confirm('Do You Want To Log Out?')">Log Out</a>
    </div>

    <main class="content">
      <!-- <header>
               
                <div class="user-info">
                    <span><strong>Name</strong></span><br>
                    <span>QA Manager</span>
                </div>
                
            </header> -->
      <a href="qa_manager_home.php" class="back-btn">← Back</a>
      <h1>New Category Registration</h1>
      <form action="add_category.php" method="POST">
        <label for="mainCategory">Main Category</label>
        <input type="text" id="mainCategory" name="mainCategory" placeholder="Enter your main category" required>

        <label for="Description">Description</label>
        <input type="text" id="Description" name="Description" placeholder="Enter your Description" required>

        <label for="status">Status</label>
        <input type="text" id="Status" name="Status" placeholder="Enter your Status" required>

        <label for="subCategory">Sub Category</label>
        <input type="text" id="subCategory" name="subCategory[]" placeholder="Enter your sub category" required>

        <label for="Sub-Description">Sub-Description</label>
        <input type="text" id="Sub-Description" name="Sub-Description" placeholder="Enter your sub category" required>


        <button type="button" class="add-sub-btn" onclick="addSubCategory()">Add Another Sub Category</button>

        <button type="submit" name="btnsubmit" class="create-btn">Create</button>
      </form>
    </main>

  </div>


  <script>
    function addSubCategory() {
      const container = document.querySelector('form');
      const label = document.createElement('label');
      label.innerText = 'Sub Category';
      const input = document.createElement('input');
      input.type = 'text';
      input.name = 'subCategory[]';
      input.placeholder = 'Enter your sub category';
      input.required = true;
      input.style.marginTop = '10px';
      input.style.padding = '15px';
      input.style.borderRadius = '10px';
      input.style.border = 'none';
      input.style.width = '100%';
      input.style.boxSizing = 'border-box';
      container.insertBefore(label, container.lastElementChild);
      container.insertBefore(input, container.lastElementChild);
    }
  </script>
</body>

</html>