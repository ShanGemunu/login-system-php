<?php
session_start();
require_once ('Bootstrap/Bootstrap.php');
require_once ('Middleware/Authorization.php');
// require_once ('Controller/Login.php');
// require_once ('Controller/Cart.php');
// require_once ('Controller/Register.php');
// require_once ('Controller/Order.php');
// require_once ('Controller/DataLoad.php');
// require_once ('Controller/Product.php');

require_once ('Routes/RoutesHandler.php');

// require_once ('Middleware/Logger.php');
// require_once ('Middleware/Exceptions/file-moved-failed.php');
// require_once ('Middleware/Exceptions/file-content-invalid.php');
// require_once ('Middleware/Exceptions/request-content-type-invalid.php');


// session variables -> token-session, loginPageStatus, registerPageStatus, 

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

try {
    // start initial services
    $bootsTrap = new Bootstrap();
    $bootsTrap->startInitialServices();

    // do authentiaction 
    $authorization = new Authorization();
    $authStatus = $authorization->doAuthorization();

    // handling routes
    $routesHandler = new RoutesHandler();
    if ($authStatus['isValidUser']) {
        $routesHandler->navigateRoutes_Registered_User($request, $method, $userType);
    } else {
        $routesHandler->navigateRoutes_Unregistered_User($request, $method);
    }
} catch (Exception $e) {

}


// function switchControls($request, $method, $isUserAuthenticated, $userType)
// {
//     $authentication = new Authentication();

//     // to get response about whether products in db sucessfully updated or not
//     // array, object(Order) -> 
//     function excec($productsIdsAndNewQuantities, $order)
//     {
//         $product = new Product();
//         $isUpdateProductsDb = $product->subtractProductsQuantity($productsIdsAndNewQuantities);

//         $isOrderLogAdded = $order->storeOrderLog();
//         $isOrderDetailsLogAdded = $order->storeOrderDetailsLog();

//         if ($isUpdateProductsDb and $isOrderLogAdded and $isOrderDetailsLogAdded) {
//             $_SESSION['cartStatus'] = "Order make sucessfully.";
//         } else {
//             $_SESSION['cartStatus'] = "Couldn't make order, try again.";
//         }
//         header("Location: /cart");
//     }

//     // validate inputs which send post requests to /products
//     // array or other tpye -> [bool, string]
//     function validateInputsProducts($decodedJsonData)
//     {
//         $output = ['isValidInputs' => false, 'result' => null];
//         $output['isValidInputs'] = true;
//         $output['result'] = "valid inputs";

//         if (!(is_array($decodedJsonData))) {
//             $output['isValidInputs'] = false;
//         }

//         if (count($decodedJsonData) === 0) {
//             $output['isValidInputs'] = false;
//             $output['result'] = "invalid inputs";
//         }

//         // need to do more sanitization
//         return $output;

//     }

//     // validate if order id send by browser belongs to current logged user 
//     // string -> bool
//     function validateOrderId($orderId)
//     {
//         $conn = new DbConnection();
//         $queries = new Queries($conn->conn);

//         $userId = $queries->getUserIdforOrder($orderId);
//         $userId = $userId['0'];

//         // check if order id is belongs to current logged user
//         if (!empty($userId) and ($userId['user_id'] == $_SESSION['currentUser'])) {
//             var_dump($userId);
//             return true;
//         } else {
//             return false;
//         }
//     }

//     switch ([$request, $method]) {

//         case ['/auth', 'POST']:
//             require_once (__DIR__ . '\Auth.php');
//             break;

//         case ['/testlogin', 'GET']:
//             require_once (__DIR__ . '\glogin.php');
//             break;

//         case ['/testing', 'GET']:
//             require_once (__DIR__ . '\DBtesting.php');
//             break;


//         // handle get requsets to /login
//         case ['/login', 'GET']:
//             if ($isUserAuthenticated) {
//                 header("Location:/homepage");
//             } else {
//                 require_once (__DIR__ . '\Views\LoginPage.php');
//                 $_SESSION['loginPageStatus'] = null;
//             }

//             break;

