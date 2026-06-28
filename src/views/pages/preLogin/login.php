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
            <form action="/preLogin/login" method="POST">
                <h1>Login :</h1>
                <div class="container">
                    <?php if (isset($_SESSION['errorMessage'])): ?>
                        <div style="color: red;">
                            <?php
                            echo $_SESSION['errorMessage'];
                            unset($_SESSION['errorMessage']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <label for="email">Email</label>
                    <input type="email" name="email" placeholder="Email..." required>

                    <label for="password">Password</label>
                    <input type="password" name="password" placeholder="Password..." required>
                    <button type="submit">Login</button>
                </div>


            </form>
        </section>
    </main>
    <span><a href="/preLogin/register">Register page</a></span>
</body>

</html>