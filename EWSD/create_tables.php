<?php
include('connection.php');

// db connection
$connect = new Connect();
$connection = $connect->getConnection(); // Use getConnection() method

// roles table

// $create = "CREATE TABLE roles (
//         role_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
//         role_type VARCHAR(255),
//         role_description TEXT,
//         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//         updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//     )";

// $sql = mysqli_query($connection, $create);

// if ($sql) {
//     echo "Roles Table Is Created Successfully.";
// } else {
//     echo "Error in Database Query: " . mysqli_error($connection);
// }

// departments table

// $create = "CREATE TABLE departments (
//     department_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
//     department_name VARCHAR(255),
//     department_location TEXT,
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// )";

// $sql = mysqli_query($connection, $create);

// if ($sql) {
//     echo "Departments Table Is Created Successfully.";
// } else {
//     echo "Error in Database Query: " . mysqli_error($connection);
// }

// users table

// $create = "CREATE TABLE users (
//         user_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
//         role_id INT,
//         department_id INT,
//         user_name VARCHAR(255),
//         user_email VARCHAR(255),
//         user_phone VARCHAR(255),
//         user_password VARCHAR(255),
//         account_status VARCHAR(255),
//         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//         updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//         Foreign Key (role_id) references roles(role_id),
//         Foreign Key (department_id) references departments(department_id)
//     )";

// $sql = mysqli_query($connection, $create);

// if ($sql) {
//     echo "Users Table Is Created Successfully.";
// } else {
//     echo "Error in Database Query: " . mysqli_error($connection);
// }

// $create = "CREATE TABLE request_ideas (
//     requestIdea_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
//     title VARCHAR(255),
//     description TEXT,
//     closure_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     final_closure_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

// )";

// $sql = mysqli_query($connection, $create);

// if ($sql) {
//     echo "Request Idea Table Is Created Successfully.";
// } else {
//     echo "Error in Database Query: " . mysqli_error($connection);
// }

$create = "CREATE TABLE ideas (
    idea_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    userID INT, 
    requestIdea_id INT,
    title VARCHAR(255),
    description TEXT,
    status VARCHAR(255),
    anonymousSubmission Boolean,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    Foreign Key (userID) references users(user_id),
    Foreign Key (requestIdea_id) references request_ideas(requestIdea_id)

)";

$sql = mysqli_query($connection, $create);

if ($sql) {
    echo "Request Idea Table Is Created Successfully.";
} else {
    echo "Error in Database Query: " . mysqli_error($connection);
}
