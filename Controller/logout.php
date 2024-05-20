<?php

session_start();

if(isset($_POST['logout_button'])){
  session_destroy();
  header("Location: ../View/login-page.php");
}




