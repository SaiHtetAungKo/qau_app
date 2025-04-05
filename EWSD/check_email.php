
    <?php
    // Include your database connection
    include('connection.php');

    // Get the email from the AJAX request
    $email = isset($_POST['email']) ? $_POST['email'] : '';

    // Check if the email exists in the database
    if (!empty($email)) {
        $query = "SELECT * FROM users WHERE user_email = '$email'";
        $result = mysqli_query($connection, $query);

        if (mysqli_num_rows($result) > 0) {
            // Email exists
            echo "exists";
        } else {
            // Email doesn't exist
            echo "not_exists";
        }
    }
