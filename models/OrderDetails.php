<?php

namespace app\models;

use app\core\Log;

class OrderDetails extends BaseModel
{
    function __construct()
    {
        parent::__construct();
        $this->table("order_details");
    }

    function addProducts(int $orderId, array $productsAndQuantities)
    {
        foreach ($productsAndQuantities as $value) {
            $data = ['order_id' => [$orderId, 'i'], 'product_id' => [$value['product_id'], 'i'], 'quantity' => [$value['product_quantity'], 'i']];
            $this->insert($data);
        }

    }

}