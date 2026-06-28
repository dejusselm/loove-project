<?php
require_once ROOT_PATH . 'controllers/UserController.php';
$controller = new UserController();

include ROOT_PATH . 'views/components/head.php'
    ?>

<title>Membership</title>
<link href="/views/style/membership.css" rel="stylesheet">
</head>

<body>
    <header>
        <a href="/user/parameters"><i class="fa-solid fa-arrow-left"></i></a>
        <h1>Membership</h1>
    </header>
    <main>
        <section class="membership-container">

            <?php if (isset($_SESSION['flashMessage'])): ?>
                <div class="flash-modal">
                    <div>
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span><?= htmlspecialchars($_SESSION['flashMessage']) ?></span>
                    </div>
                    <button class="close-flash" onclick="this.closest('.flash-modal').remove()">&times;</button>
                </div>
                <?php unset($_SESSION['flashMessage']); ?>
            <?php endif; ?>

            <article class="membership-card free-card">
                <div class="card-header">
                    <h3>Free plan</h3>
                    <p class="price">0€<span>/month</span></p>
                </div>
                <ul class="features-list">
                    <li><i class="fa-solid fa-check text-pink"></i> Discover new profiles daily</li>
                    <li><i class="fa-solid fa-check text-pink"></i> Send and receive basic messages</li>
                    <li><i class="fa-solid fa-xmark text-muted"></i> Hidden profile visitors</li>
                    <li><i class="fa-solid fa-xmark text-muted"></i> Limited search filters</li>
                </ul>
                <?php if (!$controller->user->isMember()): ?>
                    <div class="btn btn-current">Active Plan</div>
                <?php endif ?>
            </article>

            <article class="membership-card premium-card">
                <div class="card-header">
                    <span class="badge">Most Popular</span>
                    <h3>Premium plan</h3>
                    <p class="price">19,99€<span>/month</span></p>
                </div>
                <ul class="features-list">
                    <li><i class="fa-solid fa-bolt text-gold"></i> See instantly who visited your profile</li>
                    <li><i class="fa-solid fa-bolt text-gold"></i> Unlock all advanced search filters</li>
                    <li><i class="fa-solid fa-bolt text-gold"></i> Message anyone</li>
                    <li><i class="fa-solid fa-bolt text-gold"></i> Priority for your profile</li>
                </ul>
                <?php if (!$controller->user->isMember()): ?>
                    <a href="/memberships/subscribe" class="btn btn-premium">Upgrade to Premium</a>
                <?php else: ?>
                    <div class="btn btn-active-premium">Current plan <i class="fa-check fa-solid"></i></div>
                <?php endif ?>
            </article>

            <?php if ($controller->user->isMember()): ?>
                <p class="cancel-notice">
                    To cancel your subscription renewal or manage your billing history, you can updates your account
                    preferences directly from your profile settings section.
                </p>
            <?php endif ?>
        </section>
    </main>
    <script src="/views/scripts/notificationsScript.js"></script>
</body>

</html>