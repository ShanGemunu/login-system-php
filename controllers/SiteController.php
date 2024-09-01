<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\logs\Log;
use Exception;

class SiteController extends Controller
{
    /** 
    *    retrun home view
    *    @param
    *    @return string   
    */
    function home(): string
    {
        try {
            Log::logInfo("call render method of Controller at home method of SiteController.");

            return $this->render('home');
        } catch (Exception $e) {
            Log::logError("Exception raise when try to call render method of Controller at home method of SiteController as ".$e->getMessage());

            return "system error";
        }
    }
} 