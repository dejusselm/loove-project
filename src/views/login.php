<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "../controllers/loginController.php";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style/preLogin.css" rel="stylesheet">
    <title>Login</title>
</head>

<body>
    <header>
        <a href="./login.php"><img src="images/logoLoovePink.png" alt="Logo Loove" class="logo"></a>
    </header>
    <form action="login.php" method="POST">
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
            <input type="email" name="email" placeholder="Enter email" required>

            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Enter password" required>
            <button type="submit" name="submitted">Login</button>
        </div>

    </form>
    <span><a href="register.php">Register page</a></span>
</body>

</html>