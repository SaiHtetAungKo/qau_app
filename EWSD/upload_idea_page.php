<?php
// Include and use the connection class
session_start();
include 'connection.php';

// Include PHPMailer classes manually
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if (!isset($_SESSION['userID'])) {
    echo "<script>
        alert('Please Login First');
        window.location = 'index.php';
    </script>";
    exit();
}

$sessionUserID = $_SESSION['userID'];

// Use PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$database = new Connect();
$conn = $database->getConnection();

// Assuming you have a way to get the logged-in user's department_id
$loggedInUserDepartmentId = 1; // Example, replace this with the actual logged-in user's department ID

// Get the QA Coordinator's email for the same department
$qaCoordinatorQuery = "SELECT user_email FROM users WHERE role_id = 3 AND department_id = $loggedInUserDepartmentId";
$qaCoordinatorResult = mysqli_query($conn, $qaCoordinatorQuery);

$qaCoordinatorEmails = [];
while ($row = mysqli_fetch_assoc($qaCoordinatorResult)) {
    $qaCoordinatorEmails[] = $row['user_email'];
}

// Handle AJAX request for subcategories
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['main_category_id'])) {
    $mainCatId = intval($_POST['main_category_id']);
    $result = mysqli_query($conn, "SELECT * FROM subcategory WHERE MainCategoryID = $mainCatId");

    echo '<option value="">Select Sub Category</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['SubCategoryID'] . '">' . htmlspecialchars($row['SubCategoryTitle']) . '</option>';
    }
    exit; // Stop the rest of the page from loading
}

$today = date('Y-m-d');

// Query to fetch request idea whose closure_date >= today's date
$requestQuery = "SELECT * FROM request_ideas WHERE closure_date >= '$today' ORDER BY closure_date ASC LIMIT 1";

$result = mysqli_query($conn, $requestQuery);
$latestRequest = mysqli_fetch_assoc($result);

// If there is an active request idea, fetch its details
if ($latestRequest) {
    $requestIdeaId = $latestRequest['requestIdea_id'];
    $closureDate = $latestRequest['closure_date'];
} else {
    $requestIdeaId = null;
    $closureDate = null;
}

$isDisabled = false;
$user_id = $_SESSION['userID'];
$userName = $_SESSION['userName'];
if ($user_id) {
    $query = "SELECT account_status FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
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

// Handle Idea Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idea_title'])) {
    // Collect form data
    $mainCatId = intval($_POST['main_category']);
    $subCatId = intval($_POST['sub_category']);
    $title = mysqli_real_escape_string($conn, $_POST['idea_title']);
    $description = mysqli_real_escape_string($conn, $_POST['idea_description']);
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;
    $userID = $sessionUserID; // Change to dynamic logged-in user ID if needed
    $status = 'active'; // or default value you use
    $imgPath = NULL;

    // Handle image upload if exists
    if (isset($_FILES['idea_image']) && $_FILES['idea_image']['error'] === 0) {
        $targetDir = "Images/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . '_' . basename($_FILES["idea_image"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["idea_image"]["tmp_name"], $targetFilePath)) {
            $imgPath = $targetFilePath;
        }
    }

    // If there's an active request idea, use its ID, otherwise set to NULL
    $requestIdeaIdToInsert = $requestIdeaId ? $requestIdeaId : NULL;

    // Insert into ideas table
    $query = "INSERT INTO ideas (userID, requestIdea_id, SubCategoryID, title, description, img_path, status, anonymousSubmission, created_at, updated_at)
              VALUES ('$userID', " .
        ($requestIdeaIdToInsert ? "'$requestIdeaIdToInsert'" : "NULL") . ",  '$subCatId', '$title', '$description', " .
        ($imgPath ? "'$imgPath'" : "NULL") . ",
              '$status', '$anonymous', NOW(), NOW())";

    if (mysqli_query($conn, $query)) {
        // Send email to QA Coordinator using PHPMailer
        if (!empty($qaCoordinatorEmails)) {
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'opengate171@gmail.com';
                $mail->Password = 'mnsh lxzg txel skbr';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                //Recipients
                $mail->setFrom('opengate171@gmail.com', 'Idea Submission System');

                // Add all QA Coordinators
                foreach ($qaCoordinatorEmails as $email) {
                    $mail->addAddress($email);
                }

                //Content
                $mail->isHTML(true);
                $mail->Subject = 'New Idea Submitted: ' . $title;
                $mail->Body    = "Hello QA Coordinator,<br><br>A new idea has been submitted.<br><br>" .
                    "<strong>Title:</strong> $title<br>" .
                    "<strong>Description:</strong> $description<br><br>" .
                    "Please review it.<br><br>" .
                    "Best regards,<br>Your Idea Submission System";

                $mail->send();
                echo "<script>alert('Idea uploaded successfully! Email sent to QA Coordinators.'); window.location.href='staff_home_2.php';</script>";
            } catch (Exception $e) {
                echo "<script>alert('Idea uploaded, but email could not be sent.'); window.location.href='upload_idea_page.php';</script>";
            }
        } else {
            echo "<script>alert('Idea uploaded, but no QA Coordinator emails found.'); window.location.href='upload_idea_page.php';</script>";
        }
    } else {
        echo "<script>alert('Error uploading idea.'); window.location.href='upload_idea_page.php';</script>";
    }
}



