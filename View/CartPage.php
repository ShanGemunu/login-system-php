<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="http://localhost/public/assets/css/cart.css">
    <title>Document</title>
</head>
<body>
<ul class="nav-bar">
  <li><a href="/users">Users</a></li>
  <li><a href="/products">Products</a></li>
  <li><a href="/cart">Shopping Cart</a></li>
  <li><a href="/homepage">Homepage</a></li>
  <li><h6>Hello User</h6></li>
  <div>
    <li><a class="active" href="#about">Orders</a></li>
    <li><a class="active" href="#about">Logout</a></li>
  </div>
</ul>
<ul class="ul">
  <?php
    if(isset($_SESSION['cartStatus'])) echo $_SESSION['cartStatus'];
    $cartEncoded = file_get_contents(__DIR__ . '\..\cache\data\cart.txt');
    $cart = json_decode($cartEncoded, true);
    $totalPrice = 0;
    if(2<strlen($cartEncoded)){
        foreach ($cart as $product) {
            $totalPrice += $product[2]*$product[5];
            echo "<div class='product-list'>
                      <li>
                          <div class='card'>
                              <img src='http://localhost/public/assets/images/$product[3]' alt=$product[1]>
                              <div class='container'>
                                  <h5><b>$product[1]</b></h5> 
                                  <p>Rs. $product[2]</p> 
                              </div>
                              <div class='quantity-buttons'>
                                  <form action='/cart' method='post'>
                                      <input type='hidden' name='remove' value='remove'>
                                      <input type='hidden' name='product-id' value='$product[0]'>
                                      <button>-</button>
                                  </form>
                                  <h6>$product[5]</h6>
                                  <form action='/cart' method='post'>
                                      <input type='hidden' name='add' value='add'>
                                      <input type='hidden' name='product-id' value='$product[0]'>
                                      <button>+</button>
                                  </form>
                              </div>
                          </div>
                      </li>
                  </div>
                  ";
          }
          echo "Total Price - Rs .$totalPrice <br>";
          echo "
            <form action='/order' method='post'>
                <button>make order</button>
            </form>
          ";
    }else{
        echo "No products.";
    }
    
    

  ?>
</ul>

</body>
</html>