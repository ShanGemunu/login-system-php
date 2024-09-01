<?php

namespace app\exceptions;

use Exception;

class LoadInFileFailedException extends Exception{
    public $errorMessage = "Exception - fail to load infile.";

    function __construct(string $message, string $class, string $method){
        $this->errorMessage = "{$this->errorMessage} $message $class $method";
    }
}