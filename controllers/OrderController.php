<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Response;
use app\models\CartDetails;
use app\models\Order;
use app\models\OrderDetails;
use app\middlewares\AuthMiddleware;
use app\core\Application;
use app\core\Log;
use Exception;

class OrderController extends Controller
{
    function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(["getOrder","createOrder"]));
    }

    /** 
     *    get orders for current user 
     *    @param  
     *    @return string   
     */
    function getOrders()
    {
        try {
            $orderModel = new Order();


            //
            // need to provide currnet user id dynamically
            //
            $orders = $orderModel->getOrders(1);
            if (count($orders) > 0) {
                Log::logInfo("OrderController", "getOrders", "orders having for current user", "success", "no data");

                return json_encode(['success' => true, 'result' => $orders]);
            }
            Log::logInfo("OrderController", "getOrders", "No orders for current user", "success", "no data");
            Application::$app->response->setStatusCode(200);

            return json_encode(['success' => true, 'result' => "No orders for current user"]);
        } catch (Exception $exception) {
            Log::logError("OrderController","getOrders","Exception raised when trying to get orders for current user","failed",$exception->getMessage());
            Application::$app->response->setStatusCode(500);

            return "system error";
        }

    }

    /** 
     *    create order for current user 
     *    @return string   
     */
    function createOrder()
    {
        try {
            $cartDetailsModel = new CartDetails();


            //
            // need to provide current user id  
            //
            $productsAndQuantitiesInCart = $cartDetailsModel->getProducts(4);

            if (count($productsAndQuantitiesInCart) === 0) {
                Log::logInfo("OrderController", "createOrder", "No Products in cart to make order", "success", "no data");
                Application::$app->response->setStatusCode(200);

                return json_encode(['success' => true, 'result' => "No Products in cart to make order"]);
            }


            //
            // need to provide currnet user id and paymnet method dynamically 
            //
            $orderModel = new Order();
            $orderId = $orderModel->createAndGetOrder("cash", 4);
            $orderDetails = new OrderDetails();
            $orderDetails->addProducts($orderId, $productsAndQuantitiesInCart);
            Log::logInfo("OrderController", "createOrder", "Order Placed Successfully", "success", "order id-$orderId;userId-4");
            Application::$app->response->setStatusCode(200);

            return json_encode(['success' => true, 'result' => "Order Placed"]);
        } catch (Exception $exception) {
            Log::logError("OrderController","getOrders","Exception raised when trying to create order for current user","failed",$exception->getMessage());
            Application::$app->response->setStatusCode(500);

            return "system error";
        }

    }

}