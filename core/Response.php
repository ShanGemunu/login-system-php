<?php

namespace app\core;

class Response{
    /*
        set response code for response which is going to be send 
        @params int
        @return void
        
    */
    public function setStatusCode(int $code) : void
    {
        http_response_code($code);
    }
}