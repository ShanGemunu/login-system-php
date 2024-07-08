<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="http://localhost/public/assets/css/users.css">
    <title>Document</title>
</head>
<body>
<ul class="nav-bar">
  <li><a href="/users">Users</a></li>
  <li><a href="/products">Products</a></li>
  <li><h6>Hello User</h6></li>
  <div class="right-div-nav">
    <li><a href="/cart">Shopping Cart</a></li>
    <li><a href="/homepage">Homepage</a></li>
    <li><a class="active" href="#about">Logout</a></li>
  </div>
</ul>
<ul class="ul">
  <?php
    $users = array (
        array('user01','hello@gmail.com','mark','http://localhost/public/assets/images/user.jpeg'),
        array('user01','hello@gmail.com','mark','http://localhost/public/assets/images/user.jpeg'),
        array('user01','hello@gmail.com','mark','http://localhost/public/assets/images/user.jpeg'),
        array('user01','hello@gmail.com','mark','http://localhost/public/assets/images/user.jpeg'),
      );
    foreach ($users as $user) {
      echo "<div class='product-list'>
                <li>
                    <div class='card'>
                        <img src=$user[3] alt=$user[0]>
                        <div class='container'>
                            <h5><b>$user[0]</b></h5> 
                        </div>
                    </div>
                </li>
            </div>
            ";
    }
  ?>
</ul>

</body>
</html>