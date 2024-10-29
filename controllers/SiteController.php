<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Application;
use app\middlewares\AuthMiddleware;
use app\middlewares\UsertypeMiddleware;
use app\core\Log;
use Exception;

class SiteController extends Controller
{
    /** 
     *    retrun home view for authenticated users
     *    @param
     *    @return string   
     */
    function home(): string
    {
        try {
            $this->setLayout('home');
            Log::logInfo("SiteController", "home", "render home view to front end", "success", "no data");
            Application::$app->response->setStatusCode(200);

            return $this->render('home');
        } catch (Exception $exception) {
            Log::logError("SiteController","home","Exception raise when try to render home view to fornt end","failed",$exception->getMessage());
            Application::$app->response->setStatusCode(500);

            return "system error";
        }
    }
}