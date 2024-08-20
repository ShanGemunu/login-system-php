<?php
namespace app\models;

use app\core\Application;

class BaseModel
{
    private $conn = Application::$app->db;

    /*
        prepare query
        returns a statement object or false if an error occurred.
        @params string
        @return mysqli_stmt|bool

    */
    function prepareQuery(string $query): \mysqli_stmt|bool
    {
        return $this->conn->prepare($query);
    }

    /*
        bind parameters to query
        Returns true on success or false on failure.
        @params mysqli_stmt, array['column1'=>[value1, type1],...] 
        @return bool
        
    */
    function bindParameters(\mysqli_stmt $statement, array $parameters): bool
    {
        // assoiative array
        $types = array_map(function ($param) {
            return $param[1];
        }, $parameters);

        $concatTypes = implode('', $types);

        // assoiative array
        $values = array_map(function ($param) {
            return $param[0];
        }, $parameters);

        // array 
        $values = array_values($values);

        return $statement->bind_param($concatTypes, ...$values);
    }
}