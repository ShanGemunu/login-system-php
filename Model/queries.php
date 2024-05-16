<?php

function validateUser($conn, $email){
    $query = "SELECT user_name, hashed_password FROM users WHERE email='".$email."'";
    return $conn -> query($query);
}

function checkUserIsExist($conn, $email){
    $query = "SELECT * FROM users WHERE email='".$email."'";
    return $conn -> query($query);
}

function insertNewUser($conn, $email, $hashedPassword){
    $query = "INSERT INTO users (user_name, email, hashed_password)
    VALUES ('".$_POST['user_name']."','".$_POST['email']."','".$hashedPassword."')";

    return $conn -> query($query);
}