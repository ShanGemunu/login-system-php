<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Response;
use app\models\Cart;
use app\models\CartDetails;
use app\request\CartRequest;
use app\core\Log;
use Exception;

class CartController extends Controller
{
    /** 
     *    add a product to cart
     *    @return string
     */
    function addProduct(): string
    {
        try {
            $cartRequest = new CartRequest();
            $validateStatus = $cartRequest->validateProductId();

            if (!$validateStatus['isValidated']) {
                Log::logInfo("CartController", "addProduct", "validation failed", "failed", $validateStatus['invalidReason']);

                return json_encode(['success' => false, 'result' => $validateStatus['invalidReason']]);
            }

            $productId = intval($cartRequest->getProductId());

            $cartModel = new Cart();

            //
            //
            // need to provide current user id 
            // string $data
            //

            $cartId = $cartModel->getCartId(4);
            $cartDetailsModel = new CartDetails();
            $cartDetailsModel->addProduct($cartId, $productId, 1);
            Log::logInfo("CartController","addProduct","add a product to cart","success","cart id - $cartId;product id - $productId;1");

            return json_encode(['success' => true, 'result' => "product added."]);
        } catch (Exception $exception) {
            Log::logError("CartController","addProduct","Exception raised when trying to add a product to cart","failed",$exception->getMessage());
            $response = new Response();
            $response->setStatusCode(500);

            return "system error";
        }
    }

    /** 
     *    remove a product from cart
     *    @return string
     */
    function removeProduct(): string
    {
        try {
            $cartRequest = new CartRequest();
            $validateStatus = $cartRequest->validateProductId();

            if (!$validateStatus['isValidated']) {
                Log::logInfo("CartController","removeProduct","validation failed","failed",$validateStatus['invalidReason']);

                return json_encode(['success' => false, 'result' => $validateStatus['invalidReason']]);
            }

            $productId = intval($cartRequest->getProductId());

            $cartModel = new Cart();

            //
            //
            // need to provide current user id 
            //
            //

            $cartId = $cartModel->getCartId(4);
            $cartDetailsModel = new CartDetails();
            $removedRows = $cartDetailsModel->removeProduct($cartId, $productId);
            Log::logInfo("CartController","removeProduct","remove products from cart","success","cart id - $cartId;product id - $productId");

            if ($removedRows > 0) {

                return json_encode(['success' => true, 'result' => "$removedRows products removed."]);
            }

            return json_encode(['success' => true, 'result' => "No products removed."]);
        } catch (Exception $exception) {
            Log::logError("CartController","removeProduct","Exception raised when trying to remove a product from cart","failed",$exception->getMessage());
            $response = new Response();
            $response->setStatusCode(500);

            return "system error";
        }
    }

    /** 
     *    update product's quantity in cart
     *    @return string
     */
    function updateProductQuantity(): string
    {
        try {
            $cartRequest = new CartRequest();
            $validateStatus = $cartRequest->validateProductIdAndQuantity();

            if (!$validateStatus['isValidated']) {
                Log::logInfo("CartController","updateProductQuantity","validation failed","failed",$validateStatus['invalidReason']);

                return json_encode(['success' => false, 'result' => $validateStatus['invalidReason']]);
            }

            $productIdAndQuantity = $cartRequest->getProductIdAndQuantity();
            $productIdAndQuantity = array_map(function ($value) {

                return intval($value);
            }, $productIdAndQuantity);

            $cartModel = new Cart();

            //
            //
            // need to provide current user id 
            //
            //
            $cartId = $cartModel->getCartId(4);
            $cartDetailsModel = new CartDetails();
            $updatedRows = $cartDetailsModel->updateProduct($cartId, $productIdAndQuantity['productId'], $productIdAndQuantity['quantity']);
            Log::logInfo("CartController","updateProductQuantity","update quantity of product in cart","success","cart id - $cartId;product id - {$productIdAndQuantity['productId']};product quantity - {$productIdAndQuantity['quantity']}");

            if ($updatedRows > 0) {

                return json_encode(['success' => true, 'result' => "$updatedRows products updated."]);
            }

            return json_encode(['success' => true, 'result' => "No products updated."]);
        } catch (Exception $exception) {
            Log::logError("CartController","updateProductQuantity","Exception raised when trying to update a product in cart","failed",$exception->getMessage());
            $response = new Response();
            $response->setStatusCode(500);

            return "system error";
        }
    }

    /** 
     *    render cart page 
     *    @return string
     */
    function index()
    {
        $this->setLayout('main');
        Log::logInfo("CartController","index","render cart page","success","no data");

        return $this->render('cart');
    }

    /** 
     *    send products in cart to front end
     *    @return string
     */
    function loadCartProducts(): string
    {
        try {
            $cartDetailsModel = new CartDetails();


            //
            //
            // need to provide current user id 
            //
            //
            $products = $cartDetailsModel->getProducts(4);
            Log::logInfo("CartController","loadCartProducts","send products from cart to front end","success","current user id: 4");

            return json_encode(['success' => true, 'result' => $products]);
        } catch (Exception $exception) {
            Log::logError("CartController","loadCartProducts","Exception raised when trying to send products from cart","failed",$exception->getMessage());
            $response = new Response();
            $response->setStatusCode(500);

            return "system error";
        }

    }
}