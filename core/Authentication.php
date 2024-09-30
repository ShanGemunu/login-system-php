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
        $requestBody = Application::$app->request->getBody();
        if (!isset($requestBody['cookie']['token'])) {

            return $authDetails;
        }
        $browserToken = $requestBody['cookie']['token'];
        $sessionToken = Application::$app->session->get("token");

        if ($browserToken === $sessionToken) {
            $authDetails['isAuthenticated'] = true;
            $authDetails['token'] = $sessionToken;

            return $authDetails;
        }

        return $authDetails;
    }
}