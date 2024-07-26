<?php
require_once ('Model/DbConnection.php');
require_once ('Model/queries.php');
require_once __DIR__ . '/../vendor/autoload.php';

class Order
{
    private $conn;
    private $queries;

    function __construct()
    {
        $this->conn = new DbConnection();
        $this->queries = new Queries($this->conn->conn);
    }

    // fetch orders for current user
    function getOrders()
    {
        $output = ['isGetOrders'=>false,'orders'=>null];
        $ordersForCurrentUser = $this->queries->getOrdersForUser();
        if ($ordersForCurrentUser['isGetOrders'] === false) {   //  if data fetching failed somehow
            return $output;
        } elseif (count($ordersForCurrentUser['result']) === 0) {  // if current user has no orders
            $output['isGetOrders'] = true;
            return $output;
        }

        $grouppedOrders = [];

        // separate orders
        foreach ($ordersForCurrentUser['result'] as $item) {
            $id = $item['orders_id'];

            if (!isset($grouppedOrders[$id])) {
                $grouppedOrders[$id] = ['order_id'=>$item['orders_id'], 'order_date'=>$item['orders_order_date'], 
                'payment_method'=>$item['orders_payment_method'], 'order_state'=>$item['orders_state'], 'products'=>[]];
            }

            $grouppedOrders[$id]['products'][] = ['product_name'=>$item['products_product_name'], 'product_price'=>$item['product_price'], 
            'product_quantity'=>$item['order_details_quantity']];
        }
        $output['isGetOrders'] = true;
        $output['orders'] = $grouppedOrders;
        return $output;
    }

    // create and send order slip as pdf file
    // string -> array[bool,[] or string]
    function createOrderPdf($orderId)
    {
        try{
            $conn = new DbConnection();
            $queries = new Queries($conn->conn);

            $order = $queries->getOrder($orderId);

            $orderIdForHtml = $order[0]['orders_id'];
            $date = $order[0]['orders_order_date'];
            $paymentMethod = $order[0]['orders_payment_method'];
            $status = $order[0]['orders_state'];

            // products details of order
            $productsHtml = '';
            foreach ($order as $product) {
                $productName = $product['products_product_name'];
                $productPrice= $product['products_price'];
                $productQunatity = $product['order_details_quantity'];
                $tempProduct = "
                <h6>$productName</h6>
                <h6>$productPrice</h6>
                <h6>$productQunatity</h6><br>
                ";

                $productsHtml = $productsHtml . $tempProduct;
            }

            $htmlPage = "
                <!DOCTYPE html>
                    <html lang='en>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>Document</title>
                        <style>
                        body {
                        }
                        </style>
                    </head>
                    <body>
                        <div>
                            <h6>Order Id: $orderIdForHtml</h6>
                            <h6>Date: $date</h6>
                            <h6>Payment method: $paymentMethod</h6>
                            <h6>Status: $status</h6>
                        </div>
                        <div>
                            $productsHtml
                        </div>
                    </body>
                </html>
            ";

            // instantiate and use the dompdf class
            $dompdf = new Dompdf\Dompdf();
            $dompdf->loadHtml($htmlPage);

            // (Optional) Setup the paper size and orientation
            // $dompdf->setPaper('A4', 'landscape');

            // Render the HTML as PDF
            $dompdf->render();

            $slipName = "order_slip.pdf";

            // Output the generated PDF to variable
            $slipPdf = $dompdf->output();

            if (100 < strlen($slipPdf)) {
                return ['pdfFile'=>$slipPdf, 'pdfName'=>$slipName];
            } else {
                throw new Exception();
            }
        }catch(Exception $e){
            header('Content-Type: application/json; charset=utf-8');
            $output = "error";
            echo json_encode($output);

            // add log 
            $logger = new Logger();
            $logger->createLog("exception", $e->getMessage(), "Order", "createOrderPdf", $e->getLine(), $e->getFile());
            exit;
        }
        

    }

    // store orders in db
    // -> bool
    function storeOrderLog()
    {
        $isLogAdded = $this->queries->addOrderLog();
        if ($isLogAdded) {
            return true;
        } else {
            return false;
        }

    }

    // store order details log for each order in db
    // -> bool
    function storeOrderDetailsLog()
    {
        $encodedCart = file_get_contents(__DIR__ . '\..\cache\data\cart.txt');
        $decodedCart = json_decode($encodedCart, true);

        // get recent order id
        $recentOrderId = $this->queries->getRecentOrderId();
        // query value to add order details 
        $queryValuesString = '';

        // -------------------- reimplement using one query ---------------------------------

        foreach ($decodedCart as $product) {
            if ($queryValuesString != '')
                $queryValuesString .= ',';
            $queryValuesString .= '(' . $recentOrderId . ', ' . $product[0] . ', ' . $product[5] . ')';
        }

        $isOrderDetailsLogAdded = $this->queries->addOrderDetailLog($queryValuesString)[0];

        return $isOrderDetailsLogAdded;

        // ---------------------------------------------------------------------------------------------
    }


    //check for eligibility of products to make order
    // -> bool, array
    function checkProductEligibility()
    {
        $isAllProductsEligible = true;
        $unEligibleProducts = [];

        $encodedCart = file_get_contents(__DIR__ . '\..\cache\data\cart.txt');
        $decodedCart = json_decode($encodedCart, true);
        $encodedProducts = file_get_contents(__DIR__ . '\..\cache\data\products.txt');
        $decodedProducts = json_decode($encodedProducts, true);
        $decodedProducts = $decodedProducts[0];
        // ids of products in products array which are in cart array also and their quantities in cart
        $productsIdsAndNewQuantities = [];
        $cartQuantities = [];

        foreach ($decodedCart as $cProduct) {
            foreach ($decodedProducts as $pProduct) {
                if (($pProduct[0] === $cProduct[0]) and ($pProduct[5] < $cProduct[5])) {
                    $unEligibleProducts[] = $cProduct;
                    break;
                } elseif ($pProduct[0] === $cProduct[0]) {
                    $cartQuantities[] = [$pProduct[0], $cProduct[5]];
                    $productsIdsAndNewQuantities[] = [$pProduct[0], $pProduct[5] - $cProduct[5]];
                    break;
                }
            }
        }

        if (count($unEligibleProducts) === 0) {
            return [true, $productsIdsAndNewQuantities];
        } else {
            return [false];
        }
    }

    // subtract products quantity which are in cart(update produsts quantity)
    // array -> bool
    // function subtractProductsQuantity($productsIdsAndNewQuantities)
    // {
    //     $conn = new DbConnection();
    //     $queries = new Queries($conn->conn);
    //     $result = $queries->updateProducts($productsIdsAndNewQuantities);

    //     if ($result[0]) {
    //         $dataLoad = new DataLoad();
    //         if ($dataLoad->getConn() === null) {
    //             return false;
    //         }

    //         // sync local products with database
    //         $dataLoad->fetchProductsAndIds();

    //         // add order log to db
    //         $isOrderLogAdded = $this->storeOrderLog($queries);
    //         // add order details to db
    //         $isOrderDetailsLogAdded = $this->storeOrderDetailsLog($queries);

    //         // check is order lo
    //         if ($isOrderLogAdded and $isOrderDetailsLogAdded) {
    //             return true;
    //         } else {
    //             return false;
    //         }

    //     } else {
    //         return false;
    //     }
    // }

    // // 
    // function generateAndSendOrderSlip($orderId)
    // {

    // }

}