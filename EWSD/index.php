<?php
include('connection.php');
$errorMessage = "";
$emailValue = isset($_GET['email']) ? $_GET['email'] : ""; // Retain email input after submit

if (isset($_GET['error'])) {
  if ($_GET['error'] == "email") {
    $errorMessage = "This email doesn't exist!";
  } elseif ($_GET['error'] == "password") {
    $errorMessage = "Incorrect password!";
  } elseif ($_GET['error'] == "empty") {
    $errorMessage = "All fields are required!";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quality Assurance | User Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <script src="qa_script.js"></script>
</head>

<body class="d-flex justify-content-center align-items-center vh-100 bg-light">

  <div class="login-container text-center">
    <?php
    if (isset($_GET['error']) && $_GET['error'] === 'deactivated') {
      echo "<p style='color:red;'>Your account has been deactivated. Please contact an administrator.</p>";
    }
    ?>
    <h3>LOGIN</h3>
    <p>Dear, Please Login Here</p>

    <form action="login_function.php" method="POST" class="grid row-gap-3" onsubmit="return validateForm()">
      <div class="mb-3">
        <input class="form-control <?php echo ($errorMessage == "This email doesn't exist!") ? 'border-danger' : ''; ?>"
          type="email" id="txtEmail" name="txtEmail" placeholder="Email" value="<?php echo htmlspecialchars($emailValue); ?>" />
        <small id="emailMessage" class="text-danger d-block text-start">
          <?php if ($errorMessage == "This email doesn't exist!") echo "<i class='fa-solid fa-circle-info'></i> $errorMessage"; ?>
        </small>
      </div>

      <div class="mb-3 password-wrapper">
        <input class="form-control <?php echo ($errorMessage == "Incorrect password!") ? 'border-danger' : ''; ?>"
          type="password" id="txtPassword" name="txtPassword" placeholder="Password" />
        <i class="fa-solid fa-eye-slash eye-icon" id="eyePassword" onclick="passwordVisibility()"></i>
        <small id="passwordMessage" class="text-danger d-block text-start">
          <?php if ($errorMessage == "Incorrect password!") echo "<i class='fa-solid fa-circle-info'></i> $errorMessage"; ?>
        </small>
      </div>

      <button id="login" class="btn btn-primary w-100">Login</button>
    </form>

  </div>

  <script>
    function passwordVisibility() {
      let passwordField = document.getElementById("txtPassword");
      let eyeIcon = document.getElementById("eyePassword");

      if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
      } else {
        passwordField.type = "password";
        eyeIcon.classList.remove("fa-eye");
        eyeIcon.classList.add("fa-eye-slash");
      }
    }

    function validateForm() {
      let email = document.getElementById("txtEmail");
      let password = document.getElementById("txtPassword");
      let emailMessage = document.getElementById("emailMessage");
      let passwordMessage = document.getElementById("passwordMessage");
      let valid = true;

      // Reset error messages
      emailMessage.innerHTML = "";
      passwordMessage.innerHTML = "";
      email.classList.remove("border-danger");
      password.classList.remove("border-danger");

      if (email.value.trim() === "") {
        emailMessage.innerHTML = "Email is required!";
        email.classList.add("border-danger");
        valid = false;
      }

      if (password.value.trim() === "") {
        passwordMessage.innerHTML = "Password is required!";
        password.classList.add("border-danger");
        valid = false;
      }

      return valid; // Prevent form submission if false
    }

    function checkEmail() {
      let email = document.getElementById("txtEmail").value;
      let emailMessage = document.getElementById("emailMessage");
      let loginButton = document.getElementById("login");

      // Reset message and disable login button if email is empty
      if (email.trim() === "") {
        emailMessage.innerHTML = "";
        loginButton.disabled = true;
        return;
      }

      // AJAX request to check if email exists
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "check_email.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      // Handle the response from the PHP script
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          if (xhr.responseText === "not_exists") {
            // Email doesn't exist
            emailMessage.innerHTML = `<i class="fa-solid fa-circle-info"></i> This email doesn't exist!`;
            loginButton.disabled = true; // Disable login button if email doesn't exist
          } else {
            // Email exists
            emailMessage.innerHTML = "";
            loginButton.disabled = false; // Enable login button if email exists
          }
        }
      };

      // Send the email as POST data to the check_email.php script
      xhr.send("email=" + encodeURIComponent(email));
    }
  </script>
</body>

</html>