<?php
namespace app\exceptions;

use Exception;

class ForbiddenException extends Exception
{
    public $errorMessage = "Access Forbidden!";
}