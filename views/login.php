<div class="d-flex align-items-center justify-content-center">
    <form action="/login" method="post" class="w-25 border p-3 rounded">
        <h4 class="">Login here</h4><br>
        <div class="mb-3">
            <label for="" class="form-label">Email</label>
            <input type="text" name="email" id="input-email" class="form-control">
            <h6 id="email-check">
                <?php if(isset($email)){
                    echo $email[0];
                }?>
            </h6>
        </div>
        <div>
            <label for="" class="form-label">Password</label>
            <input class="form-control" type="password" name="password" id="input-password">
            <h6 id="password-check">
                <?php if(isset($password)){
                    echo $password[0];
                }?>
            </h6>
        </div><br>
        <div class="d-flex flex-column align-items-end">
            <button type="submit" id="login-button" class="btn btn-primary w-50 fw-semibold">Log
                in</button>
        </div>
    </form>
</div>
<div class="d-flex align-items-center justify-content-center mt-1">
    <p class="pt-3 fw-semibold">New to Site, Register here.</p>
    <a href="/register" class="btn btn-primary ms-2 fw-semibold">Register here</a>
</div>
<div class="d-flex align-items-center justify-content-center mt-4 text-danger">
</div>

<script type="text/javascript" src="http://localhost/javascript/input-validation.js"></script>