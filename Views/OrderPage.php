<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="http://localhost/public/assets/css/main.css">
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <title>Document</title>
</head>
<body>
    <ul class="nav-bar">
        <li><a href="/products">Products</a></li>
        <li><a href="/cart">Shopping Cart</a></li>
        <li><h6>Hello User</h6></li>
        <div>
            <li><a class="active" href="/login">Login</a></li>
            <li><a class="active" href="/register">Register</a></link>
        </div>
    </ul>
    <div id="status-div"><p id="status-p"></p></div>
    <table id="order-table" class="order-table">
        <thead>
            <tr>
              
            </tr>
        </thead>
        <tbody>
        
        </tbody>
    </table>
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include DataTables JS -->
    <script src="node_modules/datatables.net/js/dataTables.min.js"></script>
    <!-- Include DataTables Bootstrap 5 Integration JS -->
    <script src="node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script type = "module" src="http://localhost/public/javascript/order-page.js"></script>
</body>
</html>
