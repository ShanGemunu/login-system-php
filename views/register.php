<div class="d-flex align-items-center justify-content-center">
    <form method="post" action="/register" class="w-25 border p-3 rounded">
        <h4>Register here</h4>
        <div class="mb-3">
            <label for="" class="form-label">User Name</label>
            <input class="form-control" type="text" name="username" id="input-username">
            <h6 id="username-check">
                <?php if (isset($username)) {
                    echo $username[0];
                } ?>
            </h6>
        </div>
        <div class="mb-3">
            <label class="form-label" for="">Email</label>
            <input class="form-control" type="email" name="email" id="input-email">
            <h6 id="email-check">
                <?php if (isset($email)) {
                    echo $email[0];
                } ?>
            </h6>
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Password</label>
            <input class="form-control" type="password" name="password" id="input-password">
            <h6 id="password-check">
                <?php if (isset($password)) {
                    echo $password[0];
                } ?>
            </h6>
        </div><br>
        <div class="mb-3">
            <label for="" class="form-label">Confirm Password</label>
            <input class="form-control" type="password" name="confirm-password" id="input-password">
            <h6 id="password-check">
                <?php if (isset($confirm_password)) {
                    echo $confirm_password[0];
                } ?>
            </h6>
        </div><br>
        <div class="d-flex flex-column align-items-end">
            <button type="submit" name="register_button" id="register-button"
                class="btn btn-primary w-50 fw-semibold">Register</button>
        </div>
    </form>
</div>
<div class="d-flex align-items-center justify-content-center mt-1">
    <p class="pt-3 fw-semibold">Already a user, Login here.</p>
    <a href="/login" class="btn btn-primary ms-2 fw-semibold">Login here</a>
</div>
<script type="text/javascript" src="http://localhost/public/javascript/input-validation.js"></script>