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
    <title>Add New Category</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: rgb(89, 64, 122);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: black;
        }

        .form-container {
            background: white;
            padding: 30px 40px;
            border-radius: 15px;
            width: 400px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .form-container h2 {
            margin-top: 0;
            color: rgb(89, 64, 122);
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 500;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        .form-actions {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .form-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .submit-btn {
            background-color: #3c9a72;
            color: white;
        }

        .submit-btn:hover {
            background-color: #317c5d;
        }

        .cancel-btn {
            background-color: #ccc;
            color: black;
        }

        .cancel-btn:hover {
            background-color: #aaa;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add New Category</h2>
        <form action="save_category.php" method="POST">
            <label for="categoryTitle">Category Title:</label>
            <input type="text" id="categoryTitle" name="categoryTitle" required>

            <label for="categoryDetails">Category Details (comma separated):</label>
            <textarea id="categoryDetails" name="categoryDetails" rows="5" required></textarea>

            <div class="form-actions">
                <button type="submit" class="submit-btn">Submit</button>
                <button type="button" class="cancel-btn" onclick="window.location.href='home.php'">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>
