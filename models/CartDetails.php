<?php

namespace app\models;

use app\logs\Log;

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
        Log::logInfo("addProduct method of CartDetails executed with parameters - cartId: $cartId, productId: $productId, quantity: $quantity");

        return true;
    }

    /** 
     *    remove product from cart
     *    @param int $cartId
     *    @param int $productId
     *    @return int
     */
    function removeProduct(int $cartId, int $productId) : int
    {
        $this->whereAnd("cart_id","=",$cartId);
        $this->whereAnd("product_id","=",$productId);
        Log::logInfo("removeProduct method of CartDetails executed with parameters - cartId: $cartId, productId: $productId");

        return $this->delete();
    }

     /** 
     *    update product from cart
     *    @param int $cartId
     *    @param int $productId
     *    @param int $quantity
     *    @return int
     */
    function updateProduct(int $cartId, int $productId, int $quantity) : int
    {
        $this->whereAnd("cart_id","=",$cartId);
        $this->whereAnd("product_id","=",$productId);
        Log::logInfo("updateProduct method of CartDetails executed with parameters - cartId: $cartId, productId: $productId, quantity: $quantity");

        return $this->update(['product_quantity'=>[$quantity,'i']]);
    }

    /** 
     *    get products from cart 
     *    @param int $userId
     *    @return array
     */
    function getProducts(int $userId){
        $this->whereAnd("users.id","=",$userId);
        $innerJoins = [
            ['mainTable'=>["cart","id"],'subTable'=>["cart_details","cart_id"]],
            ['mainTable'=>["users","id"],'subTable'=>["cart","belonged_user"]]
        ];
        foreach($innerJoins as $innerJoin){
            $this->innerJoin($innerJoin);
        } 
        $columns = [
            'productId'=>["cart_details.product_id","product_id"],
            'productQuantity'=>["cart_details.product_quantity","product_quantity"],
        ];

        return $this->selectAs($columns);

    }

}