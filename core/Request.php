<?php

namespace app\core;

class Request
{
    /*
        get uri of current request
        @params 
        @return string
        
    */
    public function getPath(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position);
    }

    /*
        get method of current request
        @params 
        @return string
        
    */
    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /*
        get body of current request
        @params 
        @return array
        
    */
    public function getBody(): array
    {
        $body = [];
        if ($this->getMethod() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = htmlspecialchars($value, ENT_QUOTES);
            }
        }

        if ($this->getMethod() === 'post') {
            // get the content type of sended data
            $contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : 'invalid';

            switch ($contentType) {
                case 'application/x-www-form-urlencoded':
                    foreach ($_POST as $key => $value) {
                        $body[$key] = htmlspecialchars($value, ENT_QUOTES);
                    }
                    break;
                case 'application/json':
                    break;
                case 'multipart/form-data':
                    break;
                default:
                    throw new \Exception();
            }
        }

        return $body;
    }
}