//         // handle post requsets to /login 
//         case ['/login', 'POST']:
//             if ($isUserAuthenticated) {
//                 $_SESSION['loginPageStatus'] = "Already user logged in, to log in another user first log out.";
//                 header("Location:/login");
//                 break;
//             }

//             // sanitize inputs
//             $email = htmlspecialchars($_POST['email'], ENT_QUOTES);
//             // $email = urlencode($email);
//             $password = htmlspecialchars($_POST['password'], ENT_QUOTES);
//             // $password = urlencode($password);

//             $login = new Login($email, $password);

//             // if user authenticated succesfully
//             if ($login->isValidUser) {

//                 $userIdAndType = $login->userIdAndType;
//                 // return authenticated user id
//                 $userId = $userIdAndType['id'];
//                 // return authenticated user type
//                 $userType = $userIdAndType['type'];

//                 // set session using token 
//                 $token = $authentication->createToken($userId, $userType);
//                 $_SESSION['token'] = $token;

//                 // set cookie in browser
//                 setcookie("token-php-login", $token);
//                 header("Location:/homepage");
//             } else {
//                 $_SESSION['loginPageStatus'] = $login->result;
//                 header("Location:/login");
//             }

//             break;

//         // handle get requsets to /register
//         case ['/register', 'GET']:
//             require_once (__DIR__ . '\Views\RegisterPage.php');
//             $_SESSION['registerPageStatus'] = null;
//             break;

//         // handle post requests to /register
//         // ------------- implement backend validation for username 
//         case ['/register', 'POST']:
//             if ($isUserAuthenticated) {
//                 header("Location:/register");
//                 break;
//             }

//             // sanitize inputs
//             $userName = htmlspecialchars($_POST['user_name'], ENT_QUOTES);
//             // $userName = urlencode($userName);
//             $email = htmlspecialchars($_POST['email'], ENT_QUOTES);
//             // $email = urlencode($email);
//             $password = htmlspecialchars($_POST['password'], ENT_QUOTES);
//             // $password = urlencode($password);

//             $register = new Register($userName, $email, $password);

//             if ($register->isUserRegistered) {
//                 // return registered user email
//                 $userEmail = $register->result;

//                 // fetch user id for email

//                 $conn = new DbConnection();
//                 $queries = new Queries($conn->conn);
//                 $userId = $queries->getUserId($email);

//                 // set session using token 
//                 $token = $authentication->createToken($userId, "user");
//                 $_SESSION['token'] = $token;

//                 // set cookie in browser
//                 setcookie("token-php-login", $token);
//                 header("Location:/homepage");
//             } else {
//                 $_SESSION['registerPageStatus'] = $register->result;
//                 header("Location:/register");
//             }
//             break;

//         // get requests to /users to view all users access only for admins
//         // hardcoded users
//         case ['/users', 'GET']:
//             if ($isUserAuthenticated and $userType === "admin") {
//                 require_once (__DIR__ . '\Views\UsersPage.php');
//             } else {
//                 header("Location:/error");
//             }

//             break;

//         // get requests to /products  
//         case ['/products', 'GET']:
//             // access only for admins to view ui to add products quantity
//             if ($isUserAuthenticated and $userType === "admin") {
//                 require_once (__DIR__ . '\Views\AdminProductPage.php');
//                 $_SESSION['adminProductPageStatus'] = null;
//                 break;
//             }
//             // access for all users
//             require_once (__DIR__ . '\Views\ProductPage.php');
//             break;

//         // route only need to load product data
//         case ['/products/load-products/card', 'GET']:
//             $dataLoad = new DataLoad();
//             $products = $dataLoad->loadProducts();
//             header('Content-Type: application/json; charset=utf-8');
//             echo $products;

//             break;

//         // route for load all products as bulk
//         case ['/products/load-products/pagination', 'POST']:
//             header('Content-Type: application/json; charset=utf-8');
//             if ($isUserAuthenticated and $userType === "admin") {
//                 try {
//                     // get the content type of sended data
//                     $contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : 'invalid';

//                     if (!($contentType === "application/x-www-form-urlencoded; charset=UTF-8")) {
//                         throw new RequestContentTypeInvalid();
//                     }

