<?php
namespace app\exceptions;

use Exception;

class FileContentInvalidException extends Exception{
    public $errorMessage = "Exception - file content invalid.";

    function __construct(string $message, string $class, string $method){
        $this->errorMessage = "{$this->errorMessage} $message $class $method";
    }
}