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
            $data = ['order_id' => [$orderId, 'i'], 'product_id' => [$value['id'], 'i'], 'quantity' => [$value['quantity'], 'i']];
            $this->insert($data);
        }

    }

}