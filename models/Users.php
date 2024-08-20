<?php

namespace app\models;

class Users extends BaseModel
{
    /*
        insert new user to db from registration
        @params string, string, string
        @return array
        
    */
    function insertNewUser(string $userName,string $email,string $hashedPassword): array
    {
        $query = "INSERT INTO users (user_name, email, hashed_password)
            VALUES (?,?,?)";

        $preparedQuery = $this->prepareQuery($query);

        if ($preparedQuery === false) {
            throw new \Exception();
        }

        $parameters = ['user_name' => [$userName, 's'], 'email' => [$email, 's'], 'hashed_password' => [$hashedPassword, 's']];
        $isParametersBind = $this->bindParameters($preparedQuery, $parameters);

        if (!$isParametersBind) {
            throw new \Exception();
        }

        if ($preparedQuery->execute() === false) {
            throw new \Exception();
        }
        $result = $preparedQuery->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}