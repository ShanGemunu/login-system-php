<?php
// cart -> [[product_01],[product_02],...,[product_x]]  ,  products -> [[[product_01],[product_02],..,[product_x]],[[min_product_id]],[[max_product_id]]]

class Cart{
    // add product to cart
    function addProduct($productId){
        $cart = file_get_contents(__DIR__ . '\..\cache\data\cart.txt');
        // if cart is empty
        if(strlen($cart)==0){
            $productsWithIds = file_get_contents(__DIR__ . '\..\cache\data\products.txt');
            $productsWithIds = json_decode($productsWithIds,true);
            $products = $productsWithIds[0];
            foreach ($products as $product){
                if($productId == $product[0]){
                    $channgedProduct = $product;
                    $channgedProduct[5] = 1;
                    file_put_contents(__DIR__ . '\..\cache\data\cart.txt',json_encode([$channgedProduct]));
                    return null;
                }
            }
        }else{
            $decodedCart = json_decode($cart,true);
            // if cart is alredy having an inputed product
            foreach($decodedCart as $key=>$product){
                if($productId == $product[0]){
                    $channgedProduct = $product;
                    $channgedProduct[5] = $channgedProduct[5]+1;
                    $decodedCart[$key] = $channgedProduct;
                    file_put_contents(__DIR__ . '\..\cache\data\cart.txt',json_encode($decodedCart));
                    return null;
                }
            }

            // if cart doesn't have an inputed product
            $productsWithIds = file_get_contents(__DIR__ . '\..\cache\data\products.txt');
            $productsWithIds = json_decode($productsWithIds,true);
            $products = $productsWithIds[0];
            foreach ($products as $product){
                if($productId == $product[0]){
                    $newProduct = $product;
                    $newProduct[5] = 1;
                    $decodedCart[] = $newProduct;
                    file_put_contents(__DIR__ . '\..\cache\data\cart.txt',json_encode($decodedCart));
                    return null;
                }
            }

        }
    }
    
    // remove product from cart
    function removeProduct($productId){
        $cart = file_get_contents(__DIR__ . '\..\cache\data\cart.txt');
        $decodedCart = json_decode($cart,true);
        $selectedProduct = null;
        $selectedProductId = null;
        foreach($decodedCart as $key=>$product){
            if($productId == $product[0]){
                $selectedProduct = $product;
                $selectedProductId = $key;
            }
        }

        if($selectedProduct and $selectedProduct[5]==1){
            array_splice($decodedCart, $selectedProductId, 1);
            file_put_contents(__DIR__ . '\..\cache\data\cart.txt',json_encode($decodedCart));
        }elseif($selectedProduct and 1<$selectedProduct[5]){
            $selectedProduct[5] = $selectedProduct[5]-1;
            $decodedCart[$selectedProductId] = $selectedProduct;
            file_put_contents(__DIR__ . '\..\cache\data\cart.txt',json_encode($decodedCart));
        }
    }

    // get products in cart of current user
    function getCartProducts($currentUser){
        $returnArray = array("status"=>null, "result"=>null);
        $conn = new DbConnection();
        $queries = new Queries($conn->conn);

        $output = $queries->getCartProducts($currentUser);

        $isGetCartProducts = $output['status'];
        $result = $output['result'];

        if(!$isGetCartProducts){
            $returnArray['status'] = false;
            $returnArray['result'] = null;
            return $returnArray;
        }

        if(empty($result)){
            $returnArray['status'] = true;
            $returnArray['result'] = null;
            return $returnArray;
        }

        $returnArray['status'] = true;
        $returnArray['result'] = $result;
        return $returnArray;
    }
    
    function addNewProductToCart($productId){
        $returnArray = array("status"=>null, "result"=>null);
        $result = $this->getCartProducts($productId);

        if(!$result['result']){
            $returnArray['status'] = false;
            $returnArray['result'] = null;
            return $returnArray;
        }

        // check if send product is already in cart
        
    }
}