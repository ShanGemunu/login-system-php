<?php
require __DIR__ . '\..\vendor\autoload.php';

use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Authentication{
    private $secretKey = 'encoding by anonymos';
    
    // string, string -> string
    function createToken($userEmail,$userType){
        $issued_at = time();
        // $expiration_time = $issued_at + (60*60); // valid for 1 hour
        
        // if wants expiration time -> 'exp' => $expiration_time,
        $payload = array(
            'iat' => $issued_at,
            'userId' => $userEmail,
            'userType' => $userType
        );
    
        return JWT::encode($payload, $this->secretKey, 'HS256');
   }
 

    function decodeToken($token){
        try {
            // return object having payload
            return JWT::decode($token, new Key($this->secretKey, 'HS256'));  
        } catch (ExpiredException $e) {
            throw new Exception('Token expired');
        } catch (SignatureInvalidException $e) {
            throw new Exception('Invalid token signature');
        } catch (BeforeValidException $e) {
            throw new Exception('Token not valid yet');
        } catch (Exception $e) {
            throw new Exception('Invalid token');
        }
        
    }
    

    // $_COOKIE["token-from-chrome"] === $_SESSION["token"]
    // string, string, string -> bool
    function validateToken($tokenCookie, $tokenSession){
        if($tokenCookie === $tokenSession){
            $tokenPayload = $this->decodeToken($tokenSession);
            return [true, $tokenPayload];
        }else{
            return [false,null];
        }
    }
}