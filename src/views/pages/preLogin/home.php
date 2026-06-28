<?php
include ROOT_PATH . 'views/components/head.php'
    ?>
<link href="/views/style/style.css" rel="stylesheet">
<title>Loove App</title>
<link href="/views/style/preLogin.css" rel="stylesheet">
</head>

<body>

    <main>
        <img src="/views/images/logoLoovePink.png" class="logo">
        <section id="index">
            <article>
                <h3>Welcome back</h3>
                <button onclick="location.href = '/preLogin/login';">Log in</button>
            </article>
            <article>
                <h3>No account ? </h3>
                <button onclick=" location.href='/preLogin/register' ;">Sign up</button>
            </article>
        </section>
    </main>
</body>

</html>