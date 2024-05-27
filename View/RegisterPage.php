<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>
<!-- Include Bootstrap CSS -->
<link href="../vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3 m-0 border-0 m-0 border-0">
<div class="d-flex align-items-center justify-content-center">
    <form method="post" action="/login-system-php/register" class="w-25 border p-3 rounded">
        <h4>Register here</h4>
        <div class="mb-3">
            <label for="" class="form-label">User Name</label>
            <input class="form-control" type="text" name="user_name" id="">
        </div>
        <div class="mb-3">
            <label class="form-label" for="">Email</label>
            <input class="form-control" type="email" name="email" id="">
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Password</label>
            <input class="form-control" type="password" name="password" id="">  
        </div><br>
        <div class="d-flex flex-column align-items-end">
            <button type="submit" name="register_button" class="btn btn-primary w-50 fw-semibold">Register</button>
        </div>
    </form>
</div>
<div class="d-flex align-items-center justify-content-center mt-1">
    <p class="pt-3 fw-semibold">Already a user, Login here.</p>
    <a href="/login-system-php/login" class="btn btn-primary ms-2 fw-semibold">Login here</a>
</div>
<div class="d-flex align-items-center justify-content-center mt-4 text-danger">
    <p>
        <?php if(isset($_SESSION['registerPageStatus'])) echo $_SESSION['registerPageStatus'] ?>
    </p>
</div>

<!-- Include Bootstrap JS -->
<script src="../vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
   



