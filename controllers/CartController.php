<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Response;
use app\models\Cart;
use app\models\CartDetails;
use app\request\CartRequest;
use app\middlewares\AuthMiddleware;
use app\core\Log;
use app\core\Application;
use Exception;

class CartController extends Controller
{
    function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(["index", "addProduct", "removeProduct", "updateProductQuantity", "loadCartProducts"]));
    }

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
                Application::$app->response->setStatusCode(422);

                return json_encode(['success' => false, 'result' => $validateStatus['invalidReason']]);
            }

            $productId = intval($cartRequest->getProductId());

            $cartModel = new Cart();
            $cartId = $cartModel->getCartId();
            $cartDetailsModel = new CartDetails();
            $cartDetailsModel->addProduct($cartId, $productId, 1);
            Log::logInfo("CartController", "addProduct", "add a product to cart", "success", "cart id - $cartId;product id - $productId;1");
            Application::$app->response->setStatusCode(200);

            return json_encode(['success' => true, 'result' => "product added."]);
        } catch (Exception $exception) {
            Log::logError("CartController", "addProduct", "Exception raised when trying to add a product to cart", "failed", $exception->getMessage());
            Application::$app->response->setStatusCode(500);

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
                Log::logInfo("CartController", "removeProduct", "validation failed", "failed", $validateStatus['invalidReason']);
                Application::$app->response->setStatusCode(422);

                return json_encode(['success' => false, 'result' => $validateStatus['invalidReason']]);
            }

            $productId = intval($cartRequest->getProductId());

            $cartModel = new Cart();
            $cartId = $cartModel->getCartId();

            $cartDetailsModel = new CartDetails();
            $removedRows = $cartDetailsModel->removeProduct($cartId, $productId);
            Log::logInfo("CartController", "removeProduct", "remove products from cart", "success", "cart id - $cartId;product id - $productId");

            if ($removedRows > 0) {
                Application::$app->response->setStatusCode(200);

                return json_encode(['success' => true, 'result' => "$removedRows products removed."]);
            }
            Application::$app->response->setStatusCode(200);

            return json_encode(['success' => false, 'result' => "No products removed."]);
        } catch (Exception $exception) {
            Log::logError("CartController", "removeProduct", "Exception raised when trying to remove a product from cart", "failed", $exception->getMessage());
            Application::$app->response->setStatusCode(500);

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
                Log::logInfo("CartController", "updateProductQuantity", "validation failed", "failed", $validateStatus['invalidReason']);
                Application::$app->response->setStatusCode(422);

                return json_encode(['success' => false, 'result' => $validateStatus['invalidReason']]);
            }

            $productIdAndQuantity = $cartRequest->getProductIdAndQuantity();
            $productIdAndQuantity = array_map(function ($value) {

                return intval($value);
            }, $productIdAndQuantity);

            $cartModel = new Cart();
            $cartId = $cartModel->getCartId();

            $cartDetailsModel = new CartDetails();
            $updatedRows = $cartDetailsModel->updateProduct($cartId, $productIdAndQuantity['productId'], $productIdAndQuantity['quantity']);
            Log::logInfo("CartController", "updateProductQuantity", "update quantity of product in cart", "success", "cart id - $cartId;product id - {$productIdAndQuantity['productId']};product quantity - {$productIdAndQuantity['quantity']}");

            if ($updatedRows > 0) {
                Application::$app->response->setStatusCode(200);

                return json_encode(['success' => true, 'result' => "$updatedRows products updated."]);
            }
            Application::$app->response->setStatusCode(200);

            return json_encode(['success' => false, 'result' => "No products updated."]);
        } catch (Exception $exception) {
            Log::logError("CartController", "updateProductQuantity", "Exception raised when trying to update a product in cart", "failed", $exception->getMessage());
            Application::$app->response->setStatusCode(500);

            return "system error";
        }
    }

    /** 
     *    render cart page 
     *    @return string
     */
    function index()
    {
        try {
            $this->setLayout('main');
            Log::logInfo("CartController", "index", "render cart page", "success", "no data");
            Application::$app->response->setStatusCode(200);

            return $this->render('cart');
        } catch (Exception $exception) {
            $currentUser = Application::$userId;
            Log::logError("CartController", "index", "Exception raised when trying to render cart page, current user - $currentUser", "failed", $exception->getMessage());
            Application::$app->response->setStatusCode(500);

            return "system error";
        }
    }

    /** 
     *    send products in cart to front end
     *    @return string
     */
    function loadCartProducts(): string
    {
        try {
            $cartRequest = new CartRequest();
            $parameters = $cartRequest->getParametersToGetProductsByLimit();

            $cartDetailsModel = new CartDetails();
            $products = $cartDetailsModel->getProducts($parameters['start'], $parameters['length'], $parameters['searchValue']);
            Log::logInfo("CartController", "loadCartProducts", "got products in cart for current user", "success", "no data");

            $filteredData = count(value: $products);
            $totalRecords = 1000000;

            foreach ($products as &$product) {
                $product['productCard'] = Application::$app->view->buildCustomComponent(
                    "card",
                    $product['id'],
                    [
                        'source' => $product['link'],
                        'title' => $product['name'],
                        'body' => $product['price'],
                        'footer' => $product['quantity'],
                        'incButton' => ['text' => "+", 'className' => "inc-button"],
                        'subButton' => ['text' => "-", 'className' => "sub-button"],
                        'removeButton' => ['text' => "remove", 'className' => "remove-button"]
                    ]
                );
            }
            Log::logInfo("CartController", "loadCartProducts", "add product card for each of products to be returned", "success", "no data");

            $response = [
                "draw" => $parameters['draw'],
                "recordsTotal" => $filteredData,
                "recordsFiltered" => $totalRecords,
                "data" => $products
            ];
            Log::logInfo("CartController", "loadCartProducts", "send products in cart for current user to browser", "success", "no data");
            Application::$app->response->setStatusCode(200);

            return json_encode($response);
        } catch (Exception $exception) {
            $currentUser = Application::$userId;
            Log::logError("CartController", "loadCartProducts", "Exception raised when trying to get limited products in cart fro a user, current user - $currentUser", "failed", $exception->getMessage());
            Application::$app->response->setStatusCode(500);

            return "system error";
        }

    }
}