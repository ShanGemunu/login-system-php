<?php

class OrderDetails extends BaseModel{
    function __construct(){
        parent::__construct();
    }
    function addOrderDetailsForOneOrder($queryValuesString)
    {
        $query = "INSERT INTO order_details (order_id, product_id, quantity) VALUES " . $queryValuesString;
        $statement = $this->conn->prepare($query);
        if ($statement === false) {
            return [false, "coundn't prepair statement!"];
        }

        // try to add order detail log to db
        if ($statement->execute() === false) {
            return [false, "query failed."];
        }

        return [true];

    }
}