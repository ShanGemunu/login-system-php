<?php

namespace app\request;

use app\core\Request;
use app\logs\Log;

class UserRequest extends Request
{
    /** 
    *    validate login inputs 
    *    @param
    *    @return bool   
    */
    function validateLoginInputs() : bool
    {
        $request = $this->getBody();
        if (isset($request['request']['email']) && isset($request['request']['password'])) {
            Log::logInfo("all inputs for login are provided, at validateLoginInputs method of UserRequest.");

            return true;
        } 
        Log::logInfo("all inputs for login are not provided, at validateLoginInputs method of UserRequest.");

        return false;
    }

    /** 
    *    get login inputs 
    *    @param
    *    @return array   
    */
    function getLoginInputs() : array
    {
        $inputs = [];
        $request = $this->getBody();
        $inputs['email'] = $request['request']['email'];
        $inputs['password'] = $request['request']['password'];
        Log::logInfo("get login inputs, at getLoginInputs method of UserRequest.");

        return $inputs;
    }

    /** 
    *    validate register inputs 
    *    @param
    *    @return bool   
    */
    function validateRegisterInputs() : bool
    {
        $request = $this->getBody();
        if (isset($request['request']['username']) && isset($request['request']['email']) && isset($request['request']['password']) && isset($request['request']['confirm-password'])) {
            Log::logInfo("all inputs for register are provided, at validateRegisterInputs method of UserRequest.");

            return true;
        } 
        Log::logInfo("all inputs for register are not provided, at validateRegisterInputs method of UserRequest.");

        return false;
    }

    /** 
    *    get register inputs 
    *    @param 
    *    @return array
    */
    function getRegisterInputs() : array
    {
        $inputs = [];
        $request = $this->getBody();
        $inputs['username'] = $request['request']['username'];
        $inputs['email'] = $request['request']['email'];
        $inputs['password'] = $request['request']['password'];
        $inputs['confirm-password'] = $request['request']['confirm-password'];
        Log::logInfo("get register inputs, at getRegisterInputs method of UserRequest.");

        return $inputs;
    }

}