<?php

namespace app\models;

use app\core\Log;
use app\core\Application;

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
    function createAndGetOrder(string $paymentMethod = "cash"): int
    {
        $userId = Application::$userId;
        $data = ['payment_method' => [$paymentMethod, 's'], 'user_id' => [$userId, 'i']];
        $this->insert($data);
        $column = [
            'maxId' => ["MAX(id)", "max_id"]
        ];
        // get recent order id created 
        $orderIdArray = $this->select($column);
        Log::logInfo("Order","createAndGetOrder","create order in orders table and get that order id","success","payement method - $paymentMethod; user id - $userId");

        return $orderIdArray[0]['max_id'];
    }

    /** 
     *    get orders of specific user using specified columns and clauses
     *    @param int $userId
     *    @return array
     */
    function getOrders(int $start = null, int $length = null, string $searchValue = "%%"): array
    {
        $userId = Application::$userId;

        $this->whereAnd("orders.user_id", "=", $userId);
        $this->whereAnd("orders.order_date", "LIKE", $searchValue);
        $this->whereOr("products.product_name", "LIKE", $searchValue);
        $this->whereOr("products.price", "LIKE", $searchValue);
        $this->whereOr("order_details.quantity", "LIKE", $searchValue);
        $this->whereOr("orders.payment_method", "LIKE", $searchValue);
        $this->whereOr("orders.state", "LIKE", $searchValue);
        $this->addSubWhereAnd(" AND ");
        $this->orderBy("orders.id");

        $innerJoins = [
            ['joinFromTable' => ["order_details", "order_id"], 'joinToTable' => ["orders", "id"]],
            ['joinFromTable' => ["products", "id"], 'joinToTable' => ["order_details", "product_id"]]
        ];
        foreach ($innerJoins as $innerJoin) {
            $this->innerJoin($innerJoin);
        }
        if($start && $length){
            $this->limit($length, $start);
        }
        
        $columns = [
            'ordersId' => ["orders.id", "order_id"],
            'ordersDate' => ["orders.order_date", "order_date"],
            'productName' => ["products.product_name", "product_name"],
            'productPrice' => ["products.price", "product_price"],
            'quantity' => ["order_details.quantity", "product_quantity"],
            'paymentMethod' => ["orders.payment_method", "order_payment_method"],
            'orderState' => ["orders.state", "order_state"]
        ];
        Log::logInfo("Order","getOrders","get orders of specific user using specified columns and clauses","success","user id - $userId");

        return $this->select($columns);
    }
}