<?php

namespace app\request;

use app\core\Request;
use app\core\Log;

class UserRequest extends Request
{
    /** 
     *    validate login inputs 
     *    @param
     *    @return bool   
     */
    function validateLoginInputs(): bool
    {
        $request = $this->getBody();
        if (isset($request['request']['email']) && isset($request['request']['password'])) {
            Log::logInfo("UserRequest","validateLoginInputs","validate login inputs, email or password are not provided","failed","no data");

            return true;
        }
        Log::logInfo("UserRequest","validateLoginInputs","validate login inputs, email and password are provided","pass","email - ; password - ");

        return false;
    }

    /** 
     *    get login inputs 
     *    @param
     *    @return array   
     */
    function getLoginInputs(): array
    {
        $inputs = [];
        $request = $this->getBody();
        $inputs['email'] = $request['request']['email'];
        $inputs['password'] = $request['request']['password'];
        Log::logInfo("UserRequest","getLoginInputs","return login inputs","success","email - ; password - ");

        return $inputs;
    }

    /** 
     *    validate register inputs 
     *    @param
     *    @return bool   
     */
    function validateRegisterInputs(): bool
    {
        $request = $this->getBody();
        if (isset($request['request']['username']) && isset($request['request']['email']) && isset($request['request']['password']) && isset($request['request']['confirm-password'])) {
            Log::logInfo("UserRequest","validateRegisterInputs","validate register inputs, one or more required inputs are not provided","failed","username- ; email - ; password - ; confirm-password - ");

            return true;
        }
        Log::logInfo("UserRequest","validateRegisterInputs","validate register inputs, all inputs are provided","pass","username- ; email - ; password - ; confirm-password - ");

        return false;
    }

    /** 
     *    get register inputs 
     *    @param 
     *    @return array
     */
    function getRegisterInputs(): array
    {
        $inputs = [];
        $request = $this->getBody();
        $inputs['username'] = $request['request']['username'];
        $inputs['email'] = $request['request']['email'];
        $inputs['password'] = $request['request']['password'];
        $inputs['confirm-password'] = $request['request']['confirm-password'];
        Log::logInfo("UserRequest","getRegisterInputs","return register inputs","success","username- ; email - ; password - ; confirm-password - ");

        return $inputs;
    }

}