<?php

namespace app\models;

use app\core\Application;
use app\core\Log;


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
        Log::logInfo("Users", "insertNewUser", "insert new user to db from registration", "success", "user name - $userName; email - ; hashed password -");

        return true;
    }

    /** 
     *  return email is found in db, return empty array otherwise
     *  @param string
     *  @return array   
     */
    function getEmail(string $email): array
    {
        $where = [['column' => "email", 'operator' => "=", 'value' => $email]];
        foreach ($where as $value) {
            $this->whereAnd($value['column'], $value['operator'], $value['value']);
        }
        Log::logInfo("Users", "getEmail", "return email if found in db, return empty array if not found", "success", "email - ");

        return $this->select(['email'=>["email",null]]);
    }

    /** 
     *  fetch user id and type from db by taking email and hashed password
     *  if user exists rerurn user details otherwise return false
     *  @param string $email
     *  @param string $hashedPassword
     *  @return array|bool
     */
    function getUser(string $email) : array|bool
    {
        
        $this->whereAnd("email", "=",  $email);
        $user = $this->select();
        if(count($user)>0){

            return $user[0];
        }

        return false;
    }
}