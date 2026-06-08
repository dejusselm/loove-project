<?php
require 'database/db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style/style.css" rel="stylesheet">
    <title>Index Page</title>
    <link href="views/style/preLogin.css" rel="stylesheet">
</head>

<body>

    <main>
        <img src="views/images/logoLoovePink.png" class="logo">
        <section>
            <article>
                <h3>Welcome back</h3>
                <button onclick="location.href = 'views/login.php';"">Log in</button>
            </article>
        <article>    
            <h3>No account ? </h3>
            <button onclick=" location.href='views/register.php' ;"">Sign in</button>
            </article>
        </section>
    </main>
</body>

</html>