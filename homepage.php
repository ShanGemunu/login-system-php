<?php
session_start();

$result = null;

if(isset($_POST['logout_button'])){
  session_destroy();
  header("Location: login.php");
}

if (isset($_SESSION["currentUser"])) {
  $result = $_SESSION["currentUser"];
} else {
  header("Location: login.php");
}
?>
  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <!-- Include Bootstrap CSS -->
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3 m-0 border-0 m-0 border-0">
  <div class="d-flex align-items-center justify-content-center mt-5">
    <p class="fs-1 fw-normal"><?php echo "hi $result," ?></p><br>
  </div>
  <div class="d-flex align-items-center justify-content-center">
  <form action="homepage.php" method="post">
    <button class="btn btn-primary" type="submit" name="logout_button">Log out</button>
  </form>
  </div>

  <!-- Include Bootstrap JS -->
  <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>