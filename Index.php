<?php
session_start();
require_once('Controller/Login.php');
require_once('Controller/Cart.php');
require_once('Controller/Register.php');
require_once('Controller/Order.php');
require_once('Controller/DataLoad.php');
require_once('Controller/Product.php');
require_once('Middleware/Authentication.php');



// session variables -> token, loginPageStatus, registerPageStatus

// echo "starting stage of index<br>";
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

startMiddleware($request, $method);

function startMiddleware($request, $method){
    
    // do initial product data loading if application doesn't have products 
    // if(strlen(file_get_contents(__DIR__ . '\cache\data\products.txt')) === 0){
    //     $dataLoad = new DataLoadOld();
    //     if($dataLoad->getConn()){
    //         $dataLoad->fetchProductsAndIds();
    //     }
    // }

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
            $userType = $authStatus[1]->userType;
            $_SESSION['currentUserType'] = $userType;
            switchControls($request, $method, true, $userType);
        }else{
            resetCart();   // reset cart for next authenticated user 
            switchControls($request, $method, false, null);
        }
    }else{
        resetCart();    // reset cart for next authenticated user 
        switchControls($request, $method, false, null);
    }
}

function switchControls($request,$method, $isUserAuthenticated, $userType){
    $authentication = new Authentication();

    // to get response about whether products in db sucessfully updated or not
    // array, object(Order) -> 
    function excec($productsIdsAndNewQuantities, $order){
        $product = new Product();
        $isUpdateProductsDb = $product->subtractProductsQuantity($productsIdsAndNewQuantities);

        $isOrderLogAdded = $order->storeOrderLog();
        $isOrderDetailsLogAdded = $order->storeOrderDetailsLog();

        if($isUpdateProductsDb and $isOrderLogAdded and $isOrderDetailsLogAdded){
            $_SESSION['cartStatus'] = "Order make sucessfully.";
        }else{
            $_SESSION['cartStatus'] = "Couldn't make order, try again.";
        }
        header("Location: /cart"); 
    }

    // validate inputs which send post requests to /products
    // array or other tpye -> [bool, string]
    function validateInputsProducts($decodedJsonData){
        if(!(is_array($decodedJsonData))) return [false, "invalid inputs"];
     
        if(count($decodedJsonData)===0) return [false, "invalid inputs"];

        // need to do more sanitization
        return [true, "valid inputs"];

    }

    switch ([$request, $method]) {

        case ['/auth','POST']:
            require_once(__DIR__ . '\Auth.php');
            break;

        case ['/testlogin','GET']:
            require_once(__DIR__ . '\glogin.php');
            break;

        case ['/testing','POST']:
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
                
                $userEmailAndType = $login->userEmailAndType;
                // return authenticated user email
                $userEmail = $userEmailAndType[0];
                // return authenticated user type
                $userType = $userEmailAndType[1];

                // set session using token 
                $token = $authentication->createToken($userEmail, $userType);
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
        // ------------- implement backend validation for username 
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
        
        // get requests to /users to view all users access only for admins
        case ['/users','GET']:
            if($isUserAuthenticated and $userType === "admin"){
                require_once(__DIR__ . '\View\UsersPage.php');
            }else{
                header("Location:/error");
            }
            
            break;
        
        // get requests to /products
        case ['/products','GET']:
            // access only for admins to view ui to add products quantity
            if($isUserAuthenticated and $userType === "admin"){
                require_once(__DIR__ . '\View\AdminProductPage.php');
                $_SESSION['adminProductPageStatus'] = null;
                break;
            }
            // access for all users
            require_once(__DIR__ . '\View\ProductPage.php');
            break;
        
        // route only need to load product data
        case ['/products/load-products','GET']:
            $dataLoad = new DataLoad();
            $products = $dataLoad->loadProducts();
            header('Content-Type: application/json; charset=utf-8');
            echo $products;

            break;
        
        // post requests to /products to add products quantity  -> send json response
        case ['/products','POST']:

            if($isUserAuthenticated and $userType === "admin"){   
                // ^^^^^^^^^^^^^^^^^^^^^ sanitize inputs  -- need to improve sanitize consider all request data types

                $jsonData = file_get_contents('php://input');
                // $sanitizedJsonData = htmlspecialchars($jsonData,ENT_QUOTES);
                $decodedJsonData = json_decode($jsonData, true);
                
                $result = validateInputsProducts($decodedJsonData);

                $isInputsValid = $result[0];

                if(!($isInputsValid)){
                    $data = "error - invalid inputs";
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($data);
                    break;       
                }

                $product = new Product();
                $result = $product->addProductQuantity($decodedJsonData);
                $isProductsUpdated = $result[0];
 
                if($isProductsUpdated){
                    $_SESSION['adminProductPageStatus'] = "Product updated successfully";
                    $data = "success";
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($data);
                }else{
                    $data = "failed";
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($data);
                }
                
            }else{
                $data = "access denied";
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($data);
            }

            break;
        
        
        // get requests to cart page
        case ['/cart','GET']:
            if($isUserAuthenticated){
                require_once(__DIR__ . '\View\CartPage.php');
                $_SESSION['cartStatus'] = null;
            }else{
                header("Location:/homepage");
            }
            
            break;
        

        // post requests to add products to cart
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
        
        // post requests to /order for making order and updating products quantity 
        case ['/order','POST']:

            if($isUserAuthenticated){
                $order = new Order();
                $result = $order->checkProductEligibility();
                // check products eligibility 
                if($result[0]){
                    // passing products ids and new quantities of products
                    excec($result[1], $order);
                }else{
                    $_SESSION['cartStatus'] = "Couldn't make order since exceed products quantity.";
                    header("Location: /cart"); 
                }
            }else{
                header("Location: /error"); 
            }

            break;

        // get requests to /orders 
        case ['/orders','GET']:
            if($isUserAuthenticated){
                $order = new Order();
                $result = $order->getOrders();
               
                $isGetOrders = $result[0];
                $currentUserOrders = $result[1];
                
                if($isGetOrders and $currentUserOrders){
                    $_SESSION['currentUserOrders'] = $currentUserOrders;
                }else{
                    $_SESSION['currentUserOrders'] = null;
                }

                require_once(__DIR__ . '\View\OrderPage.php');
            }else{
                header("Location: /error");
            }

            break;
            
        // error page
        default:
            require_once(__DIR__ . '\View\ErrorPage.php');
    }
}




