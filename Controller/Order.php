<?php
require_once('Model/DbConnection.php');
require_once('Model/queries.php');

class Order{
    private $conn;
    private $queries;

    function __construct(){
        $this->conn = new DbConnection();
        $this->queries = new Queries($this->conn->conn);
    }

    // fetch orders for current user
    function getOrders(){
        $ordersForCurrentUser = $this->queries->getOrdersForUser();
        if($ordersForCurrentUser[0] === false){   //  if data fetching falied somehow
            return [false,null];
        }elseif(count($ordersForCurrentUser[1]) === 0){  // if current user has no orders
            return [true,null];
        }

        $grouppedOrders = [];
        
        // separate orders
        foreach($ordersForCurrentUser[1] as $item){
            $id = $item[0];

            if(!isset($grouppedOrders[$id])){
                $grouppedOrders[$id] = [];
            }

            $grouppedOrders[$id][] = $item;
        }
        return [true,$grouppedOrders];
    }

    // store orders created in db
    // -> bool
    function storeOrderLog(){
        $isLogAdded = $this->queries->addOrderLog();
        if($isLogAdded){
            return true;
        }else{
            return false;
        }
        
    }

    // store order details for each order created in db
    // -> bool
    function storeOrderDetailsLog(){
        $encodedCart = file_get_contents(__DIR__ . '\..\cache\data\cart.txt');
        $decodedCart = json_decode($encodedCart, true);

        // get recent order id
        $recentOrderId = $this->queries->getRecentOrderId();
        // query value to add order details 
        $queryValuesString = '';

        // -------------------- reimplement using one query ---------------------------------
        
        foreach($decodedCart as $product){
            if($queryValuesString != '') $queryValuesString.= ',';
            $queryValuesString .= '('. $recentOrderId .', '. $product[0] .', '. $product[5] .')';
        }
        
        $isOrderDetailsLogAdded = $this->queries->addOrderDetailLog($queryValuesString)[0];

        return $isOrderDetailsLogAdded;
       
        // ---------------------------------------------------------------------------------------------
    }

    
    //check for eligibility of products to make order
    // -> bool, array
    function checkProductEligibility(){
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

        foreach($decodedCart as $cProduct){
            foreach($decodedProducts as $pProduct){
                if(($pProduct[0] === $cProduct[0]) and ($pProduct[5]<$cProduct[5])){
                    $unEligibleProducts[] = $cProduct;
                    break;
                }elseif($pProduct[0] === $cProduct[0]){
                    $cartQuantities[] = [$pProduct[0], $cProduct[5]];
                    $productsIdsAndNewQuantities[] = [$pProduct[0], $pProduct[5]-$cProduct[5]];
                    break;
                }
            }
        }

        if(count($unEligibleProducts)===0){
            return [true, $productsIdsAndNewQuantities];
        }else{
            return [false];
        }
    }

    // subtract products quantity which are in cart(update produsts quantity)
    // array -> bool
    function subtractProductsQuantity($productsIdsAndNewQuantities){
        $conn = new DbConnection();
        $queries = new Queries($conn->conn);
        $result = $queries->updateProducts($productsIdsAndNewQuantities);
        
        if($result[0]){
            $dataLoad = new DataLoad();
            if($dataLoad->getConn() === null ){
                return false;
            }

            // sync local products with database
            $dataLoad->fetchProductsAndIds();

            // add order log to db
            $isOrderLogAdded = $this->storeOrderLog($queries);
            // add order details to db
            $isOrderDetailsLogAdded = $this->storeOrderDetailsLog($queries);
            
            // check is order lo
            if($isOrderLogAdded and $isOrderDetailsLogAdded){
                return true;
            }else{
                return false;
            }
            
        }else{
            return false;
        }
    }
}