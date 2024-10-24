<?php

namespace app\controllers;

use app\core\Controller;
use app\request\OrderRequest;
use app\models\CartDetails;
use app\models\Order;
use app\models\Products;
use app\models\OrderDetails;
use app\middlewares\AuthMiddleware;
use app\core\Application;
use app\core\Log;
use Exception;

class OrderController extends Controller
{
    function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(["index", "getOrders", "createOrder"]));
    }

    /** 
     *    render order page
     *    @param  
     *    @return string   
     */
    function index(): string
    {
        try {
            $this->setLayout('main');
            Log::logInfo("OrderController", "index", "render order page", "success", "no data");
            Application::$app->response->setStatusCode(200);

            return $this->render('order');
        } catch (Exception $exception) {
            $currentUser = Application::$userId;
            Log::logError("OrderController", "index", "Exception raised when trying to render order page, current user - $currentUser", "failed", $exception->getMessage());
            Application::$app->response->setStatusCode(500);

            return "system error";
        }
    }

    /** 
     *    get orders for current user 
     *    @param  
     *    @return string   
     */
    function getOrders(): string
    {
        try {
            $orderRequest = new OrderRequest();
            $parameters = $orderRequest->getParametersToGetOrdersByLimit();

            $orderModel = new Order();
            $orders = $orderModel->getOrders($parameters['start'], $parameters['length'], $parameters['searchValue']);

            $filteredData = count(value: $orders);
            $totalRecords = 100;

            if (count($orders) > 0) {
                // format all orders 
                // add first element 
                $formattedOrders[] = [
                    'order_id'=> $orders[0]['order_id'],
                    'order_date' => $orders[0]['order_date'],
                    'order_payment_method' => $orders[0]['order_payment_method'],
                    'order_status' => $orders[0]['order_state'],
                    'products' => [
                        [
                            'name' => $orders[0]['product_name'],
                            'price' => $orders[0]['product_price'],
                            'quantity' => $orders[0]['product_quantity']
                        ]
                    ]
                ];
                // then looping to add other elements
                foreach (array_slice($orders, 1) as $order) {
                    $lastElementOrderId = end($formattedOrders)['order_id'];

                    if ($lastElementOrderId === $order['order_id']) {
                        $formattedOrders[count($formattedOrders) - 1]['products'][] = [
                            'name' => $order['product_name'],
                            'price' => $order['product_price'],
                            'quantity' => $order['product_quantity']
                        ];
                        continue;
                    }

                    $formattedOrders[] = [
                        'order_id'=> $order['order_id'],
                        'order_date' => $order['order_date'],
                        'order_payment_method' => $order['order_payment_method'],
                        'order_status' => $order['order_state'],
                        'products' => [
                            [
                                'name' => $order['product_name'],
                                'price' => $order['product_price'],
                                'quantity' => $order['product_quantity']
                            ]
                        ]
                    ];
                }

                $response = [
                    "draw" => $parameters['draw'],
                    "recordsTotal" => $filteredData,
                    "recordsFiltered" => $totalRecords,
                    "data" => $formattedOrders
                ];

                Log::logInfo("OrderController", functionName: "getOrders", stepDescription: "current user is having orders, send them to browser", stepStatus: "success", data: "no data");
                Application::$app->response->setStatusCode(200);

                return json_encode($response);
            }
            Log::logInfo("OrderController", "getOrders", stepDescription: "No orders for current user", stepStatus: "success", data: "no data");
            Application::$app->response->setStatusCode(200);

            return json_encode(['success' => true, 'result' => false]);
        } catch (Exception $exception) {
            Log::logError("OrderController", "getOrders", "Exception raised when trying to get orders for current user", "failed", $exception->getMessage());
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
            $insufficientProducts = [];
            $cartDetailsModel = new CartDetails();
            $productsInCart = $cartDetailsModel->getProducts();

            if (count($productsInCart) === 0) {
                Log::logInfo("OrderController", "createOrder", "No Products in cart to make order", "success", "no data");
                Application::$app->response->setStatusCode(200);

                return json_encode(['success' => false, 'result' => "No Products in cart to make order"]);
            }

            $productIds = [];

            foreach ($productsInCart as $product) {
                $productIds[] = $product['id'];
            }

            $productModel = new Products();
            $productsInstock = $productModel->getProductsForMakeOrder($productIds);

            foreach ($productsInCart as $product) {
                if ($product['quantity'] > $productsInstock[$product['id']]['quantity']) {
                    $insufProduct = $product;
                    $insufProduct['stockValue'] = $productsInstock[$product['id']]['quantity'];
                    $insufficientProducts[] = $insufProduct;
                }
            }

            if (count($insufficientProducts) > 0) {
                Log::logInfo("OrderController", "createOrder", "Order can not procced because of insufficient quantity in stock", "success", "no data");
                Application::$app->response->setStatusCode(200);

                return json_encode(['success' => false, 'result' => $insufficientProducts]);
            }

            $orderModel = new Order();
            $orderId = $orderModel->createAndGetOrder();

            $orderDetails = new OrderDetails();
            $orderDetails->addProducts($orderId, $productsInCart);
            Log::logInfo("OrderController", "createOrder", "Order Placed Successfully", "success", "order id-$orderId;userId-4");
            Application::$app->response->setStatusCode(200);

            return json_encode(['success' => true, 'result' => "Order Placed"]);
        } catch (Exception $exception) {
            Log::logError("OrderController", "getOrders", "Exception raised when trying to create order for current user", "failed", $exception->getMessage());
            Application::$app->response->setStatusCode(500);

            return "system error";
        }

    }

}