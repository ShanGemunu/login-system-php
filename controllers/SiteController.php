<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;

class SiteController extends Controller
{
    /*
        retrun specific view
        @params string, array
        @return string
        
    */
    function home() : string
    {
        $params = [
            'name' => "gemunu"
        ];

        return $this->render('home', $params);
    }
}