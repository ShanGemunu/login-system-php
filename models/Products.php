<?php

namespace app\models;

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
    function getProductsByLimit(int $start, int $length, string $searchValue, string $orderColumn, string $order): array
    {
        $columns = ["id", "product_name", "price", "input_date", "quantity"];

        foreach ($columns as $column) {
            $this->whereOr($column, "like", $searchValue);
        }

        $this->orderBy($orderColumn, $order);
        $this->limit($length, $start);
        $products = $this->select();
        Log::logInfo("Products","getProductsByLimit","get limited number of products by search Value and limit","success","start - $start; length - $length; searchValue - $searchValue; order column - $orderColumn; order - $order");


        return $products;
    }

    /** 
     *    insert products as a infile(csv file) to db
     */
    function insertProductsAsInFile(): void
    {
        $this->insertInFile(["product_name", "price", "link", "quantity"], "products.csv");
        Log::logInfo("Products","insertProductsAsInFile","insert products as a infile(csv file) to db","success","no data");
    }

}