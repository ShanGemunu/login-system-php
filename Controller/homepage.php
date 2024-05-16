<?php
include '../View/home-page.php';

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

echo generateHTMLPage($result);
