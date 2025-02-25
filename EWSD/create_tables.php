<?php
    include('connection.php'); 

    // db connection
    $connect = new Connect(); 
    $connection = $connect->getConnection(); // Use getConnection() method

    // roles table

    // $create = "CREATE TABLE roles (
    //     role_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    //     role_type VARCHAR(255),
    //     role_description TEXT,
    //     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    //     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    // )";

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

    $create = "CREATE TABLE users (
        user_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        role_id INT,
        department_id INT,
        user_name VARCHAR(255),
        user_email VARCHAR(255),
        user_phone VARCHAR(255),
        user_password VARCHAR(255),
        account_status VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        Foreign Key (role_id) references roles(role_id),
        Foreign Key (department_id) references departments(department_id)
    )";

    $sql = mysqli_query($connection, $create);

    if ($sql) {
        echo "Users Table Is Created Successfully.";
    } else {
        echo "Error in Database Query: " . mysqli_error($connection); 
    }
?>