//                     $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
//                     $start = isset($_POST['start']) ? abs(intval($_POST['start'])) : 0;
//                     $length = isset($_POST['length']) ? abs(intval($_POST['length'])) : 10;
//                     $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : "";
//                     $orderColumnIndex = isset($_POST['order'][0]['column']) ? abs(intval($_POST['order'][0]['column'])) : 0;
//                     $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : "asc";

//                     $columns = ["id", "product_name", "price", "input_date", "quantity"];
//                     $orderColumn = $columns[$orderColumnIndex];

//                     $dataLoad = new DataLoad();
//                     $products = $dataLoad->loadProductsPagination($start, $length, $searchValue, $orderColumn, $orderDir);

//                     if (count($products) === 0) {
//                         echo json_encode("error");
//                         exit;
//                     }

//                     $totalRecords = 10;
//                     $filteredData = 1000013;

//                     $response = [
//                         "column" => $orderColumnIndex,
//                         "colName" => $orderColumn,
//                         "draw" => $draw,
//                         "recordsTotal" => $totalRecords,
//                         "recordsFiltered" => $filteredData,
//                         "data" => $products
//                     ];

//                     echo json_encode($response);
//                 } catch (Exception $e) {
//                     echo json_encode("invalid data send!");
//                 }

//             } else {
//                 echo json_encode("error");
//             }

//             break;

//         // post requests to /products to add products quantity  -> send json response
//         case ['/products', 'POST']:

//             if ($isUserAuthenticated and $userType === "admin") {
//                 // ^^^^^^^^^^^^^^^^^^^^^ sanitize inputs  -- need to improve sanitize consider all request data types

//                 try {
//                     $jsonData = $_POST[''];
//                     // $sanitizedJsonData = htmlspecialchars($jsonData,ENT_QUOTES);
//                     $decodedJsonData = json_decode($jsonData, true);

//                     $result = validateInputsProducts($decodedJsonData);

//                     $isInputsValid = $result['isValidInputs'];

//                     if (!$isInputsValid) {
//                         $data = "error - invalid inputs";
//                         header('Content-Type: application/json; charset=utf-8');
//                         echo json_encode($data);
//                         break;
//                     }

//                     $product = new Product();
//                     $result = $product->addProductQuantity($decodedJsonData);
//                     $isProductsUpdated = $result['isProductUpdated'];

//                     if ($isProductsUpdated) {
//                         $_SESSION['adminProductPageStatus'] = "Product updated successfully";
//                         $data = "success";
//                         header('Content-Type: application/json; charset=utf-8');
//                         echo json_encode($data);
//                     } else {
//                         $data = "failed";
//                         header('Content-Type: application/json; charset=utf-8');
//                         echo json_encode($data);
//                     }
//                 } catch (Exception $e) {
//                     $data = "failed";
//                     header('Content-Type: application/json; charset=utf-8');
//                     echo json_encode($data);

//                     // add log 
//                     $logger = new Logger();
//                     $logger->createLog("exception", $e->getMessage(), "Index", "switchControls", $e->getLine(), $e->getFile());
//                 }


//             } else {
//                 $data = "error";
//                 header('Content-Type: application/json; charset=utf-8');
//                 echo json_encode($data);
//             }

//             break;


//         // get requests to cart page 
//         // ------------------------- reimplement using json
//         case ['/cart', 'GET']:
//             if ($isUserAuthenticated) {
//                 require_once (__DIR__ . '\Views\CartPage.php');
//                 $_SESSION['cartStatus'] = null;
//             } else {
//                 header("Location:/homepage");
//             }

//             break;

//         // to add new product to cart from products 
//         // ------------------------- reimplement using json
//         case ['/cart/add-product', 'POST']:
//             if ($isUserAuthenticated) {
//                 $product = new Product();
//                 $min = $product->getMinProductId();
//                 $minProductId = $min['id'];
//                 $max = $product->getMaxProductId();
//                 $maxProductId = $max['id'];

//                 header("Access-Control-Allow-Origin: http://localhost");
//                 header("Access-Control-Allow-Methods: POST");
//                 header('Expires: 0');
//                 header('Pragma: public');

//                 $jsonData = file_get_contents('php://input');
//                 $data = json_decode($jsonData, true);
//                 $productIdToBeAdded = $data['productId'];

