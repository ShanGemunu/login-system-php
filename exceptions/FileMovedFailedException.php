<?php
namespace app\exceptions;

use Exception;

class FileMovedFailedException extends Exception
{
    public $errorMessage = "Exception - file moved failed.";

    function __construct(string $message, string $class, string $method)
    {
        $this->errorMessage = "{$this->errorMessage} : $message $class $method";
    }
}