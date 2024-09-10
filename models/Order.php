<?php

namespace app\models;

use app\core\Log;

class Order extends BaseModel
{
    function __construct()
    {
        parent::__construct();
        $this->table("orders");
    }

    /** 
     *    create order in orders table and get that order id
     *    @param string $paymentMethod 
     *    @param int $userId 
     *    @return int  
     */
    function createAndGetOrder(string $paymentMethod, int $userId): int
    {
        $data = ['payment_method' => [$paymentMethod, 's'], 'user_id' => [$userId, 'i']];
        $this->insert($data);
        $column = [
            'maxId' => ["MAX(id)", "max_id"]
        ];
        // get recent order id created 
        $orderIdArray = $this->selectAs($column);
        Log::logInfo("Order","createAndGetOrder","create order in orders table and get that order id","success","payement method - $paymentMethod; user id - $userId");

        return $orderIdArray[0]['max_id'];
    }

    /** 
     *    get orders of specific user using specified columns and clauses
     *    @param int $userId
     *    @return array
     */
    function getOrders(int $userId): array
    {
        $this->whereOr("users.id", "=", $userId);
        $innerJoins = [
            ['mainTable' => ["order_details", "order_id"], 'subTable' => ["orders", "id"]],
            ['mainTable' => ["products", "id"], 'subTable' => ["order_details", "product_id"]]
        ];
        foreach ($innerJoins as $innerJoin) {
            $this->innerJoin($innerJoin);
        }
        $this->orderBy("order_details.quantity");
        $columns = [
            'ordersId' => ["orders.id", "orders_id"],
            'ordersDate' => ["orders.order_date", "orders_order_date"],
            'productName' => ["products.product_name", "products_product_name"],
            'productPrice' => ["products.price", "product_price"],
            'quantity' => ["order_details.quantity", "order_details_quantity"],
            'paymentMethod' => ["orders.payment_method", "orders_payment_method"],
            'orderState' => ["orders.state", "orders_state"]
        ];
        Log::logInfo("Order","getOrders","get orders of specific user using specified columns and clauses","success","user id - $userId");

        return $this->selectAs($columns);
    }
}