<?php

namespace app\models;

use app\core\Application;
use app\core\Log;

class Products extends BaseModel
{
    function __construct()
    {
        parent::__construct();
        $this->table("products");
    }

    /** 
     *    get limited number of products by search Value and limit
     *    @param int $start
     *    @param int $length
     *    @param string $searchValue
     *    @return array
     */
    function getProductsByLimitManage(int $start, int $length, string $searchValue, string $orderColumn, string $order): array
    {
        $columns = ["id", "product_name", "price", "input_date", "quantity"];

        foreach ($columns as $column) {
            $this->whereOr($column, "like", $searchValue);
        }

        $this->orderBy($orderColumn, $order);
        $this->limit($length, $start);
        $products = $this->select();
        Log::logInfo("Products", "getProductsByLimit", "get limited number of products by search Value and limit", "success", "start - $start; length - $length; searchValue - $searchValue; order column - $orderColumn; order - $order");

        return $products;
    }

    /** 
     *    get limited number of products by search Value and limit
     *    @param int $start
     *    @param int $length
     *    @param string $searchValue
     *    @return array
     */
    function getProducts(int $start, int $length, string $searchValue): array
    {
        $columns = ["products.product_name", "products.price", "products.quantity"];

        foreach ($columns as $column) {
            $this->whereOr($column, "like", $searchValue);
        }
        $currentUserId = Application::$userId;
        $case = "
            CASE 
                WHEN cart_details.product_id IS NOT NULL AND cart.belonged_user = $currentUserId THEN 'In Cart' 
                ELSE 'Not In Cart' 
            END AS cart_status
        ";

        $leftJoins = [
            ['joinFromTable' => ["cart_details", "product_id"], 'joinToTable' => ["products", "id"]],
            ['joinFromTable' => ["cart", "id"], 'joinToTable' => ["cart_details", "cart_id"]],
            ['joinFromTable' => ["users", "id"], 'joinToTable' => ["cart", "belonged_user"]]
        ];
        foreach ($leftJoins as $leftJoin) {
            $this->leftJoin($leftJoin);
        }
        $this->limit($length, $start);
        $products = $this->select([
            'id' => ["products.id", null],
            'product_name' => ["products.product_name", null],
            'price' => ["products.price", null],
            'link' => ["products.link",null],
            'quantity' => ["products.quantity", null]
        ], $case);
        Log::logInfo("Products", "getProducts", "get limited number of products by search Value and limit", "success", "start - $start; length - $length; searchValue - $searchValue");

        return $products;
    }

     /** 
     *    get limited number of products by search Value and limit for guest users
     *    @param int $start
     *    @param int $length
     *    @param string $searchValue
     *    @return array
     */
    function getProductsTrail(int $start, int $length, string $searchValue): array
    {
        $columns = ["products.product_name", "products.price", "products.quantity"];

        foreach ($columns as $column) {
            $this->whereOr($column, "like", $searchValue);
        }
       
        $this->limit($length, $start);
        $products = $this->select([
            'id' => ["products.id", null],
            'product_name' => ["products.product_name", null],
            'price' => ["products.price", null],
            'link' => ["products.link", null],
            'quantity' => ["products.quantity", null]
        ]);
        Log::logInfo("Products", "getProductsTrail", "get limited number of products by search Value and limit for guest users", "success", "start - $start; length - $length; searchValue - $searchValue");

        return $products;
    }

    /** 
     *    insert products as a infile(csv file) to db
     */
    function insertProductsAsInFile(): void
    {
        $this->insertInFile(["product_name", "price", "link", "quantity"], "csv");
        Log::logInfo("Products", "insertProductsAsInFile", "insert products as a infile(csv file) to db", "success", "no data");
    }

}