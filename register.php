<?php
include 'db-connection.php';
session_start();

$result = null;

function registerNewUser(){
  global $servername, $username, $password, $dbName, $port,$result;

  // chreck if user already logged in
  if(isset($_SESSION["currentUser"])){
    $result = "<p>Already user logged in. To register new user first log out curent user.</p>";
    return null;
  }
  
  // validate user inputs
  if($_POST['user_name'] and $_POST['email'] and filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) and $_POST['password']){
    
    // Create db connection
    try{
      $conn = new mysqli($servername, $username, $password, $dbName, $port);
    }catch(Exception $e){
      $result =  "<p>DB connection falied and couldn't register user!</p>";
      return null;
    }
    
    // check if existing user email in DB as new input user email
    $checkQuery = "SELECT * FROM users WHERE email='".$_POST['email']."'";
    $tempResult = $conn -> query($checkQuery);
    if($tempResult->num_rows > 0){
      $result = "<p>Email provided already registered, try different email.</p>";
      $conn->close();
      return null;
    }
    

    // if no existing user email as inputed user email then continue
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $insertQuery = "INSERT INTO users (user_name, email, hashed_password)
    VALUES ('".$_POST['user_name']."','".$_POST['email']."','".$hashedPassword."')";

    if ($conn -> query($insertQuery)) {
      $conn->close();
      $_SESSION['currentUser'] = $_POST['user_name'];
      header("Location: homepage.php");
    }else{
      $result =  "<p>Something went wrong, couldn't register user!</p>";
    }

    $conn->close();
  }else{
    $result =  "<p>Make sure to fill all the required fields OR provide vaild email.</p>";
  }
}

if(isset($_POST["register_button"])){
  registerNewUser();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <!-- Include Bootstrap CSS -->
  <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3 m-0 border-0 m-0 border-0">
  <div class="d-flex align-items-center justify-content-center">
    <form action="" method="post" action="register.php" class="w-25 border p-3 rounded">
      <h4>Register here</h4>
      <div class="mb-3">
        <label for="" class="form-label">User Name</label>
        <input class="form-control" type="text" name="user_name" id="">
      </div>
      <div class="mb-3">
        <label class="form-label" for="">Email</label>
        <input class="form-control" type="email" name="email" id="">
      </div>
      <div class="mb-3">
        <label for="" class="form-label">Password</label>
        <input class="form-control" type="password" name="password" id="">  
      </div><br>
      <div class="d-flex flex-column align-items-end">
        <button type="submit" name="register_button" class="btn btn-primary w-50">Register</button>
      </div>
    </form>
  </div>
  <div class="d-flex align-items-center justify-content-center mt-4 text-danger">
      <?php echo $result; ?>
  </div>

  <!-- Include Bootstrap JS -->
  <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>