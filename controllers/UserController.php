<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;

class UserController extends Controller
{
    /*
        render login pgae
        @params 
        @return string
        
    */
    function login() : string
    {
        $method = Application::$app->request->getMethod();
        
        if($method === 'post'){
            return $this->render('login');
        }

        $this->setLayout('auth');

        return $this->render('login');
    }

    /*
        render register page
        @params 
        @return string
        
    */
    function register() : string
    {
        $inputs = Application::$app->request->getBody();
        
        $this->setLayout('auth');
        return $this->render('register');
    }

    /*
        register new user in system
        @params 
        @return string
        
    */
    function registerUser() : string
    {
        $inputs = Application::$app->request->getBody();
        
        $this->setLayout('auth');
        return $this->render('register');
    }
}