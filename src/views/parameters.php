<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION["userId"])) {
    header('Location: login.php');
    exit;
}

include "../controllers/parametersController.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parameters</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <header style="display:flex">
        <a href="profile.php"><i class="fa-solid fa-arrow-left" style="font-size:50px"></i></a>
        <h1>Parameters</h1>
    </header>

    <main>
        <section>
            <article>
                <h3>Email and password</h3>
            </article>
            <article>
                <h3>App theme</h3>
            </article>
            <article>

                <form action="../controllers/parametersController.php" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete your account ? This action cannot be undone.');">
                    <button type="submit" name="deleteAccount">
                        <i class="fa-solid fa-trash"></i> Delete account ?
                    </button>
                </form>
            </article>
        </section>
    </main>
</body>

</html>