<?php
require __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;

class Authentication
{
    private $secretKey = 'encoding by anonymos';

    // string, string -> string
    function createToken($userId, $userType)
    {
        $issued_at = time();
        // $expiration_time = $issued_at + (60*60); // valid for 1 hour

        // if wants expiration time -> 'exp' => $expiration_time,
        $payload = array(
            'iat' => $issued_at,
            'userId' => $userId,
            'userType' => $userType
        );

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    function setAuthVariables($userDetails)
    {
        // set session using token 
        $token = $this->createToken($userDetails['id'], $userDetails['type']);
        $_SESSION['token'] = $token;

        // set cookie in browser
        setcookie("token-php-login", $token);
    }

    // string,string -> [bool,string,string]
    function authenticateUser($email_, $password_)
    {
        // sanitize inputs
        $email = htmlspecialchars($email_, ENT_QUOTES);
        // $email = urlencode($email);
        $password = htmlspecialchars($password_, ENT_QUOTES);
        // $password = urlencode($password);

        if (30 < strlen($email) or 20 < strlen($password)) {
            $output['outputStatus'] = "invalid inputs";
            return $output;
        }

        $autheticateStatus = ['isValidCredentials' => false, 'outputStatus' => null, 'userDetails' => null];

        $users = new Users();

        // check if provided email was already registered
        $userData = $users->checkUserEmailIsExist($email);

        if (count($userData) === 0) {
            $autheticateStatus['outputStatus'] = "Login failed, there is no user registered to this email.";
            return $autheticateStatus;
        }

        $userData = $userData[0];

        // check password
        $hashedPassword = $userData["hashed_password"];
        if (password_verify($password, $hashedPassword)) {
            $autheticateStatus['isValidCredentials'] = true;
            $autheticateStatus['status'] = "success";
            $autheticateStatus['userDetails'] = ["id" => $userData["id"], "type" => $userData["user_type"]];
            return $autheticateStatus;
        } else {
            $autheticateStatus['status'] = "Login failed, password is wrong.";
            $autheticateStatus['userDetails'] = null;
            return $autheticateStatus;
        }
    }
}