// Otherwise, normal page loading
$mainCategories = mysqli_query($conn, "SELECT * FROM maincategory WHERE Status = 'active'");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Idea</title>
    <style>
        /* Same CSS styling */
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }

        .upload-container {

            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            margin: auto;
        }

        .left-side,
        .right-side {
            padding: 20px;
        }

        .left-side {
            flex: 1;
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .upload-area {
            border: 2px dashed #ccc;
            border-radius: 10px;
            width: 100%;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            cursor: pointer;
            background-color: #fafafa;
        }

        .upload-area.dragover {
            background-color: #e0e0e0;
        }

        .right-side {
            flex: 2;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 12px 25px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .terms {
            font-size: 12px;
            color: #555;
            margin-bottom: 20px;
        }

        .upload-file-info {
            text-align: center;
            padding: 10px;
        }

        .upload-file-info img {
            width: 50px;
            height: 50px;
            margin-bottom: 10px;
        }

        .upload-file-info p {
            font-size: 14px;
            margin: 0;
        }

        .anonoymous {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .anonoymous input {
            width: fit-content;
            margin: 0px;
        }

        .terms {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .terms input {
            width: fit-content;
            margin: 0px;
        }

        .upload-header {
            display: flex;
            justify-content: space-between;
        }

        .line-space {
            width: 100%;
            height: 1px;
            background: #ddd;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .form-flex {
                flex-direction: column;
                align-items: stretch;
            }

            .left-side,
            .right-side {
                width: 100%;
                margin: 0;
                padding: 10px 0;
            }

            .upload-area {
                width: 100%;
                height: auto;
                min-height: 200px;
                border: 2px dashed #ccc;
                display: flex;
                justify-content: center;
                align-items: center;
                text-align: center;
                cursor: pointer;
            }

            .upload-header h2 {
                font-size: 1.5rem;

            }

            .request-idea-info p {
                width: 100% !important;
                word-wrap: break-word;
                font-size: 0.9rem;
            }

            .right-side label,

            .right-side select,
            .right-side textarea,
            .right-side button {
                width: 100%;
                font-size: 1rem;
            }

            #idea_title,
            #idea_description {
                box-sizing: border-box;
                /* Prevent overflow due to padding */
            }

            .right-side button {
                margin-top: 10px;
                padding: 10px;
            }

            .anonoymous,
            .terms {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <!-- Request Idea and Closure Date -->
    <!-- <?php if ($latestRequest): ?>
        <div class="request-idea-info">
            <p><strong>Latest Request Idea:</strong> <?= htmlspecialchars($latestRequest['title']) ?></p>
            <p><strong>Closure Date:</strong> <?= date('F j, Y', strtotime($closureDate)) ?></p>
        </div>
    <?php else: ?>
        <p>No active request ideas at the moment.</p>
    <?php endif; ?> -->
    <?php if ($isDisabled): ?>
        <div class="alert alert-danger mt-2">Your account is not active. You cannot post ideas.</div>
    <?php else: ?>

        <form id="ideaForm" action="" method="POST" enctype="multipart/form-data">
            <div class="upload-container">
                <div class="upload-header">
                    <h2>Upload Idea Form</h2>
                    <!-- Request Idea and Closure Date -->
                    <?php if ($latestRequest): ?>
                        <div class="request-idea-info">
                            <p style="width: 400px; word-wrap: break-word;">QA Coordinator Emails: <?= implode(', ', $qaCoordinatorEmails); ?></p>
                            <p><strong>Closure Date:</strong> <?= date('F j, Y', strtotime($closureDate)) ?></p>
                        </div>
                    <?php else: ?>
                        <p>No active request ideas at the moment.</p>
                    <?php endif; ?>
                </div>
                <div class="line-space"></div>

                <!-- Left Side: Image Upload -->
                <div class="form-flex" style="display: flex;">
                    <div class="left-side">
                        <div class="upload-area" id="uploadArea">
                            <div id="uploadText">
                                <p>Drag & Drop File Here<br>or<br>Click to Upload</p>
                            </div>
                            <input type="file" name="idea_image" id="fileInput" style="display: none;">
                        </div>
                    </div>

                    <!-- Right Side: Form Fields -->
                    <div class="right-side">

                        <!-- Main Category -->
                        <label for="main_category">Main Category</label>
                        <select name="main_category" id="main_category" required>
                            <option value="">Select Main Category</option>
                            <?php while ($row = mysqli_fetch_assoc($mainCategories)): ?>
                                <option value="<?= $row['MainCategoryID'] ?>"><?= htmlspecialchars($row['MainCategoryTitle']) ?></option>
                            <?php endwhile; ?>
                        </select>

                        <!-- Sub Category (dynamic) -->
                        <label for="sub_category">Sub Category</label>
                        <select name="sub_category" id="sub_category" disabled required>
                            <option value="">Select Sub Category</option>
                        </select>

                        <!-- Title -->
                        <label for="idea_title">Idea Title</label>
                        <input type="text" name="idea_title" id="idea_title" required>

                        <!-- Description -->
                        <label for="idea_description">Idea Description</label>
                        <textarea name="idea_description" id="idea_description" rows="5" style="resize: none;" required></textarea>

                        <!-- Anonymous Checkbox -->
                        <div class="anonoymous">
                            <input type="checkbox" name="anonymous" id="anonymous" value="1">
                            <label for="anonymous">Submit Anonymously</label>
                        </div>
                        <br>
                        <!-- Terms -->
                        <div class="terms">
                            <input type="checkbox" name="" id="">
                            By submitting, you agree to our Terms and Conditions.
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="submitIdea">Submit Idea</button>

                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
    <script>
        // Drag and Drop Upload Handling
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const uploadText = document.getElementById('uploadText');

        uploadArea.addEventListener('click', () => fileInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            fileInput.files = e.dataTransfer.files;
            showFileInfo(fileInput.files[0]);
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                showFileInfo(fileInput.files[0]);
            }
        });

        function showFileInfo(file) {
            let fileType = file.type;
            let iconSrc = '';

            if (fileType.startsWith('image/')) {
                iconSrc = 'https://img.icons8.com/fluency/48/image.png'; // Image Icon
            } else if (fileType === 'application/zip' || fileType === 'application/x-zip-compressed') {
                iconSrc = 'https://img.icons8.com/fluency/48/zip.png'; // Zip Icon
            } else if (fileType === 'application/pdf') {
                iconSrc = 'https://img.icons8.com/fluency/48/pdf.png'; // PDF Icon
            } else if (fileType.startsWith('application/msword') || fileType.startsWith('application/vnd.openxmlformats-officedocument.wordprocessingml.document')) {
                iconSrc = 'https://img.icons8.com/fluency/48/word.png'; // Word Icon
            } else {
                iconSrc = 'https://img.icons8.com/fluency/48/file.png'; // Default file icon
            }

            uploadText.innerHTML = `
                <div class="upload-file-info">
                    <img src="${iconSrc}" alt="file type">
                    <p>${file.name}</p>
                </div>
            `;
        }

        // AJAX to load Sub Categories
        document.getElementById('main_category').addEventListener('change', function() {
            var mainCatId = this.value;
            var subCatSelect = document.getElementById('sub_category');

            if (!mainCatId) {
                subCatSelect.innerHTML = '<option value="">Select Sub Category</option>';
                subCatSelect.disabled = true;
                return;
            }

            subCatSelect.disabled = false;
            subCatSelect.innerHTML = '<option>Loading...</option>';

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // Post to same page
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                subCatSelect.innerHTML = this.responseText;
            }
            xhr.send('main_category_id=' + mainCatId);
        });

        document.querySelector('.submitIdea').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent form submission

            const button = e.target;
            button.disabled = true; // Disable the button to prevent multiple clicks
            button.textContent = 'Loading...'; // Change text to indicate loading state

            // Submit the form immediately after showing the loading state
            document.getElementById('ideaForm').submit();
        });
    </script>

</body>

</html>