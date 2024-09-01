<?php

namespace app\core;

use app\logs\Log;

class Response
{
    /** 
    *    set response code for response which is going to be send 
    *    @param int $code
    *    @return void   
    */
    public function setStatusCode(int $code): void
    {
        Log::logInfo("call setStatusCode method of Response with parameters - $code at setStatusCode method of Response.");
        http_response_code($code);
    }

    /** 
    *    set content type for response which is going to be send 
    *    @param string $type
    *    @return void
    */
    public function setContentType(string $type): void
    {
        
    }

    /** 
    *    redirect to specific route 
    *    @param string $route
    *    @return void   
    */
    public function redirect(string $route): void
    {
        Log::logInfo("call redirect method of Response with parameters - $route at redirect method of Response.");
        header("Location: $route");
    }

}