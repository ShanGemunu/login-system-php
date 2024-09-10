<?php

namespace app\core;

use app\core\Log;

class Response
{
    /** 
     *    set response code for response which is going to be send 
     *    @param int $code
     *    @return void   
     */
    public function setStatusCode(int $code): void
    {
        Log::logInfo("Response", "setStatusCode", "set status code for outgoing request", "success", $code);
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
        Log::logInfo("Response", "redirect", "redirect user to specific route", "success", $route);
        header("Location: $route");
    }

    /** 
     *    set a cookie 
     *    @param string $name
     *    @param string $value 
     *    @return void   
     */
    public function setCookie(string $name, int|string $value): void
    {
        Log::logInfo("Response", "setCookie", "set a cookie", "success", "cookie name - $name; cookie value - ");
        setcookie($name, $value);
    }

     /** 
     *    unset a cookie 
     *    @param string $name
     *    @return void   
     */
    public function unsetCookie(string $name): void
    {
        Log::logInfo("Response", "unsetCookie", "unset a cookie", "success", "cookie name - $name");
        setcookie($name, "", time() - 3600 * 24 * 7);
    }

}