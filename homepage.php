<?php
session_start();

$result = null;

if(isset($_POST['logout-button'])){
  session_destroy();
  header("Location: /demo/login-system-php/login.php");
}

if (isset($_SESSION["currentUser"])) {
  echo "hi ".$_SESSION["currentUser"].".you are logged in.";
} else {
  header("Location: /demo/login-system-php/login.php");
}
?>
  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h4><?php echo $result ?></h4>
    <form action="homepage.php" method="post">
        <button type="submit" name="logout-button">Log out</button>
    </form>
</body>
</html>