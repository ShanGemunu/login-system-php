<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Log in</title>
        <!-- Include Bootstrap CSS -->
        <link href="../vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="login.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </head>
    <body class="p-3 m-0 border-0 m-0 border-0">
    
    <div class="d-flex align-items-center justify-content-center">
        <form action="/login-system-php/login" method="post" class="w-25 border p-3 rounded">
            <h4 class="">Login here</h4><br>
            <div class="mb-3">
                <label for="" class="form-label">Email</label>
                <input type="text" name="email" id="" class="form-control">  
            </div>
            <div>
                <label for="" class="form-label">Password</label>
                <input class="form-control" type="password" name="password" id="">
            </div><br>
            <div class="d-flex flex-column align-items-end">
                <button type="submit" name="login_button" class="btn btn-primary w-50 fw-semibold">Log in</button>
            </div>
        </form>
    </div>
    <div class="d-flex align-items-center justify-content-center mt-1">
            <p class="pt-3 fw-semibold">New to Site, Register here.</p>
            <a href="/login-system-php/register" class="btn btn-primary ms-2 fw-semibold">Register here</a>
    </div>
    <div class="d-flex align-items-center justify-content-center mt-4 text-danger">
        <p>
            <?php if(isset($_SESSION['loginPageStatus'])) echo $_SESSION['loginPageStatus'] ?>
        </p>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="../vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
  





