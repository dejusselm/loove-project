<?php
include ROOT_PATH . 'controllers/AuthController.php';
$authController = new AuthController();
$authController->login();

include ROOT_PATH . 'views/components/head.php';
?>
<link href="/views/style/preLogin.css" rel="stylesheet">
<title>Login</title>
</head>

<body>
    <header>
        <a href="/preLogin/home"><img src="/views/images/logoLoovePink.png" alt="Logo Loove" class="logo"></a>
    </header>
    <main>
        <section>
            <h1>Login</h1>

            <?php if (isset($_SESSION['errorMessage'])): ?>
                <div class="error-box">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>
                        <?php
                        echo $_SESSION['errorMessage'];
                        unset($_SESSION['errorMessage']);
                        ?>
                    </span>
                </div>
            <?php endif; ?>

            <form action="/preLogin/login" method="POST">
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email..." required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password..." required>
                </div>

                <button type="submit" class="btn-primary" style="margin-top: 1em;">Login</button>
            </form>

            <p class="footer-link">Don't have an account yet? <a href="/preLogin/register">Register</a></p>
        </section>
    </main>
</body>

</html>