//                 // input validation
//                 if (!(is_int($productIdToBeAdded) and ($minProductId <= $productIdToBeAdded) and ($productIdToBeAdded <= $maxProductId))) {
//                     $data = "error";
//                     header('Content-Type: application/json; charset=utf-8');
//                     echo json_encode($data);
//                     return null;
//                 }

//                 // check if send product is already in cart
//                 $cart = new Cart();
//                 $result = $cart->getCartProducts($_SESSION['currentUser']);

//                 $isGetCartProducts = $result['status'];
//                 $cartProducts = $result['result'];

//                 if (!$isGetCartProducts) {
//                     $data = "error: failed to do operation";
//                     header('Content-Type: application/json; charset=utf-8');
//                     echo json_encode($data);
//                 }

//                 // if()

//                 // if(in_array()) 

//             } else {
//                 $data = "error";
//                 header('Content-Type: application/json; charset=utf-8');
//                 echo json_encode($data);
//             }


//         // to update products in cart
//         // ------------------------- reimplement using json
//         case ['/cart/update-cart', 'POST']:
//             if ($isUserAuthenticated) {
//                 // sanitize product id input
//                 $prodcutsAndDetails = file_get_contents(__DIR__ . '\cache\data\products.txt');
//                 $prodcutsAndDetails = json_decode($prodcutsAndDetails, true);
//                 if ($_POST['product-id'] < $prodcutsAndDetails[1][0][0] or $prodcutsAndDetails[2][0][0] < $_POST['product-id']) {
//                     header("Location:/error");
//                     return null;
//                 }

//                 // if product id is valid then continue
//                 $cart = new Cart();
//                 if (isset($_POST['add'])) {
//                     $cart->addProduct($_POST['product-id']);
//                     header("Location:/cart");
//                 } elseif (isset($_POST['remove'])) {
//                     $cart->removeProduct($_POST['product-id']);
//                     header("Location:/cart");
//                 } else {
//                     header("Location:/error");
//                 }
//             } else {
//                 $data = "error";
//                 header('Content-Type: application/json; charset=utf-8');
//                 echo json_encode($data);
//             }



//             break;

//         // get requests to /homepage
//         case ['/homepage', 'GET']:
//             require_once (__DIR__ . '\Views\HomePage.php');
//             break;

//         // post requsets to /homepage    -  to log out user
//         case ['/homepage', 'POST']:
//             if ($isUserAuthenticated) {
//                 // unset session and current user session 
//                 unset($_SESSION['token']);
//                 unset($_SESSION['currentUser']);
//                 // set cookie to expire
//                 setcookie("token-php-login", "", time() - 3600);

//                 header("Location: /homepage");
//             } else {
//                 header("Location: /homepage");
//             }
//             break;

//         // post requests to /order for making order and updating products quantity
//         // ------------------------- reimplement using json 
//         case ['/order', 'POST']:

//             if ($isUserAuthenticated) {
//                 $order = new Order();
//                 $result = $order->checkProductEligibility();
//                 // check products eligibility 
//                 if ($result[0]) {
//                     // passing products ids and new quantities of products
//                     excec($result[1], $order);
//                 } else {
//                     $_SESSION['cartStatus'] = "Couldn't make order since exceed products quantity.";
//                     header("Location: /cart");
//                 }
//             } else {
//                 header("Location: /error");
//             }

//             break;

//         // get requests to /orders 
//         // ------------------------- reimplement using json
//         case ['/orders', 'GET']:
//             if ($isUserAuthenticated) {
//                 require_once (__DIR__ . '\Views\OrderPage.php');
//             } else {
//                 header("Location: /error");
//             }

//             break;

//         // get orders
//         case ['/orders/load-orders', 'GET']:
//             header('Content-Type: application/json; charset=utf-8');
//             if ($isUserAuthenticated) {
//                 $order = new Order();
//                 $result = $order->getOrders();

//                 $isGetOrders = $result['isGetOrders'];
//                 $currentUserOrders = $result['orders'];



//                 if ($isGetOrders and $currentUserOrders) {
//                     $data = $currentUserOrders;
//                     echo json_encode($data);
//                 } else if ($isGetOrders) {
//                     $output = "User has No orders";
//                     echo json_encode($output);
//                 } else {
//                     $output = "Failed to Load orders";
//                     echo json_encode($output);
//                 }

