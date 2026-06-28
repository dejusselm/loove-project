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
        <a href="/preLogin/home"><img class="logo" src="/views/images/logoLoovePink.png" alt="Logo Loove"></a>
    </header>
    <main>
        <section style="text-align: center; padding-top: 1em;">
            <h1>Email Verification</h1>
            <p class="verification-text">We sent a 6-digit verification code to your email address. Please enter it
                below.</p>

            <?php if (isset($_SESSION['errorMessage'])): ?>
                <div class="error-box">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>
                        <?= $_SESSION['errorMessage'];
                        unset($_SESSION['errorMessage']); ?>
                    </span>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <input type="text" name="verification_code" placeholder="000000" required pattern="[0-9]{6}"
                        maxlength="6" inputmode="numeric" class="code-input">
                </div>

                <button type="submit" name="submitVerification" class="btn-primary" style="margin-top: 1em;">
                    Verify Code
                </button>
            </form>

            <p class="footer-link" style="margin-top: 2em;">
                Didn't receive it? <a href="verifyEmail?action=resend">Resend code</a>
            </p>

            <p class="footer-link" style="margin-top: 1em;">
                <a href="/preLogin/home" style="color: var(--gray-muted); font-weight: normal;">Back to home</a>
            </p>
        </section>
    </main>
</body>

</html>