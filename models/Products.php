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
     *    get limited number of products by search Value and limit for seller and admin to crud products
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
     *    get limited number of products by search Value and limit for users to add products to cart
     *    @param int $start
     *    @param int $length
     *    @param string $searchValue
     *    @return array
     */
    function getProducts(int $start, int $length, string $searchValue): array
    {
        $columns = ["products.product_name", "products.price", "products.quantity"];

        foreach ($columns as $column) {
            $this->whereOr($column, "like", $searchValue, true);
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
            $this->leftJoin($leftJoin, true);
        }
        $this->limit(1000, 0, true);

        $subQuery = $this->selectSubQuery([
            'id' => ["products.id", null],
            'product_name' => ["products.product_name", null],
            'price' => ["products.price", null],
            'link' => ["products.link",null],
            'quantity' => ["products.quantity", null]
        ], $case);

        $this->groupBy([
            "id",
            "product_name",
            "price",
            "link",
            "quantity"
        ]);

        $this->limit($length, $start);

        $products = $this->select([
            'id' => ["id", null],
            'product_name' => ["product_name", null],
            'price' => ["price", null],
            'link' => ["link",null],
            'quantity' => ["quantity", null],
            'MIN(cart_status)' => ["MIN(cart_status)", "cart_status"]
        ], "",$subQuery);

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
     *    @param array $productIds
     *    @return array
     */
    function getProductsForMakeOrder(array $productIds){
        $productIdsString = implode(',', $productIds);
        $this->whereIn("id",$productIdsString);
        $this->orderBy("id");

        $products = $this->select([
            'id' => ["products.id", "id"],
            'quantity' => ["products.quantity", "quantity"] 
        ]);

        $productsWithKeys = [];

        foreach($products as $product){
            $productsWithKeys["{$product['id']}"] = $product;
        }
        Log::logInfo("Products", "getProductsForMakeOrder", "get products of cart of current user to check if each product in order not exceed each of product quantity", "success", "");

        return $productsWithKeys;
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