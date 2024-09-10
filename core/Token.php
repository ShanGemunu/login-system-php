<?php

namespace app\core;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

class Token
{
    /** 
     *    create token 
     *    @param int $userId
     *    @param string $userType
     *    @return string
     */
    public function createToken(int $userId, string $userType, string $secretKey): string
    {
        $issued_at = time();
        $payload = [
            'issuedAt' => $issued_at,
            'userId' => $userId,
            'userType' => $userType
        ];
        Log::logInfo("Token","createToken","create token successfully","success","user id - ; user type - ");

        return JWT::encode($payload, $secretKey, 'HS256');
    }

    /** 
     *    decode token
     *    @param string $token
     *    @param string $secretKey
     *    @return stdClass
     */
    function decodeToken($token, $secretKey) : stdClass
    {
        Log::logInfo("Token","decodeToken","decode token successfully","success","token - ; secret key - ");

        return JWT::decode($token, new Key($secretKey, 'HS256'));
    }
}