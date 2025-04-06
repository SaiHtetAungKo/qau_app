<?php
session_start();
include('connection.php');
$connect = new Connect(); 
$connection = $connect->getConnection(); 

if (!isset($_SESSION['user'])) {
    echo "<script> alert('Please Login First'); window.location = 'index.php'; </script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>New Category</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #563d7c;
      color: white;
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

.back-button:hover {
  background-color: #4a3a6b;
}

  </style>
</head>
<body>
  <div class="top-right">
    <div class="name">Name</div>
    <div class="role">QA Manager</div>
  </div>
  <a href="qa_manager_home.php" class="back-button">← Back</a>

  <div class="container">
    <h1>New Category</h1>
    <form action="save_category.php" method="POST">
      <label for="mainCategory">Main Category</label>
      <input type="text" id="mainCategory" name="mainCategory" placeholder="Enter your main category" required>

      <label for="subCategory">Sub Category</label>
      <input type="text" id="subCategory" name="subCategory[]" placeholder="Enter your sub category" required>

      <button type="button" class="add-sub-btn" onclick="addSubCategory()">Add Another Sub Category</button>

      <button type="submit" class="create-btn">Create</button>
    </form>
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
