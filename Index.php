<?php
session_start();
require_once('Controller/Login.php');
require_once('Controller/Cart.php');
require_once('Controller/Register.php');
require_once('Middleware/Authentication.php');
require_once('Middleware/DataLoad.php');

// session variables -> token, loginPageStatus, registerPageStatus

// echo "starting stage of index<br>";
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

startMiddleware($request, $method);

function startMiddleware($request, $method){
    
    // do initial product data loading if application doesn't have products 
    if(strlen(file_get_contents(__DIR__ . '\cache\data\products.txt')) === 0){
        $dataLoad = new DataLoad();
        if($dataLoad->getConn()){
            $dataLoad->fetchProductsAndIds();
        }
    }

    // reset cart if no user logged in
    function resetCart(){
        file_put_contents(__DIR__ . '\cache\data\cart.txt',"");
    }

    // do authentication 
    if(isset($_COOKIE["token-php-login"]) and isset($_SESSION["token"])){
        $authentication = new Authentication();
        $authStatus = $authentication->validateToken($_COOKIE["token-php-login"],$_SESSION["token"]);
        $isValidUser = $authStatus[0];

        if($isValidUser){
            $_SESSION["currentUser"] = $authStatus[1]->userId;
            switchControls($request, $method, true);
        }else{
            resetCart();   // reset cart for next authenticated user 
            switchControls($request, $method, false);
        }
    }else{
        resetCart();    // reset cart for next authenticated user 
        switchControls($request, $method, false);
    }
}

function switchControls($request,$method, $isUserAuthenticated){
    $authentication = new Authentication();
    switch ([$request, $method]) {

        case ['/auth','GET']:
            require_once(__DIR__ . '\Auth.php');
            break;

        case ['/testlogin','GET']:
            require_once(__DIR__ . '\glogin.php');
            break;

        case ['/testing','GET']:
            require_once(__DIR__ . '\DBtesting.php');
            break;
        

        // handle get requsets to /login
        case ['/login','GET']:
            if($isUserAuthenticated){
                header("Location:/homepage");
            }else{
                require_once(__DIR__ . '\View\LoginPage.php');
                $_SESSION['loginPageStatus'] = null;
            }
            
            break;

        // handle post requsets to /login 
        case ['/login','POST']:   
            if($isUserAuthenticated){
                $_SESSION['loginPageStatus'] = "Already user logged in, to log in another user first log out.";
                header("Location:/login");
                break;
            }    
            
            // sanitize inputs
            $email = htmlspecialchars($_POST['email'],ENT_QUOTES);
            // $email = urlencode($email);
            $password = htmlspecialchars($_POST['password'],ENT_QUOTES);
            // $password = urlencode($password);

            $login = new Login($email,$password);
            
            // if user authenticated succesfully
            if($login->isValidUser){
                // return authenticated user email
                $userEmail = $login->result;

                // set session using token 
                $token = $authentication->createToken($userEmail,"user");
                $_SESSION['token'] = $token;

                // set cookie in browser
                setcookie("token-php-login",$token);
                header("Location:/homepage");
            }elseif($login->result == "DB connection failed and couldn't log in, try again!"){
                $_SESSION['loginPageStatus'] = null;
                header("Location:/login");
                $message = "DB connection failed and couldn't log in, try again!";
                echo "<script>console.log('" . addslashes($message) . "');</script>";
            }else{
                $_SESSION['loginPageStatus'] = $login->result;
                header("Location:/login");
            }

            break;

        // handle get requsets to /register
        case ['/register','GET']:         
            require_once(__DIR__ . '\View\RegisterPage.php');
            $_SESSION['registerPageStatus'] = null;
            break;

        // handle post requests to /register
        case ['/register','POST']:
            if($isUserAuthenticated){
                $_SESSION['registerPageStatus'] = "Already user logged in, to register new user first log out.";
                require_once(__DIR__ . '\View\RegisterPage.php');
                break;
            }
            
            // sanitize inputs
            $userName = htmlspecialchars($_POST['user_name'],ENT_QUOTES);
            // $userName = urlencode($userName);
            $email = htmlspecialchars($_POST['email'],ENT_QUOTES);
            // $email = urlencode($email);
            $password = htmlspecialchars($_POST['password'],ENT_QUOTES);
            // $password = urlencode($password);

            $register = new Register($userName, $email , $password);

            if($register->isUserRegistered){
                // return registered user email
                $userEmail = $register->result;

                // set session using token 
                $token = $authentication->createToken($userEmail,"user");
                $_SESSION['token'] = $token;

                // set cookie in browser
                setcookie("token-php-login",$token);
                header("Location:/homepage");
            }elseif($register->result == "DB connection failed and couldn't log in, try again!"){
                header("Location:/register");
                $message = "DB connection failed and couldn't log in, try again!";
                echo "<script>console.log('" . addslashes($message) . "');</script>";
            }else{
                $_SESSION['registerPageStatus'] = $register->result;
                header("Location:/register");
            }
            break; 
        
        case ['/users','GET']:
            if($isUserAuthenticated){
                require_once(__DIR__ . '\View\UsersPage.php');
            }else{
                header("Location:/homepage");
            }
            
            break;
    
        case ['/products','GET']:
            require_once(__DIR__ . '\View\ProductPage.php');
            break;
    
        case ['/cart','GET']:
            if($isUserAuthenticated){
                require_once(__DIR__ . '\View\CartPage.php');
            }else{
                header("Location:/homepage");
            }
            
            break;
    
        case ['/cart','POST']:
            if($isUserAuthenticated){
                // sanitize product id input
                $prodcutsAndDetails = file_get_contents(__DIR__ . '\cache\data\products.txt');
                $prodcutsAndDetails = json_decode($prodcutsAndDetails,true);
                if($_POST['product-id']<$prodcutsAndDetails[1][0][0] or $prodcutsAndDetails[2][0][0]<$_POST['product-id']){
                    header("Location:/error");
                    return null;
                }
                
                // if product id is valid then continue
                $cart = new Cart();
                if(isset($_POST['add'])){
                    $cart->addProduct($_POST['product-id']);
                    header("Location:/cart");
                }elseif(isset($_POST['remove'])){
                    $cart->removeProduct($_POST['product-id']);
                    header("Location:/cart");
                }else{
                    header("Location:/error");
                }
            }else{
                header("Location:/error");
            }

            
            
            break;

        // get requests to /homepage
        case ['/homepage','GET']:
            require_once(__DIR__ . '\View\HomePage.php');
            break;
        
        // post requsets to /homepage    -  to log out user
        case ['/homepage','POST']:
            if($isUserAuthenticated){
                // unset session and current user session 
                unset($_SESSION['token']);
                unset($_SESSION['currentUser']);
                // set cookie to expire
                setcookie("token-php-login", "", time()-3600);

                header("Location: /homepage");   
            }else{
                header("Location: /homepage");   
            }
            break;
    
        default:
            require_once(__DIR__ . '\View\ErrorPage.php');
    }
}