//             } else {
//                 $output = "error";
//                 echo json_encode($output);
//             }

//             break;

//         case ['/orders/get-order-slip', 'POST']:

//             if ($isUserAuthenticated) {
//                 header("Access-Control-Allow-Origin: http://localhost");
//                 header("Access-Control-Allow-Methods: POST");
//                 header('Expires: 0');
//                 header('Pragma: public');

//                 try {
//                     $jsonData = file_get_contents('php://input');
//                     $data = json_decode($jsonData, true);

//                     // validate input --> need to be strenth
//                     if (!isset($data["order-id"])) {
//                         header('Content-Type: application/json; charset=utf-8');
//                         $output = [false, "error"];
//                         echo json_encode($data);
//                         return null;
//                     }

//                     $orderId = $data["order-id"];

//                     // check order id is valid and belongs to current user
//                     $isOrderIdValid = validateOrderId($orderId);

//                     if (!$isOrderIdValid) {
//                         header('Content-Type: application/json; charset=utf-8');
//                         $output = ['isOrderIdValid' => false, 'message' => "error"];
//                         echo json_encode($output);
//                         return null;
//                     }

//                     $order = new Order();
//                     $result = $order->createOrderPdf($orderId);

//                     $slipPdf = $result['pdfFile'];
//                     $slipName = $result['pdfName'];

//                     header('Content-Description: File Transfer');
//                     header('Content-Type: application/pdf');
//                     header('Content-Disposition: attachment; filename="' . $slipName . '"');
//                     header('Cache-Control: must-revalidate');
//                     header('Content-Length: ' . filesize($slipPdf));
//                     echo $slipPdf;
//                 } catch (Exception $e) {
//                     // add log 
//                     $logger = new Logger();
//                     $logger->createLog("exception", $e->getMessage(), "index.php", "swithControls", $e->getLine(), $e->getFile());

//                     header('Content-Type: application/json; charset=utf-8');
//                     $output = "error";
//                     echo json_encode($output);
//                 }

//             } else {
//                 header('Content-Type: application/json; charset=utf-8');
//                 $output = "error";
//                 echo json_encode($output);
//             }

//             break;

//         // to insert new products to db
//         case ['/products/upload-products', 'POST']:

//             if ($isUserAuthenticated and $userType === "admin") {
//                 try {
//                     // validating request data 
//                     if (!isset($_FILES['products'])) {
//                         $output = "error - empty file uploaded!";
//                         echo json_encode($output);
//                         exit;
//                     }

//                     // validating file type 
//                     if (!($_FILES['products']['name'] === "products_1m.csv")) {
//                         $output = "file format is not valid!";
//                         echo json_encode($output);
//                         exit;
//                     }

//                     $isFileMoved = move_uploaded_file($_FILES['products']['tmp_name'], __DIR__ . "\cache\products.csv");
//                     if (!$isFileMoved) {
//                         throw new FileMovedFailed();
//                     }

//                     // validating if file contains correct header format
//                     $file = fopen(__DIR__ . '\cache\products.csv', 'r');
//                     $header = fgets($file);
//                     fclose($file);
//                     $header = str_replace(' ', '', $header);
//                     $header = trim($header);
//                     if (!($header === "product_name,price,link,quantity")) {
//                         throw new FileContentInvalid('invalid file headers');
//                     }

//                     $product = new Product();
//                     $product->uploadNewProducts();

//                     $output = "products uploaded";
//                     echo json_encode($output);
//                 } catch (FileContentInvalid $e) {
//                     $output = "invalid file headers";
//                     echo json_encode($output);
//                 } catch (Exception $e) {
//                     // add log 
//                     $logger = new Logger();
//                     $logger->createLog("exception", $e->getMessage(), "index.php", "swithControls", $e->getLine(), $e->getFile());

//                     $output = "operation failed, try again";
//                     echo json_encode($output);
//                 }

//             } else {
//                 $output = "error";
//                 echo json_encode($output);
//             }
//             break;

//         // error page
//         default:
//             require_once (__DIR__ . '\Views\ErrorPage.php');
//     }
// }




