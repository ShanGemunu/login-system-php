<?php
require __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Authorization
{
    private $secretKey = 'encoding by anonymos';

    // -> [bool,int,string]
    function doAuthorization()
    {
        $authStatus = ['isValidUser' => false, 'userId' => null, 'userType' => null];

        if (isset($_COOKIE["token-cookie"]) && isset($_SESSION["token-session"])) {
            $isValidUser = $this->validateToken($_COOKIE["token-php-login"], $_SESSION["token"]);

            if ($isValidUser) {
                $decodedToken = $this->decodeToken($_SESSION["token-session"]);
                $authStatus['userId'] = $decodedToken->userId;
                $authStatus['userType'] = $decodedToken->userType;
                
                return $authStatus;
            } else {

                return $authStatus;
            }
        } else {

            return $authStatus;
        }
    }

    // -> object()
    function decodeToken($token)
    {
            // return object having payload
            return JWT::decode($token, new Key($this->secretKey, 'HS256'));

    }

    // string, string, string -> bool
    function validateToken($tokenCookie, $tokenSession)
    {
        if ($tokenCookie === $tokenSession) {

            return true;
        } else {

            return false;
        }
    }
}