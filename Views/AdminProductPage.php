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
    <link href="node_modules/select2/dist/css/select2.min.css" rel="stylesheet" />
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

    <br><br>
    <button id="filter-button">filter products</button>
    <br><br>

    <select class="js-example-basic-single" name="state">
        <option value="AL">Alabama</option>
        <option value="WY">Wyoming</option>
        <select value="">
            <option value="WY">one</option>
            <option value="WY">two</option>
            <select value="WY">
                <option value="WY">a</option>
                <option value="WY">b</option>
            </select>
        </select>
    </select>

    <select class="js-example-basic-multiple" name="states[]" multiple="multiple">
        <option value="AL">Alabama</option>
            ...
        <option value="WY">Wyoming</option>
    </select>
    <button class="enable">enable</button>
    <button class="disable">disable</button>
    
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
    <div>
        <form id="uploadForm" enctype="multipart/form-data">
            <input type="file" id="file-input" accept=".csv" />
            <button type="button" id="product-upload">Upload New Products</button>
        </form>
        <p id="result"></p>
    </div>
    
<script src="node_modules/jquery/dist/jquery.min.js"></script>
<script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<!-- Include DataTables JS -->
<script src="node_modules/datatables.net/js/dataTables.min.js"></script>
<!-- Include DataTables Bootstrap 5 Integration JS -->
<script src="node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="node_modules/select2/dist/js/select2.min.js"></script>
<script type = "module" src="http://localhost/public/javascript/admin-product-page-validation.js"></script>
</body>
</html>


