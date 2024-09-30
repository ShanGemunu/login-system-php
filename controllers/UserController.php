<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Response;
use app\models\Users;
use app\request\UserRequest;
use app\middlewares\UsertypeMiddleware;
use app\core\Log;
use Exception;

class UserController extends Controller
{
    private const LOGIN_RULES = [
        'email' => [parent::RULE_REQUIRED],
        'password' => [parent::RULE_REQUIRED],
    ];

    private const REGISTER_RULES = [
        'firstname' => [parent::RULE_REQUIRED],
        'lastname' => [parent::RULE_REQUIRED],
        'email' => [
            parent::RULE_REQUIRED,
            parent::RULE_EMAIL,
            [
                parent::RULE_UNIQUE,
                'class' => parent::class
            ]
        ],
        'password' => [parent::RULE_REQUIRED, [parent::RULE_MIN, 'min' => 8], [parent::RULE_MAX, 'max' => 30]],
        'passwordConfirm' => [[parent::RULE_MATCH, 'match' => 'password']],
    ];

    function __construct()
    {
        $this->registerMiddleware(new UsertypeMiddleware(
            [
                'user' => ["indexLogin","indexRegister","login","register"],
                'admin' => ["indexLogin","indexRegister","login","register"],
                'seller' => ["indexLogin","indexRegister","login","register"]
            ]
        ));
    }

    /** 
     *    render login page to frontend
     *    @param  
    *    @return string   
     */
    function indexLogin(): string
    {
        try {
            $this->setLayout('auth');
            Log::logInfo("UserController", "indexLogin", "render login page to front end", "success", "layout - auth, page -login");

            return $this->render('login');
        } catch (Exception $exception) {
            Log::logError("UserController", "indexLogin", "Exception raised when trying to render login page", "failed", $exception->getMessage());
            Application::$app->response->setStatusCode(500);

            return "system error";
        }
    }

    /** 
     *    render register page to frontend
     *    @param  
     *    @return string   
     */
    function indexRegister(): string
    {
        try {
            $this->setLayout('auth');
            Log::logInfo("UserController", "indexRegister", "render register page to front end", "success", "layout - auth, page - register");
            Application::$app->response->setStatusCode(200);

            return $this->render('register');
        } catch (Exception $exception) {
            Log::logError("UserController", "indexRegister", "Exception raised when trying to render register page", "failed", $exception->getMessage());
            Application::$app->response->setStatusCode(500);

            return "system error";
        }
    }

    /** 
     *    log in user 
     *    @param 
     *    @return string   
     */
    function login(): string
    {
        try {
            $userRequest = new UserRequest();

            if (!$userRequest->validateLoginInputs()) {
                Log::logInfo("UserController", "login", "login inputs are missed when trying to login", "failed", "no data");
                Application::$app->response->setStatusCode(422);

                return json_encode(['success' => false, 'result' => "Invalid request!"]);
            }

            $inputs = $userRequest->getLoginInputs();

            // if email and password is not empty
            if ($inputs['email'] !== "" and $inputs['password'] !== "") {
                $userModel = new Users();
                $user = $userModel->getUser($inputs['email']);

                // if user is not registered 
                if (!$user) {
                    Application::$app->response->setStatusCode(200);
                    $this->setLayout('auth');

                    return $this->render('login', ['emailNotExist' => "Email not registered!"]);
                }
                // if password is wrong
                if (!password_verify($inputs['password'], $user['hashed_password'])) {
                    Application::$app->response->setStatusCode(200);
                    $this->setLayout('auth');

                    return $this->render('login', ['passwordWrong' => "Password is wrong!"]);
                }

                Application::$app->login($user['id'], $user['user_type']);
                Application::$app->response->redirect('/');
                Log::logInfo("UserController", "login", "user successfully logged and user redirected to homepage", "success", "no data");

                return json_encode(['success' => true, 'result' => "user redirected"]);
            }
            if ($inputs['email'] === "") {
                $this->addErrorByRule('email', self::RULE_REQUIRED);
            }
            if ($inputs['password'] === "") {
                $this->addErrorByRule('password', self::RULE_REQUIRED);
            }
            $this->setLayout('auth');
            Log::logInfo("UserController", "login", "login inputs are invalid and render login page again", "failed", "no data");
            Application::$app->response->setStatusCode(200);

            return $this->render('login', $this->errors);

        } catch (Exception $exception) {
            Log::logError("UserController", "login", "Exception raised when trying to login", "failed", $exception->getMessage());
            Application::$app->response->setStatusCode(500);

            return "system error";
        }

    }

    /** 
     *    register user 
     *    @param  
     *    @return string   
     */
    function register(): string
    {
        try {
            $userRequest = new UserRequest();
            $userModel = new Users();

            if (!$userRequest->validateRegisterInputs()) {
                Log::logInfo("UserController", "register", "register inputs are missed when trying to register", "failed", "no data");
                Application::$app->response->setStatusCode(422);

                return json_encode(['success' => false, 'result' => "Invalid request!"]);
            }
            $inputs = $userRequest->getRegisterInputs();
            if ($inputs['username'] === "") {
                $this->addErrorByRule('username', self::RULE_REQUIRED);
            }
            if ($inputs['email'] === "") {
                $this->addErrorByRule('email', self::RULE_REQUIRED);
            }
            if (!filter_var($inputs['email'], FILTER_VALIDATE_EMAIL)) {
                $this->addErrorByRule('email', self::RULE_EMAIL);
            }
            if (count($userModel->getEmail($inputs['email'])) > 0) {
                $this->addErrorByRule('email', self::RULE_UNIQUE);
            }
            if ($inputs['password'] === "") {
                $this->addErrorByRule('password', self::RULE_REQUIRED);
            }
            if ($inputs['confirm-password'] !== $inputs['password']) {
                $this->addErrorByRule('confirm_password', self::RULE_MATCH);
            }
            if (empty($this->errors)) {

                // for now 'user' is get for user type 
                $userModel->insertNewUser($inputs['username'], $inputs['email'], password_hash($inputs['password'], PASSWORD_DEFAULT));
                $user = $userModel->getUser($inputs['email']);
                Application::$app->login($user['id'], $user['user_type']);
                Application::$app->response->redirect('/');
                Log::logInfo("UserController", "register", "user sucessfully register and user redirected to homepage", "success", "no data");
                Application::$app->response->setStatusCode(200);

                return json_encode(['success' => true, 'result' => "user successfully registered."]);
            }
            $this->setLayout('auth');
            Log::logInfo("UserController", "register", "register inputs are invalid and render register page again", "failed", "no data");
            Application::$app->response->setStatusCode(200);

            return $this->render('register', $this->errors);

        } catch (Exception $exception) {
            Log::logError("UserController", "register", "Exception raised when trying to register user", "failed", $exception->getMessage());
            Application::$app->response->setStatusCode(500);

            return "system error";
        }

    }

}