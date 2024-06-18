<?php
require_once('Model/DbConnection.php');
require_once('Model/Queries.php') ;

class DataLoad{
    private $conn; 

    function __construct(){
        $this->conn = new DbConnection(); // create object to get db connection
    }

    function getConn(){
        return $this->conn;
    }

    function fetchProductsAndIds(){
        $queries = new Queries($this->conn->conn);
        $products = $queries->fetchProducts();
        $minProductId = $queries->getMinProductId();
        $maxProductId = $queries->getMaxProductId();
        $productsAndDetails = [$products,$minProductId,$maxProductId]; 
        file_put_contents(__DIR__ . '\..\cache\data\products.txt',json_encode($productsAndDetails));
    }
}


// [
//     [[100,"Iphone 13","200000","http:\/\/localhost\/public\/assets\/images\/iphone 13.jpg","2024-06-11 11:43:50",null],[101,"macbook","400000","http:\/\/localhost\/public\/assets\/images\/macbook.jpg","2024-06-11 11:54:45",null],[102,"s20","150000","http:\/\/localhost\/public\/assets\/images\/s20.jpg","2024-06-11 11:55:32",null],[103,"p50","200000","http:\/\/localhost\/public\/assets\/images\/p50.jpg","2024-06-11 11:56:10",null],[104,"apple watch","80000","http:\/\/localhost\/public\/assets\/images\/applewatch.jpg","2024-06-11 11:57:16",null]],
//     {},{}
//     ]