<?php

namespace app\models;

use app\core\Log;
use app\core\Application;

class CartDetails extends BaseModel
{
    function __construct()
    {
        parent::__construct();
        $this->table("cart_details");
    }

    /** 
     *    add product to cart
     *    @param int $cartId
     *    @param int $productId
     *    @param int $quantity
     *    @return bool
     */
    function addProduct(int $cartId, int $productId, int $quantity)
    {
        $data = ['cart_id' => [$cartId, 'i'], 'product_id' => [$productId, 'i'], 'product_quantity' => [$quantity, 'i']];
        $this->insert($data);
        Log::logInfo("CartDetails","addProduct","add a product to cart","success","cartId - $cartId; productId - $productId; quantity - $quantity");

        return true;
    }

    /** 
     *    remove a product from cart
     *    @param int $cartId
     *    @param int $productId
     *    @return int
     */
    function removeProduct(int $cartId, int $productId): int
    {
        $this->whereAnd("cart_id", "=", $cartId);
        $this->whereAnd("product_id", "=", $productId);
        Log::logInfo("CartDetails","removeProduct","remove a product from cart","success","cartId - $cartId; productId - $productId");

        return $this->delete();
    }

    /** 
     *    update a product from cart
     *    @param int $cartId
     *    @param int $productId
     *    @param int $quantity
     *    @return int
     */
    function updateProduct(int $cartId, int $productId, int $quantity): int
    {
        $this->whereAnd("cart_id", "=", $cartId);
        $this->whereAnd("product_id", "=", $productId);
        Log::logInfo("CartDetails","updateProduct","update a product from cart","success","cartId - $cartId; productId - $productId; quantity - $quantity");

        return $this->update(['product_quantity' => [$quantity, 'i']]);
    }

    /** 
     *    get products from cart 
     *    @return array
     */
    function getProducts(int $start, int $length, string $searchValue)
    {
        $currentUserId = Application::$userId;
    
        $this->whereAnd("cart.belonged_user", "=", $currentUserId);
        $this->whereAnd("products.product_name", "LIKE", $searchValue);
        $this->whereOr("products.price", "LIKE", $searchValue);
        $this->whereOr("products.quantity", "LIKE", $searchValue);
        $this->addSubWhereAnd(" AND ");

        $innerJoins = [
            ['joinFromTable' => ["cart", "id"], 'joinToTable' => ["cart_details", "cart_id"]],
            ['joinFromTable' => ["products", "id"], 'joinToTable' => ["cart_details", "product_id"]]
        ];
        foreach ($innerJoins as $innerJoin) {
            $this->innerJoin($innerJoin);
        }
        $this->limit($length, $start);
        $columns = [
            'productId' => ["products.id", "id"],
            'productName' => ["products.product_name", "name"],
            'productPrice' => ["products.price", "price"],
            'productLink' => ["products.link", "link"],
            'productQuantity' => ["cart_details.product_quantity", "quantity"]
        ];
        Log::logInfo("CartDetails","getProducts","get products from cart","pending","user id - $currentUserId; search value - $searchValue; offset - $start; limit - $length");

        return $this->select($columns);
    }

}