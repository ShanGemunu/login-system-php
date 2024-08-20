<?php
use app\core\Application;
use app\controllers\SiteController;
use app\controllers\UserController;

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$config = [
    'server' => $_ENV['SERVER'],
    'userName' => $_ENV['USER_NAME'],
    'password' => $_ENV['PASSWORD'],
    'dbName' => $_ENV['DB_NAME'],
    'port' => $_ENV['PORT'],
];

$app = new Application(__DIR__ . "/../", $config);
$app->router->get('/', [SiteController::class, 'home']);
$app->router->get('/login', [UserController::class, 'login']);
$app->router->get('/register', [UserController::class, 'register']);

$app->run();


