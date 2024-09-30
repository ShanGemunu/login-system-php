<?php

namespace app\core;

use app\database\Database;
use Exception;
use app\exceptions\ForbiddenException;
use app\core\Log;

class Application
{

    public Router $router;
    public Request $request;
    public Response $response;
    public static string $ROOT_DIR;
    public static Application $app;
    public static ?string $userId = null;
    public static ?string $userType = null;
    private string $secretKey;
    public Database $db;
    public static string $dbName;
    public Controller $controller;
    public string $layout = 'main';
    public View $view;
    public Session $session;
    public Token $token;

    public function __construct(string $rootPath, array $config)
    {
        new DateTime($config['timeZone']);
        Log::config();
        self::$ROOT_DIR = $rootPath;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router();
        self::$app = $this;
        self::$dbName = $config['database']['dbName'];
        $this->db = Database::getDatabseInstance($config['database']);
        $this->secretKey = $config['secretKey'];
        $this->session = new Session();
        $this->token = new Token();
        $this->view = new View();

        $authDetails = Authentication::authenticateUser();
        if ($authDetails['isAuthenticated']) {
            $decodedToken = $this->token->decodeToken($authDetails['token'], $config['secretKey']);
            self::$userId = $decodedToken->userId;
            self::$userType = $decodedToken->userType;
        }
    }

    /** 
     *    start point of application
     *    @param 
     *    @return void   
     */
    public function run(): void
    {
        try {
            echo $this->router->resolve();
            Log::logInfo("Application", "run", "echo view provided", "success", "no data");
        } catch (ForbiddenException $exception) {
            $this->response->setStatusCode(403);
            echo $this->router->renderView('access-forbidden');
            Log::logError("Application", "run", "ForbiddenException raised", "failed", $exception->getMessage());
        } catch (Exception $exception) {
            $this->response->setStatusCode(500);
            echo 'server-error';
            Log::logError("Application", "run", "Exception raised when trying to execute method", "failed", $exception->getMessage());
        }
    }

    /** 
     *    set token in session and cookies of browser 
     *    when user logged in
     *    @param int $userId 
     *    @param string $userType
     *    @return bool
     */
    public function login(int $userId, string $userType): void
    {
        $token = self::$app->token->createToken($userId, $userType, self::$app->secretKey);
        self::$app->session->set("token", $token);
        self::$app->response->setCookie("token", $token);
        Log::logInfo("Application", "login", "set token in session and cookies of browser successfully ", "success", "user id - ;user type - ;token - ");
    }

    /** 
     *    unset token in session and cookies of browser 
     *    when user logged out
     *    @return void
     */
    public function logout()
    {
        self::$app->session->remove("token");
        self::$app->response->unsetCookie("token");
        Log::logInfo("Application", "logout", "unset token in session and cookie of browser successfully ", "success", "token - ");
    }

    public static function isGuest()
    {
        return !self::$userId;
    }
}