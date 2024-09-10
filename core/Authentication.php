<?php

namespace app\core;

class Authentication
{

    /** 
     *    auithenticate user and return whether user is authenticated.
     *    if user authenticated, rerurn token
     *    @return array
     */
    static function authenticateUser() : array
    {
        $authDetails = ['isAuthenticated'=>false,'token'=>null];
        $request = new Request();
        $requestBody = $request->getBody();
        if (!isset($requestBody['request']['token'])) {

            return $authDetails;
        }
        $browserToken = $requestBody['request']['token'];
        $session = new Session();
        $sessionToken = $session->get("token");
        if ($browserToken === $sessionToken) {
            $authDetails['isAuthenticated'] = true;
            $authDetails['token'] = $sessionToken;

            return $authDetails;
        }

        return $authDetails;
    }
}