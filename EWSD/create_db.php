<?php
  $servername = "localhost";
  $username = "root";
  $password = "";

  // Create db connection
  $connection = new mysqli($servername, $username, $password);

  // Check db connection
  if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
  }

  // Create qa database
  $sql = "CREATE DATABASE quality_assurance";
  if ($connection->query($sql) === TRUE) {
    echo "<p>QA Database is created successfully.</p>";
  } 
  else {
    echo "Error in creating database: " . $connection->error;
  }

  $connection->close();
?>