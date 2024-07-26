<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Include Bootstrap CSS -->
    <link href="public/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <script type = "text/javascript" src="http://localhost/public/jquery/jquery-3.7.1.min.js"></script>
    <script type = "text/javascript" src="http://localhost/public/javascript/input-validation.js"></script>
</head>
<body class="p-3 m-0 border-0 m-0 border-0">
<div class="d-flex align-items-center justify-content-center">
    <form method="post" action="/register" class="w-25 border p-3 rounded">
        <h4>Register here</h4>
        <div class="mb-3">
            <label for="" class="form-label">User Name</label>
            <input class="form-control" type="text" name="user_name" id="input-username">
            <h6 id="username-check"></h6>
        </div>
        <div class="mb-3">
            <label class="form-label" for="">Email</label>
            <input class="form-control" type="email" name="email" id="input-email">
            <h6 id="email-check"></h6>
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Password</label>
            <input class="form-control" type="password" name="password" id="input-password"> 
            <h6 id="password-check"></h6> 
        </div><br>
        <div class="d-flex flex-column align-items-end">
            <button type="submit" name="register_button" id="register-button" class="btn btn-primary w-50 fw-semibold">Register</button>
        </div>
    </form>
</div>
<div class="d-flex align-items-center justify-content-center mt-1">
    <p class="pt-3 fw-semibold">Already a user, Login here.</p>
    <a href="/login" class="btn btn-primary ms-2 fw-semibold">Login here</a>
</div>
<div class="d-flex align-items-center justify-content-center mt-4 text-danger">
    <p>
        <?php if(isset($_SESSION['registerPageStatus'])) echo $_SESSION['registerPageStatus'] ?>
    </p>
</div>

<!-- Include Bootstrap JS -->
<script src="public/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
   


