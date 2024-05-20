<?php

class Queries{
    public $conn;

    function __construct($conn){
        $this->conn = $conn;
    }
    
    function validateUser($email){
        $query = "SELECT user_name, hashed_password FROM users WHERE email='".$email."'";
        return $this->conn -> query($query);
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
}

