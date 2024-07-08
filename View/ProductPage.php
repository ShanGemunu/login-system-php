<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="http://localhost/public/assets/css/products.css">
    <title>Document</title>
</head>
<body>
<ul class="nav-bar">
  <li><a href="/users">Users</a></li>
  <li><a href="/products">Products</a></li>
  <li><a href="/cart">Shopping Cart</a></li>
  <li><a href="/homepage">Homepage</a></li>
  <li><h6>Hello User</h6></li>
  <li style="float:right"><a class="active" href="#about">Logout</a></li>
</ul>
<ul class="ul">
  <?php
    $products = file_get_contents(__DIR__ . '\..\cache\data\products.txt');
    $products = json_decode($products,true);
    foreach ($products[0] as $product) {
      echo "<div class='product-list'>
                <li>
                    <div class='card'>
                        <img src='http://localhost/public/assets/images/$product[3]' alt=$product[1]>
                    
                        <div class='container'>
                            <h5><b>$product[1]</b></h5> 
                            <p>Rs. $product[2]</p> 
                        </div>
                        <form action='/cart' method='post'>
                          <input hidden type='text' name='add' value='add'>
                          <input hidden type='text' name='product-id' value='$product[0]'>
                          <button>add to cart</button>
                        </form>
                    </div>
                </li>
            </div>
            ";
    }
  ?>
</ul> 
</body>
</html>