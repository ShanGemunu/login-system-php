<?php

// fetch_assoc , fetch_all
class Queries{
    public $conn;

    function __construct($conn){
        $this->conn = $conn;
    }
    
    function testDb(){
        $sql = "SELECT MAX(id) FROM products"; // SQL with parameters 
        
        return $this->conn->execute_query($sql)->fetch_all(); // type array

        // var_dump(json_encode($result));

        // echo file_put_contents(__DIR__ . '\data\chache.json',json_encode($result));
       
        // var_dump(json_decode(file_get_contents(__DIR__ . '\..\Controller\chache6.json'), true));
       
        // foreach($result as $row){
        //     var_dump($row);  // type array
        //     echo "<br>";
        // }
    }

    function getMinProductId(){
        $query = "SELECT MIN(id) FROM products";
        return $this->conn->execute_query($query)->fetch_all();
    }

    function getMaxProductId(){
        $query = "SELECT MAX(id) FROM products";
        return $this->conn->execute_query($query)->fetch_all();
    }
    
    function checkUserIsExist($email){
        $query = "SELECT * FROM users WHERE email='".$email."'";
        return $this->conn -> query($query);
    }
    
    function insertNewUser($email, $hashedPassword){
        $query = "INSERT INTO users (user_name, email, hashed_password)
        VALUES ('".$_POST['user_name']."','".$_POST['email']."','".$hashedPassword."')";
    
        return $this->conn -> query($query);
    }

    function fetchProducts(){
        $query = "SELECT * FROM products";
        return $this->conn->execute_query($query)->fetch_all();
    }
}

