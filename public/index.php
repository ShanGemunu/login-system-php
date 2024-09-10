<?php
use app\core\Application;
use app\controllers\SiteController;
use app\controllers\UserController;
use app\controllers\CartController;
use app\controllers\ProductController;
use app\controllers\OrderController;

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$config = [
    'database' => [
        'server' => $_ENV['SERVER'],
        'userName' => $_ENV['USER_NAME'],
        'password' => $_ENV['PASSWORD'],
        'dbName' => $_ENV['DB_NAME'],
        'port' => $_ENV['PORT'],
    ],
    'secretKey' => $_ENV['SECRET_KEY'],
    'timeZone' => $_ENV['TIME_ZONE']
];

$app = new Application(__DIR__ . "/../", $config);
$app->router->get('/', [SiteController::class, 'home']);
$app->router->get('/login', [UserController::class, 'indexLogin']);
$app->router->get('/register', [UserController::class, 'indexRegister']);
$app->router->post('/login', [UserController::class, 'login']);
$app->router->post('/register', [UserController::class, 'register']);
$app->router->get('/products', [ProductController::class, 'index']);
$app->router->get('/cart', [CartController::class, 'loadCartPage']);
$app->router->post('/product/get-products', [ProductController::class, 'getProductsByLimit']);
$app->router->post('/cart/get-products', [CartController::class, 'loadCartProducts']);
$app->router->post('/product/upload-products', [ProductController::class, 'uploadProductsAsBulk']);
$app->router->post('/cart/add-product', [CartController::class, 'addProduct']);
$app->router->post('/cart/remove-product', [CartController::class, 'removeProduct']);
$app->router->post('/cart/update-product', [CartController::class, 'updateProductQuantity']);
$app->router->post('/order/create-order', [OrderController::class, 'createOrder']);

$app->run();















