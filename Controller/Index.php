<?php
require_once('Login.php');
require_once('Register.php');

// session variables -> currentUser, loginPageStatus, registerPageStatus
session_start();

Class Index{
    function handleRoutes($request, $method){
        switch ([$request, $method]) {
            // handle get requsets to /login
            case ['/login-system-php/login','GET']:
                if(isset($_SESSION['currentUser'])){
                    header("Location:/login-system-php/homepage");
                }else{
                    require_once(__DIR__ . '\..\View\LoginPage.php');
                    $_SESSION['loginPageStatus'] = null;
                }
                break;

            // handle post requsets to /login 
            case ['/login-system-php/login','POST']:   
                if(isset($_SESSION['currentUser'])){
                    $_SESSION['loginPageStatus'] = "Already user logged in, to log in another user first log out.";
                    header("Location:/login-system-php/login");
                    break;
                }    

                $login = new Login($_POST['email'],$_POST['password']);

                if($login->isValidUser){
                    $_SESSION['currentUser'] = $login->result;
                    header("Location:/login-system-php/homepage");
                }elseif($login->result == "DB connection failed and couldn't log in, try again!"){
                    $_SESSION['loginPageStatus'] = null;
                    header("Location:/login-system-php/login");
                    $message = "DB connection failed and couldn't log in, try again!";
                    echo "<script>console.log('" . addslashes($message) . "');</script>";
                }else{
                    $_SESSION['loginPageStatus'] = $login->result;
                    header("Location:/login-system-php/login");
                }
                break;

            // handle get requsets to /register
            case ['/login-system-php/register','GET']:         
                require_once(__DIR__ . '\..\View\RegisterPage.php');
                $_SESSION['registerPageStatus'] = null;
                break;

            // handle post requests to /register
            case ['/login-system-php/register','POST']:
                if(isset($_SESSION['currentUser'])){
                    $_SESSION['registerPageStatus'] = "Already user logged in, to register new user first log out.";
                    require_once(__DIR__ . '\..\View\RegisterPage.php');
                    break;
                }

                $register = new Register($_POST['user_name'], $_POST['email'] , $_POST['password']);

                if($register->isUserRegistered){
                    $_SESSION['currentUser'] = $register->result;
                    header("Location:/login-system-php/homepage");
                }elseif($register->result == "DB connection failed and couldn't log in, try again!"){
                    header("Location:/login-system-php/register");
                    $message = "DB connection failed and couldn't log in, try again!";
                    echo "<script>console.log('" . addslashes($message) . "');</script>";
                }else{
                    $_SESSION['registerPageStatus'] = $register->result;
                    header("Location:/login-system-php/register");
                }
                break;

            // get requests to /homepage
            case ['/login-system-php/homepage','GET']:
                if(isset($_SESSION['currentUser'])){
                    require_once(__DIR__ . '\..\View\HomePage.php');
                }else{
                    header("Location: /login-system-php/login");   
                }
                break;
            
            // post requsets to /homepage    - to log-out user
            case ['/login-system-php/homepage','POST']:
                if(isset($_SESSION['currentUser'])){
                    // unset currrentUser session variable
                    unset($_SESSION['currentUser']);
                    header("Location: /login-system-php/login");   
                }else{
                    header("Location: /login-system-php/login");   
                }
                break;

            default:
                require_once(__DIR__ . '\..\View\ErrorPage.php');
        }
    }
}

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$index = new Index();

$index->handleRoutes($request, $method);

