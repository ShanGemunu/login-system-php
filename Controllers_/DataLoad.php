<?php
require_once ('Model/DbConnection.php');
require_once ('Model/queries.php');

class DataLoad
{
    private $queries;
    private $conn;

    function __construct()
    {
        $this->conn = new DbConnection();
        $this->queries = new Queries($this->conn->conn);
    }

    // -> array
    function loadProducts()
    {

        $products = $this->queries->fetchProducts();
        return json_encode($products);
    }

    function loadProductsPagination($start, $length, $searchValue, $orderColumn, $orderDir){
        $productsPagination = $this->queries->fetchProductsPagination($start, $length, $searchValue, $orderColumn, $orderDir);
        return $productsPagination;
    }

}

// $data = /** whatever you're serializing **/;
// header('Content-Type: application/json; charset=utf-8');
// echo json_encode($data);

// $jsonData = file_get_contents('php://input');

// $data = json_decode($jsonData,true);

// $_SESSION['apiData'] = $jsonData;