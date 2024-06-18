<?php
require_once('Model/DbConnection.php');
require_once('Model/Queries.php') ;

$conn = new DbConnection();

$queries = new Queries($conn->conn);

$queries->validateUser("mark@abc.com");




// var_dump($row["user_name"]);

