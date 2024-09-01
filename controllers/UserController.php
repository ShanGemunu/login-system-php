<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Response;
use app\models\Users;
use app\request\UserRequest;
use app\logs\Log;
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

    /** 
    *    render login pgae in get, login user in post
    *    @param 
    *    @return string   
    */
    function login(): string
    {
        try{
            $userRequest = new UserRequest();
            $response = new Response();
            $method = $userRequest->getMethod();
            if ($method === 'post') {
                if (!$userRequest->validateLoginInputs()) {
                    Log::logInfo("login inputs are missed when trying to login at login method of UserController");
    
                    return "invalid request!";
                }
    
                $inputs = $userRequest->getLoginInputs();
                if ($inputs['email'] !== "" and $inputs['password'] !== "") {
                    $response->redirect('/');
                    Log::logInfo("user successfully logged at login method of UserController");
    
                    return "user redirected";
                }
                if ($inputs['email'] === "") {
                    $this->addErrorByRule('email', self::RULE_REQUIRED);
                }
                if ($inputs['password'] === "") {
                    $this->addErrorByRule('password', self::RULE_REQUIRED);
                }
                $this->setLayout('auth');
                Log::logInfo("login inputs are invalid at login method of UserController");
    
                return $this->render('login', $this->errors);
            }
            $this->setLayout('auth');
            Log::logInfo("call render method of Controller as a get request at login method of UserController");
    
            return $this->render('login');
        }catch(Exception $e){
            Log::logError("Exception raised when trying to login at login method of UserController as ".$e->getMessage());

            return "system error";
        }
      
    }

    /** 
    *    render register page in get, register user in post
    *    @param  
    *    @return string   
    */
    function register(): string
    {
        try{
            $userRequest = new UserRequest();
            $response = new Response();
            $method = $userRequest->getMethod();
            $userModel = new Users();
            if ($method === 'post') {
                if (!$userRequest->validateRegisterInputs()) {
                    Log::logInfo("register inputs are missed when trying to register at register
                     method of UserController");
    
                    return "invalid request!";
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
                if (!$userModel->checkEmailUnique($inputs['email'])) {
                    $this->addErrorByRule('email',self::RULE_UNIQUE);
                }
                if ($inputs['password'] === "") {
                    $this->addErrorByRule('password', self::RULE_REQUIRED);
                }
                if ($inputs['confirm-password'] !== $inputs['password']) {
                    $this->addErrorByRule('confirm_password', self::RULE_MATCH);
                }
                if (empty($this->errors)) {
                    $userModel->insertNewUser($inputs['username'], $inputs['email'], password_hash($inputs['password'], PASSWORD_DEFAULT));
                    $response->redirect('/');
                    Log::logInfo("user sucessfully register at register method of UserController");
    
                    return "user successfully registered.";
                }
                $this->setLayout('auth');
                Log::logInfo("register inpiuts are invalid at register method of UserController");
    
                return $this->render('register', $this->errors);
            }
            $this->setLayout('auth');
            Log::logInfo("call render method of Controller as a get request at register method of UserController");
    
            return $this->render('register');
        }catch(Exception $e){
            Log::logError("Exception raised when trying to register user at resgiter method of UserController as ".$e->getMessage());

            return "system error";
        }
    
    }

}