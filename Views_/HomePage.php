<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="http://localhost/public/assets/css/main.css">
    <!-- Include Bootstrap CSS -->
    <link href="public/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class=" m-0 border-0 m-0 border-0">
<ul class="nav-bar">
  <?php
    require_once __DIR__."/NavBar.php";
  ?>
  <div>
    <li><a class="active" href="/login">Login</a></li>
    <li><a class="active" href="/register">Register</a></link>
    <li class="homepage-logout-button">
        <form action="/homepage" method="post">
            <button class="btn btn-primary" type="submit" name="logout_button">Log out</button>
        </form>
    </li>
  </div>     
</ul>

<div class="d-flex align-items-center justify-content-center mt-5">
    <p class="fs-1 fw-normal"> <?php 
    if(isset($_SESSION['currentUser']) and isset($_SESSION['currentUserType'])){
      $currentUser = $_SESSION['currentUser'];
      $currentUserType = $_SESSION['currentUserType'];
      echo "<h3> hi $currentUser </h3>";
      echo "<h6> $currentUserType </h6>";
    } 
    ?>
    </p>
</div>

<!-- Include Bootstrap JS -->
<script src="public/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
 