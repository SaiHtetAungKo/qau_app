<?php

session_start();
include('connection.php');
include('users_table.php');
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$connect = new Connect();
$connection = $connect->getConnection();

if (!isset($_SESSION['userID'])) {
    echo "<script>
        alert('Please Login First');
        window.location = 'index.php';
    </script>";
    exit();
}
$user_id = $_SESSION['userID'];
$userName = $_SESSION['userName'];

$isDisabled = false;

if ($user_id) {
    $query = "SELECT account_status FROM users WHERE user_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $user['account_status'] !== 'active') {
        $isDisabled = true;
    }
} else {
    // User not logged in
    $isDisabled = true;
}
$userName = $_SESSION['userName'];
$userProfileImg = $_SESSION['userProfile'] ?? 'default-profile.jpg'; // Default image if none is found


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quality_assurance";  // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

if (isset($_GET['action']) || isset($_POST['action'])) {
    $action = $_GET['action'] ?? $_POST['action'];


    if ($action === 'get_main_categories') {
        $result = $conn->query("SELECT MainCategoryID, MainCategoryTitle FROM maincategory");
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
        exit;
    }

    if ($action === 'get_sub_categories') {
        $main_id = (int)($_GET['main_id'] ?? $_POST['main_id']);
        $result = $conn->query("SELECT SubCategoryID, SubCategoryTitle FROM subcategory WHERE MainCategoryID = $main_id");
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
        exit;
    }
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if file is uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // Validate image type
        $fileTmp = $_FILES['file']['tmp_name'];
        $check = getimagesize($fileTmp);
        if ($check === false) {
            echo "The uploaded file is not a valid image.";
            exit;
        }

        $fileName = basename($_FILES['file']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

        $uploadDir = 'Images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $targetPath = $uploadDir . $fileName;
        $baseFileName = pathinfo($fileName, PATHINFO_FILENAME);
        $counter = 1;
        while (file_exists($targetPath)) {
            // Generate new filename with a counter: "image (1).jpg", "image (2).jpg", etc.
            $newFileName = $baseFileName . ' (' . $counter . ').' . $fileExt;
            $targetPath = $uploadDir . $newFileName; // Update the path with the new filename
            $counter++; // Increment the counter
        }


        // Move uploaded file to the target directory
        if (move_uploaded_file($fileTmp, $targetPath)) {
            // Get form data
            $userID = 1; // Replace this with actual user ID (e.g., from $_SESSION)
            $subCategoryID = $_POST['subCategory'];
            $suggestion = htmlspecialchars($_POST['suggestion']);


            // Insert data into the 'idea' table
            $stmt = $conn->prepare("INSERT INTO ideas (userID, SubCategoryID, description, image_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $userID, $subCategoryID, $suggestion, $targetPath);

            if ($stmt->execute()) {

                // Email notification


                $mail = new PHPMailer(true);

                try {
                    // Mailtrap SMTP settings
                    $mail->isSMTP();
                    $mail->Host       = 'sandbox.smtp.mailtrap.io';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = '883613232c3b68'; // Replace with your Mailtrap credentials
                    $mail->Password   = '9684fa048f7638';
                    $mail->Port       = 2525;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

                    $mail->setFrom('noreply@example.com', 'Idea Box');
                    $mail->addAddress('admin@example.com'); // Change to recipient (e.g., admin or yourself)

                    $mail->isHTML(true);
                    $mail->Subject = 'New Idea Submitted';
                    $mail->Body    = "
                                        <p>Someone posted Idea</p>
                                    ";

                    $mail->send();
                    echo "Idea uploaded successfully! Email sent.";
                } catch (Exception $e) {
                    echo "Idea uploaded, but email failed to send. Error: {$mail->ErrorInfo}";
                }

                echo "Idea uploaded successfully!";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Failed to upload image.";
        }
    } else {
        echo "No file uploaded or upload error.";
    }

    $conn->close();
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Upload Idea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css" />
    <style>
        /* Fullscreen Container */
        body,
        html {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        /* Row to Fill Height */
        .form-row {
            flex-grow: 1;
            display: flex;
        }

        /* File Upload Box */
        .upload-box {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 40px;
            background-color: #fafafa;
            transition: 0.3s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            /* Stack items vertically */
            align-items: center;
            justify-content: center;
            height: calc(100vh - 200px);
            position: relative;
            text-align: center;
        }

        #fileName {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        /* Highlight on Drag Over */
        .upload-box.dragover {
            border-color: #007bff;
            background-color: #e9f5ff;
        }

        /* Upload Icon */
        .upload-icon {
            font-size: 50px;
            /* Adjust size */
            margin-bottom: 10px;
            color: #007bff;
        }

        /* Form Fields */
        textarea {
            resize: none;
        }

        button {
            width: 100%;
        }

        input[type="checkbox"] {
            accent-color: #007bff;
        }

        input[type="checkbox"]:checked+label {
            color: #007bff;
        }

        #mainCategory,
        #subCategory {
            border: 1px solid black !important;
            height: 50px;
            border-radius: 10px;

        }

        #suggestion {
            border: 1px solid black !important;
            border-radius: 10px;

        }

        hr {
            display: block;
            /* Ensure it is visible */
            border: 1px solid #000;
            /* Add a visible border */
            margin: 20px 0;
            /* Add spacing */
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 class="font-weight-bold">Upload Idea</h2>
        <div class="text-end font-weight-bold">Closure Date: <span id="closureDate"></span></div>
        <hr />

        <?php if ($isDisabled): ?>
            <div class="alert alert-danger mt-2">Your account is not active. You cannot post ideas.</div>
        <?php else: ?>
        <form id="ideaForm" action="upload.php" method="POST" enctype="multipart/form-data" class="mt-3">
            <div class="row form-row">
                <!-- File Upload Box -->
                <div class="col-md-6">
                    <div class="upload-box" id="dropZone">
                        <input type="file" id="fileInput" name="file" class="d-none" accept="image/*" />
                        <div id="fileName">
                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                            <p>Drop Files to upload<br><small>(or click to select)</small></p>
                        </div>
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label for="mainCategory" class="mb-2">Main Category</label>
                        <select class="form-control" id="mainCategory" name="mainCategory" required>
                            <option value="">Choose main category</option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="subCategory" class="mb-2">Sub Category</label>
                        <select class="form-control" id="subCategory" name="subCategory" required disabled>
                            <option value="">Choose sub category</option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="suggestion" class="mb-2">Suggestion</label>
                        <textarea class="form-control" id="suggestion" name="suggestion" rows="10" placeholder="Enter Suggestion" required></textarea>
                    </div>

                    <div class="form-group mb-4">
                        <input type="checkbox" id="terms" required />
                        <label for="terms">I have agreed to <a href="#">Terms & Conditions</a></label>
                    </div>

                    <button type="submit" class="btn btn-success">Post</button>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load main categories
        $.ajax({
            url: 'upload_idea_page.php',
            type: 'GET',
            dataType: 'json',
            data: {
                action: 'get_main_categories'
            },
            success: function(data) {
                const mainSelect = $('#mainCategory');
                mainSelect.empty().append('<option value="">Choose main category</option>');
                $.each(data, function(i, category) {
                    mainSelect.append(`<option value="${category.MainCategoryID}">${category.MainCategoryTitle}</option>`);
                });
            },
            error: function() {
                alert('Failed to load main categories.');
            }
        });

        // Load subcategories
        $('#mainCategory').on('change', function() {
            const mainCategoryId = $(this).val();
            const subSelect = $('#subCategory');

            if (mainCategoryId) {

                subSelect.prop('disabled', false);

                $.ajax({
                    url: 'upload_idea_page.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        action: 'get_sub_categories',
                        main_id: mainCategoryId
                    },
                    success: function(data) {
                        subSelect.empty().append('<option value="">Choose sub category</option>');
                        $.each(data, function(i, sub) {
                            subSelect.append(`<option value="${sub.SubCategoryID}">${sub.SubCategoryTitle}</option>`);
                        });
                    },
                    error: function() {
                        alert('Failed to load sub categories.');
                    }
                });
            } else {
                subSelect.prop('disabled', true);
                subSelect.html('<option value="">Choose sub category</option>');
            }
        });


        $(document).ready(function() {
            const dropZone = $('#dropZone');
            const fileInput = $('#fileInput');
            const fileName = $('#fileName');

            // Open file select dialog when clicking the upload box
            dropZone.on('click', function(e) {
                if (e.target !== fileInput[0]) {
                    fileInput.click();
                }
            });

            // Handle file select
            fileInput.on('change', function() {
                if (this.files && this.files[0]) {
                    handleFile(this.files[0]);
                }
            });

            // Drag and Drop Events
            dropZone.on('dragover', function(e) {
                e.preventDefault();
                dropZone.addClass('dragover');
            });

            dropZone.on('dragleave', function() {
                dropZone.removeClass('dragover');
            });

            dropZone.on('drop', function(e) {
                e.preventDefault();
                dropZone.removeClass('dragover');
                let files = e.originalEvent.dataTransfer.files;
                if (files.length) {
                    handleFile(files[0]);
                }
            });

            // Handle File Selection
            function handleFile(file) {

                if (!file.type.startsWith('image/')) {
                    alert('Only image files are allowed!');
                    fileInput.val(''); // Reset input
                    return;
                }

                // Show file name in the box
                fileName.html(`<i class="fas fa-file-alt upload-icon"></i>
                                <p>${file.name}</p>`);
            }

            $('#ideaForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                // Basic front-end validation
                if (!$('#fileInput').val()) {
                    alert('Please upload a file.');
                    return;
                }

                if (!$('#mainCategory').val() || !$('#subCategory').val() || !$('#suggestion').val()) {
                    alert('Please fill all fields.');
                    return;
                }

                if (!$('#terms').is(':checked')) {
                    alert('You must agree to the Terms & Conditions.');
                    return;
                }

                // Prepare form data
                let formData = new FormData(this);

                // Send data via AJAX
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {

                        $('#ideaForm')[0].reset(); // Reset form
                        $('#fileName').html(`<i class="fas fa-cloud-upload-alt upload-icon"></i>
                                 <p>Drop Files to upload<br><small>(or click to select)</small></p>`);

                        window.location.href = 'staff_home_2.php';

                    },
                    error: function() {
                        alert('Something went wrong. Please try again.');
                    }
                });
            });

        });
    </script>
</body>

</html>