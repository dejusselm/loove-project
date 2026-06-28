<?php
require_once ROOT_PATH . 'controllers/UserController.php';
$controller = new UserController();

include ROOT_PATH . 'views/components/head.php'
    ?>

<title>Membership</title>
</head>

<body>
    <header>
        <a href="/user/parameters"><i class="fa-solid fa-arrow-left"></i></a>
        <h1>Membership</h1>
    </header>
    <main>
        <section>
            <article>
                <?php if (isset($_SESSION['flashMessage'])) {
                    echo '<h2>' . $_SESSION['flashMessage'] . '</h2>';
                    unset($_SESSION['flashMessage']);
                }
                ?>
            </article>
            <article class="freecard">
                <h3>Free offer</h3>
                <p>Basic features</p>
            </article>

            <article class="premium-card">
                <h3>Premium offer</h3>
                <p>Gain access to every search filters, see anyone who visits your profile...</p>
                <?php if (!$controller->user->isMember()): ?>
                    <?= '<a href="/memberships/subscribe" class="btn btn-premium">Devenir Premium</a>' ?>
                <?php else: ?>
                    <?= '<div class="btn">Current plan<i class="fa-check fa-solid"></i></div>' ?>
                <?php endif ?>
            </article>
            <?php if ($controller->user->isMember()): ?>
                To cancel your Subscription renewal, please go in your parameters' membership section.
            <?php endif ?>
        </section>
    </main>
    <script src="/views/scripts/notificationsScript.js"></script>
</body>

</html>