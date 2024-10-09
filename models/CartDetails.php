<?php

namespace app\models;

use app\core\Log;

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
     *    @param int $userId
     *    @return array
     */
    function getProducts(int $userId)
    {
        $this->whereAnd("users.id", "=", $userId);
        $innerJoins = [
            ['joinFromTable' => ["cart", "id"], 'joinToTable' => ["cart_details", "cart_id"]],
            ['joinFromTable' => ["users", "id"], 'joinToTable' => ["cart", "belonged_user"]]
        ];
        foreach ($innerJoins as $innerJoin) {
            $this->innerJoin($innerJoin);
        }
        $columns = [
            'productId' => ["cart_details.product_id", "product_id"],
            'productQuantity' => ["cart_details.product_quantity", "product_quantity"],
        ];
        Log::logInfo("CartDetails","getProducts","get products from cart","success","user id - $userId");

        return $this->select($columns);

    }

}