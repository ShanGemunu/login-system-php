<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Log in</title>
        <!-- Include Bootstrap CSS -->
        <link href="public/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="p-3 m-0 border-0 m-0 border-0">
    
    <div class="d-flex align-items-center justify-content-center">
        <form action="/login" method="post" class="w-25 border p-3 rounded">
            <h4 class="">Login here</h4><br>
            <div class="mb-3">
                <label for="" class="form-label">Email</label>
                <input type="text" name="email" id="input-email" class="form-control">  
                <h6 id="email-check"></h6>
            </div>
            <div>
                <label for="" class="form-label">Password</label>
                <input class="form-control" type="password" name="password" id="input-password">
                <h6 id="password-check"></h6>
            </div><br>
            <div class="d-flex flex-column align-items-end">
                <button type="submit" id="login-button" name="login_button" class="btn btn-primary w-50 fw-semibold">Log in</button>
            </div>
        </form>
    </div>
    <div class="d-flex align-items-center justify-content-center mt-1">
            <p class="pt-3 fw-semibold">New to Site, Register here.</p>
            <a href="/register" class="btn btn-primary ms-2 fw-semibold">Register here</a>
    </div>
    <div class="d-flex align-items-center justify-content-center mt-4 text-danger">
        <p>
            <?php if(isset($_SESSION['loginPageStatus'])) echo $_SESSION['loginPageStatus'] ?>
        </p>
    </div>
    <img src="http://localhost/public/assets/images/iphone 13.jpg" alt="">
    <!-- Include Bootstrap JS -->
    <script src="public/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script type = "text/javascript" src="http://localhost/public/jquery/jquery-3.7.1.min.js"></script>
    <script type = "text/javascript" src="http://localhost/public/javascript/input-validation.js"></script>
    </body>
</html>
  





