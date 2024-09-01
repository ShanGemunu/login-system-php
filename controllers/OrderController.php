<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Response;
use app\models\Cart;
use app\models\CartDetails;
use app\models\Order;
use app\request\CartRequest;
use app\logs\Log;
use Exception;

class OrderController extends Controller
{
    /** 
     *    get orders for current user 
     *    @param  
     *    @return string   
     */
    function getOrders(){
        $orderModel = new Order();


        //
        // need to provide currnet user id dynamically
        //
        $orders = $orderModel->getOrders(1);
        if(count($orders)>0){
            Log::logInfo("executed getOrders method of ProductController and return orders");

            return json_encode($orders);
        }
        Log::logInfo("executed getOrders method of ProductController and return no orders");

        return "No orders for current user";
    }

    function createOrder(){
        $orderModel = new Order();

      
        //
        // need to provide currnet user id and paymnet method dynamically 
        //
        $orderId = $orderModel->createAndGetOrder("cash",1);

        // SELECT cart_details.product_id,cart_details.product_quantity FROM cart_details 
        // INNER JOIN cart ON cart_details.cart_id=cart.id 
        // INNER JOIN users ON users.id = cart.belonged_user WHERE users.id = 4;
        $cartDetailsModel = new CartDetails();


        //
        // need to provide currnet user id dynamically 
        //
        $productsInCart = $cartDetailsModel->getProducts(4);

        return json_encode($products);
    }

}