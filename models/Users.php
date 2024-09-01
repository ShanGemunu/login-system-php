<?php

namespace app\models;

use app\core\Application;
use app\logs\Log;


class Users extends BaseModel
{
    function __construct()
    {
        parent::__construct();
        $this->table("users");
    }

    /** 
     *  insert new user to db from registration
     *  @param string $userName
     *  @param string $email
     *  @param string $hashedPassword
     *  @return bool  
     */
    function insertNewUser(string $userName, string $email, string $hashedPassword): bool
    {
        $data = ['user_name' => [$userName, 's'], 'email' => [$email, 's'], 'hashed_password' => [$hashedPassword, 's']];
        $this->insert($data);
        Log::logInfo("executing insertNewUser with parameters - --user name--, --email--, --hashed password-- at Users");

        return true;
    }

    /** 
     *  check uniqueness of given user email against saved ones in db
     *  @param string
     *  @return bool   
     */
    function checkEmailUnique(string $email): bool
    {
        $where = [['column' => "email", 'operator' => "=", 'value' => $email]];
        foreach ($where as $value) {
            $this->whereAnd($value['column'], $value['operator'], $value['value']);
        }
        $result = $this->select(["email"]);

        if (count($result) > 0){
            Log::logInfo("executing checkEmailUnique with parameters - --email-- and return FALSE at Users");
            return false;
        }
            
        Log::logInfo("executing checkEmailUnique with parameters - --email-- and return TRUE at Users");
        return true;
    }
}