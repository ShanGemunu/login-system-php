<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="http://localhost/public/assets/css/order.css">
    <link rel="stylesheet" href="http://localhost/public/assets/css/homepage.css">
    <title>Document</title>
</head>
<body>
    <ul class="nav-bar">
        <li><a href="/users">Users</a></li>
        <li><a href="/products">Products</a></li>
        <li><a href="/cart">Shopping Cart</a></li>
        <li><h6>Hello User</h6></li>
        <div>
            <li><a class="active" href="/login">Login</a></li>
            <li><a class="active" href="/register">Register</a></link>
        </div>
    </ul>
    <?php
        if($_SESSION['currentUserOrders']){
            foreach($_SESSION['currentUserOrders'] as $order){
            
                $orderDate = $order[0][1];
                $orderStatus = $order[0][6];
                $paymentMethod = $order[0][5];
                echo "
                <ul class='header-li'>
                    <p>$orderDate</p>
                    <p>$orderStatus</p>
                    <p>$paymentMethod</p>
                </ul>
                ";
                    
                foreach($order as $product){
                    $productName = $product[2];
                    $productPrice = $product[3];
                    $productQuantity = $product[4];
                    echo "
                    <ul class='product-li'>
                        <li>$productName</li>
                        <li>$productPrice</li>
                        <li>$productQuantity</li>
                    </ul>
                    ";
                }
                echo "<br>";
    
            }
        }else{
            echo "No orders.";
        }
        
    ?>

    
</body>
</html>
