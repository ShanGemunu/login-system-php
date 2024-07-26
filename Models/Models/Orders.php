<?php
require_once __DIR__.'/BaseModel.php';

class Orders extends BaseModel{
    function __construct(){
        parent::__construct();
    }
    function getOrdersForUser()
    {
        $output = ['isGetOrders' => false, 'result' => null];
        try {
            // [[order-detail,order-detail->product-01],[order-detail,order-detail->product-02,...]
            $query = "SELECT orders.id as orders_id, orders.order_date as orders_order_date, products.product_name as products_product_name, 
            products.price as product_price, order_details.quantity as order_details_quantity, orders.payment_method as
            orders_payment_method, orders.state as orders_state FROM orders INNER JOIN order_details ON orders.id=order_details.order_id
            INNER JOIN products ON products.id = order_details.product_id WHERE orders.user_id = ? ORDER BY orders.id";

            $statement = $this->conn->prepare($query);

            if ($statement === false) {
                throw new SqlFailedPrepair("coundn't prepair statement!");
            }

            // $currentUserId = $_SESSION['currentUser'];
            $currentUserId = 1;

            $statement->bind_param("i", $currentUserId);

            if ($statement->execute() === false) {
                throw new SqlQueryFailed("query failed.");
            }

            $result = $statement->get_result();
            $result = $result->fetch_all(MYSQLI_ASSOC);  //  get empty array [] if user id is not in orders table,  if user id is in orders table then all order details shows for all orders of user[[order_details-1],[order_details-2],..]

            $output['isGetOrders'] = true;
            $output['result'] = $result;
            return $output;
        } catch (SqlFailedPrepair $e) {
            $output['isGetOrders'] = false;
            $output['result'] = null;

            // add log
            $logger = new Logger();
            $logger->createLog("exception", $e->getMessage(), "Queries", "getOrdersForUser", $e->getLine(), $e->getFile());
            return $output;
        } catch (Exception $e) {
            $output['isGetOrders'] = false;
            $output['result'] = null;

            // add log
            $logger = new Logger();
            $logger->createLog("exception", $e->getMessage(), "Queries", "getOrdersForUser", $e->getLine(), $e->getFile());
            return $output;
        }
    }

    function getOrder($orderId)
    {
        try {
            // [[order-detail-01,order-detail-01->product-01],[order-detail-01,order-detail-01->product-02],...]
            $query = "SELECT orders.id as orders_id, orders.order_date as orders_order_date, products.product_name as products_product_name, products.price as products_price, 
            order_details.quantity as order_details_quantity, orders.payment_method as orders_payment_method, orders.state as orders_state FROM orders INNER JOIN order_details ON orders.id=order_details.order_id
            INNER JOIN products ON products.id = order_details.product_id WHERE orders.id = ?";

            $statement = $this->conn->prepare($query);

            if ($statement === false) {
                throw new SqlFailedPrepair();
            }

            $statement->bind_param("i", $orderId);

            if ($statement->execute() === false) {
                throw new SqlQueryFailed();
            }

            $result = $statement->get_result();
            $result = $result->fetch_all(MYSQLI_ASSOC);

            return $result;
        } catch (SqlFailedPrepair $e) {
            header('Content-Type: application/json; charset=utf-8');
            $output = "error";
            echo json_encode($output);

            // add log 
            $logger = new Logger();
            $logger->createLog("exception", $e->getMessage(), "Queries", "getOrder", $e->getLine(), $e->getFile());
            exit;
        } catch (SqlQueryFailed $e) {
            header('Content-Type: application/json; charset=utf-8');
            $output = "error";
            echo json_encode($output);

            // add log 
            $logger = new Logger();
            $logger->createLog("exception", $e->getMessage(), "Queries", "getOrder", $e->getLine(), $e->getFile());
            exit;
        }

    }

    function addOrder()
    {
        $query = "INSERT INTO orders (payment_method, user_id, state) VALUES (?,?,?)";
        $statement = $this->conn->prepare($query);
        if ($statement === false) {
            return [false, "coundn't prepair statement!"];
        }
        // get current user id
        $currentUserId = $this->getUserId()[0][0];
        $paymentMethod = "debit-card";
        $status = "pending";

        $statement->bind_param("sis", $paymentMethod, $currentUserId, $status);

        // add log to db
        if ($statement->execute() === false) {
            return [false, "query failed."];
        }

        return true;
    }

    function getRecentOrderId()
    {
        $query = "SELECT MAX(id) FROM orders";
        $statement = $this->conn->prepare($query);
        if ($statement === false) {
            return [false, "coundn't prepair statement!"];
        }

        // add log to db
        if ($statement->execute() === false) {
            return [false, "query failed."];
        }

        $result = $statement->get_result();
        return $result->fetch_all()[0][0];  // return max order id
    }
}