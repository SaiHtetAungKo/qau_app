<?php
  include('connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quality Assurance | User Login</title>  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="qa_script.js"></script>
</head>
<body>  
  <h3>LOGIN</h3>
  <p>Dear, Please Login Here</p>
       
  <?php if (isset($_GET['fail'])) : ?>
      <script>alert('Sorry, Your Email or Password Is Incorrect')</script>
  <?php endif ?>
  
  <?php if (isset($_GET['success'])) : ?>
        <script>alert('Successfully Logout') </script>
  <?php endif ?>

  <form action="login_function.php" method="POST">

    <input type="email" name="txtEmail" placeholder="Email" required/>

    <input type="password" id="txtPassword" name="txtPassword" placeholder="Password" required/>  
    <!-- psw eye icon -->
    <i class="fa-solid fa-eye-low-vision" id="eyePassword" onclick="passwordVisibility('txtPassword', this)"></i> 

    <button id="btnLogin">Login</button>

  </form>
</body>
</html>