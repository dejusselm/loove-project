<?php
require_once ROOT_PATH . 'controllers/AuthController.php';

$authController = new AuthController();

$authController->verifyCode();

include ROOT_PATH . 'views/components/head.php';
?>
<link href="/views/style/preLogin.css" rel="stylesheet">
<title>Verify your Email</title>
</head>

<body>
    <header>
        <a href="/preLogin/home"><img class="logo" src="/views/images/logoLoovePink.png"></a>
    </header>
    <main>
        <section style="max-width: 400px; margin: 40px auto; padding: 20px; text-align: center;">
            <h1>Email Verification</h1>
            <p>We sent a 6-digit verification code to your email address. Please enter it below.</p>

            <?php if (isset($_SESSION['errorMessage'])): ?>
                <div style="color:red; margin-bottom: 15px;">
                    <?= $_SESSION['errorMessage'];
                    unset($_SESSION['errorMessage']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <p>
                    <input type="text" name="verification_code" placeholder="123456" required pattern="[0-9]{6}"
                        maxlength="6" inputmode="numeric"
                        style="font-size: 24px; text-align: center; letter-spacing: 5px; width: 80%; padding: 10px;">
                </p>
                <button type="submit" name="submitVerification"
                    style="width: 80%; padding: 10px; background-color: #ea638c; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                    Verify Code
                </button>
            </form>
            <p style="margin-top: 15px;">
                Didn't receive it? <a href="verifyEmail?action=resend"
                    style="color: #ea638c; text-decoration: none; font-weight: bold;">Resend code</a>
            </p>

            <p style="margin-top: 20px;"><a href="/preLogin/register">Back to register</a></p>
        </section>
    </main>
</body>

</html>