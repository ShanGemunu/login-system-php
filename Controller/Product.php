<?php
require_once('Model/DbConnection.php');
require_once('Model/queries.php');

class Product{

    // increase products qunatity
    // 
    function addProductQuantity($decodedJsonData){

        $productsIdsAndNewQuantities = [];

        foreach($decodedJsonData as $product){
            $item = [$product[0],$product[5]];
            $productsIdsAndNewQuantities[] = $item;
        }

        $conn = new DbConnection();
        $queries = new Queries($conn->conn);

        $result = $queries->updateProducts($productsIdsAndNewQuantities);
        
        $isProductsUpdated = $result[0];

        if($isProductsUpdated) return [true, "products updated"];


    

        // $encodedProductsAndDetails = file_get_contents(__DIR__ . '\..\cache\data\products.txt');
        // $decodedProductsAndDetials = json_decode($encodedProductsAndDetails, true);
        // $minProductId = intval($decodedProductsAndDetials[1][0][0]);
        // $maxProductId = intval($decodedProductsAndDetials[2][0][0]);
        // if(!($minProductId <= $productId and $productId<= $maxProductId)){
        //     return [false, "error: invalid product id"];
        // }

        // $products = $decodedProductsAndDetials[0];
        // $conn = new DbConnection();
        // $queries = new Queries($conn->conn);

        // $isInputQuantityValid = false;
        // $result = null;
        
        // // updating products in db
        // foreach($products as $product){
        //     $_productId = $product[0];
        //     $_quantity = $product[5];
        //     if((intval($_productId) === $productId) and intval($_quantity<$quantity and $quantity<101)){
        //         $result = $queries->updateProducts([[$_productId, $_quantity]]);
        //         $isInputQuantityValid = true;
        //         break;
        //     }
        // }

        // // check if user sends valid qunatity
        // if(!($isInputQuantityValid)){
        //     return [false, "error: invalid quantity"];
        // }
        
        // // is product qunatity added to db successfully
        // $isProductQuantityUpdated = $result[0];

        // // check if product qunatity added to db successfully
        // if(!$isProductQuantityUpdated){
        //     return [false,"error: Couldn't update product quantity in db"];
        // }

        // $dataLoad = new DataLoad();
        // // check if db connection created
        // if($dataLoad->getConn()){
        //     // sync up products in local with products in db
        //     $dataLoad->fetchProductsAndIds();
        //     return [true, "Products quantity updated in db successfully and sync up with local"];
        // }else{
        //     return [false,"error: Update products quantity successfully in db, but couldn't sync up with local"];
        // }

    }


    // subtract products quantity (update produsts quantity)
    // array -> bool
    function subtractProductsQuantity($productsIdsAndNewQuantities){
        $conn = new DbConnection();
        $queries = new Queries($conn->conn);
        $result = $queries->updateProducts($productsIdsAndNewQuantities);
        
        if($result[0]){
            // sync local products with database
            $dataLoad = new DataLoad();
            if($dataLoad->getConn()){
                $dataLoad->fetchProductsAndIds();
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}