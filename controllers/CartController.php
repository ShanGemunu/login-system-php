<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Response;
use app\models\Cart;
use app\models\CartDetails;
use app\request\CartRequest;
use app\logs\Log;
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
                Log::logInfo("validation failed beacause of {$validateStatus['invalidReason']} at addProduct method of CartController");

                return $validateStatus['invalidReason'];
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
            $cartDetailsModel->addProduct($cartId, $productId, 1);
            Log::logInfo("add a product to cart at addProduct method of CartController");

            return "product added.";
        } catch (Exception $e) {
            Log::logError("Exception raised when trying to add a product to cart at addProduct method of CartController as " . $e->getMessage());

            return "system error";
        }
    }

    /** 
     *    remove a product from cart
     *    @return string
     */
    function removeProduct() : string
    {
        try {
            $cartRequest = new CartRequest();
            $validateStatus = $cartRequest->validateProductId();

            if (!$validateStatus['isValidated']) {
                Log::logInfo("validation failed beacause of {$validateStatus['invalidReason']} at addProduct method of CartController");

                return $validateStatus['invalidReason'];
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
            Log::logInfo("remove $removedRows products from cart at removeProduct method of CartController");

            if ($removedRows > 0) {

                return "$removedRows products removed.";
            }

            return "No products removed.";
        } catch (Exception $e) {
            Log::logError("Exception raised when trying to remove a product from cart at removeProduct method of CartController as " . $e->getMessage());

            return "system error";
        }
    }

    /** 
     *    update product's quantity in cart
     *    @return string
     */
    function updateProductQuantity()
    {
        try {
            $cartRequest = new CartRequest();
            $validateStatus = $cartRequest->validateProductIdAndQuantity();

            if (!$validateStatus['isValidated']) {
                Log::logInfo("validation failed beacause of {$validateStatus['invalidReason']} at updateProductQuantity method of CartController");

                return $validateStatus['invalidReason'];
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
            Log::logInfo("update a product from cart at updateProductQuantity method of CartController");

            if ($updatedRows > 0) {

                return "$updatedRows products updated.";
            }

            return "No products updated.";
        } catch (Exception $e) {
            Log::logError("Exception raised when trying to update a product in cart at updateProductQuantity method of CartController as " . $e->getMessage());

            return "system error";
        }
    }
}