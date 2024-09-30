<?php

namespace app\exceptions;

use Exception;

class FileCopyFailedException extends Exception
{
    public $errorMessage = "Exception - file copy failed.";

    function __construct(string $message, string $class, string $method)
    {
        $this->errorMessage = "{$this->errorMessage} : $message $class $method";
    }
}