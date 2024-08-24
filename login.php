<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/login.css">
    <title>BaGoTours || Signin & Signup</title>
</head>
<body>
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                <form id="sign-in-form" action="php/login.php" class="sign-in-form" method="POST">
                    <h2 class="title">Sign in</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" id="signin-username" name="username" placeholder="Username">
                    </div>
                    <small class="error-message" id="signin-username-error"></small>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="signin-password" name="password" placeholder="Password">
                    </div>
                    <small class="error-message" id="signin-password-error"></small>
                    <input type="submit" value="Login" class="btn solid">
                </form>

                <form id="sign-up-form" action="php/register.php" class="sign-up-form" method="POST">
                    <h2 class="title">Sign up</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" id="signup-username" name="username" placeholder="Username">
                    </div>
                    <small class="error-message" id="signup-username-error"></small>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="signup-email" name="email" placeholder="Email">
                    </div>
                    <small class="error-message" id="signup-email-error"></small>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="signup-password" name="password" placeholder="Password">
                    </div>
                    <small class="error-message" id="signup-password-error"></small>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="signup-confirm-password" name="confirm-password" placeholder="Confirm Password">
                    </div>
                    <small class="error-message" id="signup-confirm-password-error"></small>
                    <input type="submit" class="btn" value="Sign up">
                </form>
            </div>
        </div>

        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <h3>New here?</h3>
                    <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Debitis, ex ratione. Aliquid!</p>
                    <button class="btn transparent" id="sign-up-btn">Sign up</button>
                </div>
                <img src="img/log.svg" class="image" alt="">
            </div>
            <div class="panel right-panel">
                <div class="content">
                    <h3>One of us?</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nostrum laboriosam ad deleniti.</p>
                    <button class="btn transparent" id="sign-in-btn">Sign in</button>
                </div>
                <img src="img/register.svg" class="image" alt="">
            </div>
        </div>
    </div>
    <script src="assets/js/app.js"></script>
</body>
</html>
