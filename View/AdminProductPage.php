<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="http://localhost/public/assets/css/products.css">
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <title>Document</title>
</head>
<body>
    <ul class="nav-bar">
        <li><a href="/homepage">Homepage</a></li>
        <li><a href="/users">Users</a></li>
        <li><a href="/products">Products</a></li>
        <li><a href="/cart">Shopping Cart</a></li>
        <li><a href="/orders">Orders</a></li>
    </ul>
    <button id="update-button">update products</button>
    <table id="product-table" class="display">
        <thead>
            <tr>
              
            </tr>
        </thead>
        <tbody>
        
        </tbody>
    </table>
    <div class="custom">
        <table id="added-product-table" class="display">
            <thead>
                <tr>
                
                </tr>
            </thead>
            <tbody>
            
            </tbody>
        </table>
    </div>   
<script src="node_modules/jquery/dist/jquery.min.js"></script>
<script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<!-- Include DataTables JS -->
<script src="node_modules/datatables.net/js/dataTables.min.js"></script>
<!-- Include DataTables Bootstrap 5 Integration JS -->
<script src="node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script type = "module" src="http://localhost/public/javascript/admin-product-page-validation.js"></script>
</body>
</html>


