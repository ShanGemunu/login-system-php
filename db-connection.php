<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbName = "loginsystem_php";
$port = "3307";

// Create connection
try{
  $conn = new mysqli($servername, $username, $password, $dbName, $port);
}catch(Exception $e){
  die("Connection failed: <br>".$e);
}

// Check connection
// if ($conn->connect_error) {
//   die("Connection failed: " . $conn->connect_error);
// }

// $query = "INSERT INTO test (firstname, lastname, email, log_password)
// VALUES ('mark','zuck','mark@fb.com','mark_1234')";

// $query = "SELECT * FROM test";

// if($result = $conn->query($query)){
//   $a = $result;
//   var_dump($a);
// }else{
//   echo "data fetching failed.";
// }

// Perform query
// if ($result = $conn -> query("SELECT * FROM Persons")) {
//   echo "Returned rows are: " . $result -> num_rows;
//   // Free result set
//   $result -> free_result();
// }

// Create database
// $createDatabase = "CREATE DATABASE loginsystem_php";
// if ($conn->query($createDatabase) === TRUE) {
//   echo "Database created successfully";
// } else {
//   echo "Error creating database: " . $conn->error;
// }

// sql to create table
// $createTable = "CREATE TABLE users (
//   id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//   user_name VARCHAR(30) NOT NULL,
//   email VARCHAR(50) NOT NULL,
//   hashed_password VARCHAR(255) NOT NULL,
//   reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//   )";

if ($result = $conn -> query($createTable)) {
    echo "table created sucessfully";
}else{
  echo "failed";
}

$conn->close();

// try{
//   if ($result = $conn -> query("SELECT * FROM test WHERE id=20")) {
//       var_dump($result);
//       while($row = $result->fetch_assoc()) {
//         print_r($row);
//         echo "<br>";
//       }
//   }else{
//     echo "failed";
//   }
// }catch(Exception){
//   echo "qurey error.";
// }

// if ($conn->query($createTable) === TRUE) {
//   echo "Table test created successfully";
// } else {
//   echo "Error creating table: " . $conn->error;
// }

// $session_id = session_id();

// echo 'Session ID: ' . $session_id . "<br>";

// PHP_SESSION_DISABLED if sessions are disabled.                  0
// PHP_SESSION_NONE if sessions are enabled, but none exists.      1
// PHP_SESSION_ACTIVE if sessions are enabled, and one exists.     2

// try{
//   if ($result = $conn -> query("SELECT * FROM test WHERE firstname='mark'")) {
//       var_dump($result->num_rows);
//       while($row = $result->fetch_assoc()){
//           var_dump($row);
//       }
//   }else{
//     echo "failed";
//   }
// }catch(Exception){
//   echo "qurey error.";
// }
?>

