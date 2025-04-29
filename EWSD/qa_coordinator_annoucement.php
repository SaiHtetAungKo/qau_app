<?php
session_start();
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();

// Check whether user is logged in
if (!isset($_SESSION['userID'])) {
  echo "<script>
      alert('Please Login First');
      window.location = 'index.php';
  </script>";
  exit();
}

$userID = $_SESSION['userID'];

// Get department ID from database
$query = "SELECT department_id FROM users WHERE user_ID = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $departmentID = $row['department_id'];
    // You can now use $departmentID anywhere on this page
} else {
    // Handle error if no department found
    echo "<script>alert('User department not found');</script>";
}

if  (isset($_POST['btnsubmit'])){
  $announcetitle = $_POST['title'];
  $description = $_POST['description'];
  $department_id = $_POST['d-id'];

  $insert = "INSERT INTO annoucement(department_id, announce_title, description)
            VALUES ('$department_id','$announcetitle','$description')";
    $ret = mysqli_query($connection, $insert);
    echo "<script>window.alert('Anounncement upload successfully!');</script>";
    echo "<script>window.location='qa_coordinator_annoucement.php'</script>";
}

// Fetch user data from session
$userName = $_SESSION['userName'];
$userProfileImg = $_SESSION['userProfile'] ?? 'default-profile.jpg'; // Default image if none is found
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
.form-container {
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-width: 600px;
  padding-top:25px;
}

label {
  font-size: 1.2rem;
}
strong{
  color: White;
  text-decoration: none;
}
input,
textarea {
  background-color: white;
  border: none;
  border-radius: 8px;
  padding: 12px;
  font-size: 1rem;
  color: #333;
  width: 100%;
  box-sizing: border-box;
}

textarea {
  resize: vertical;
}

button {
  align-self: lea;
  padding: 10px 40px;
  background-color: #7ee0c2;
  color: white;
  font-weight: bold;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #67cbb0;
}
</style>
    <title>Quality Assurance | Admin Home</title>
</head>

<body>
  <form action="qa_coordinator_annoucement.php" method="POST">
    <div class="admin-container">


        <!-- <div class="nav flex-column gap-3 px-2 py-5 mh-100">
                    <div class="logo text-center">
                        <h2>LOGO</h2>
                    </div>
                    <div class="d-flex flex-column">
                        <a class="nav-link" href="register.php"><b>User Registration</b></a>
                        <a class="nav-link" href="change_password.php"><b>Change Password</b></a>

                        <a class="nav-link mt-auto bg-danger text-white rounded text-center" href="logout.php" onclick="return confirm('Do You Want To Log Out?')">Log Out</a>
                    </div>
                </div> -->
        <div class="side-nav">
            <div class="logo text-center">
                <h2>LOGO</h2>
            </div>
            <a class="nav-link" href="qa_coordinator_home.php"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a class="nav-link" href="qa_coordinator_staff_list.php"><i class="fa-solid fa-users"></i> Staff List</a>
            <!-- <a class="nav-link" href="qa_coordinator_request_idea.php"><i class="fa-regular fa-comment"></i> Request Idea</a> -->
            <!-- <a class="nav-link" href="qa_coordinator_idea_report.php"><i class="fa-regular fa-lightbulb"></i> Idea Reports</a> -->
            <a class="nav-link-active" href="qa_coordinator_annoucement.php"><i class="fa-regular fa-lightbulb"></i> Annoucement</a>
            <!-- <a class="nav-link" href="register.php"><b>User Registration</b></a> -->
            <!-- <a class="nav-link" href="change_password.php"><b>Change Password</b></a> -->
            <a class=" logout" href="logout.php" onclick="return confirm('Do You Want To Log Out?')">Log Out</a>
        </div>
        <div class="dash-section">
            <header class="dash-header">
                <div class="search-input">
                    <input type="search" placeholder="Search" aria-label="Search">
                </div>
                <div class="user-display">
                    <img src="<?php echo htmlspecialchars($userProfileImg); ?>"
                        alt="Profile Image">
                    <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                </div>
            </header>
            <div class="form-container">
                <label for="title" name="a-title"><u><strong>Announcement Title</strong></u></label>
                <input type="text" id="title" name="title" placeholder="Enter title..." />

                <label for="description" name="a-title"><strong>Description</strong></label>
                <textarea id="description" name="description" rows="8" placeholder="Enter description..."></textarea>

                <input type="hidden" name="d-id" value="<?php echo htmlspecialchars($departmentID);?>" >

                <button type="submit" name="btnsubmit">Post</button>
            </div>

        </div>
        </form>
</body>

</html>