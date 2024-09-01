<?php

namespace app\models;

use app\logs\Log;

class OrderDetails extends BaseModel
{
    function __construct()
    {
        parent::__construct();
        $this->table("order_details");
    }

    // function addProducts(array $productsAndQuantities){
    //     foreach($productsAndQuantities as $)
    //     $data = ['product_id'=>[],'quantity'=>[]];
    // }

}