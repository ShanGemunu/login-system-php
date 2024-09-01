<?php
namespace app\exceptions;

use Exception;

class ParameterBindFailedException extends Exception{
    public $errorMessage = "Exception - fail to bind parameter.";

    function __construct(string $message, string $class, string $method){
        $this->errorMessage = "{$this->errorMessage} $message $class $method";
    }
}