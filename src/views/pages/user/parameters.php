<?php
include ROOT_PATH . 'controllers/UserController.php';
include ROOT_PATH . 'controllers/MembershipController.php';
require_once ROOT_PATH . 'controllers/AuthController.php';

$authController = new AuthController();
$userController = new UserController();
$memberController = new MembershipController();

extract($userController->profileData());

$isMember = $user->isMember();
$viewers = $user->getProfileViewers();

if (isset($_POST['cancelSubscription'])) {
    $memberController->cancelSubscriptionRenewal();
}

include ROOT_PATH . 'views/components/head.php';
?>
<title>Parameters</title>
<link href="/views/style/parameters.css" rel="stylesheet">
</head>

<body>
    <header class="header-parameters">
        <a href="profile" class="back-link"><i class="fa-solid fa-arrow-left"></i></a>
        <h1>Parameters</h1>
    </header>

    <main class="main-parameters">
        <section class="settings-section">

            <article class="settings-card">
                <h3>Email and password</h3>

                <?php if (isset($_SESSION['passwordMessage'])): ?>
                    <p class="message-alert"><?= $_SESSION['passwordMessage'] ?></p>
                    <?php unset($_SESSION['passwordMessage']); ?>
                <?php endif; ?>

                <form action="parameters" method="POST" class="settings-form">
                    <div class="input-group">
                        <label for="currentPassword">Current password</label>
                        <input type="password" id="currentPassword" name="currentPassword" placeholder="Password..."
                            oninput="clearError(this)" required minlength="12">
                    </div>

                    <div class="input-group">
                        <label for="password">New password</label>
                        <input type="password" id="password" name="password" placeholder="Password..."
                            oninput="clearError(this)" required minlength="12">
                    </div>

                    <div class="input-group">
                        <label for="confirmPassword">Confirm new password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword"
                            placeholder="Confirm password..." oninput="clearError(this); validatePassword('update')">
                    </div>

                    <span id="pswdErrorSpan" class="error-text"></span>
                    <button name="passwordModified" type="submit" class="btn-primary">Confirm</button>
                </form>
            </article>

            <article class="settings-card">
                <a href="/memberships" class="card-title-link">
                    <h3>Membership plans</h3>
                </a>

                <?php if (isset($_SESSION['successMessage'])): ?>
                    <p class="message-success"><?= $_SESSION['successMessage'] ?></p>
                    <?php unset($_SESSION['successMessage']); ?>
                <?php endif; ?>

                <?php if ($isMember): ?>
                    <div class="membership-status">
                        <p><strong>Status:</strong> Premium Member <i class="fa-solid fa-crown icon-crown"></i></p>

                        <form action="/parameters" method="POST"
                            onsubmit="return confirm('Are you sure you want to cancel your automatic renewal? You will keep your benefits until the end of the current period.');">
                            <button type="submit" name="cancelSubscription" class="btn-secondary">
                                Cancel automatic renewal
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </article>

            <article class="settings-card profile-views-section">
                <h3>Profile views</h3>
                <p class="total-views-counter">
                    <i class="fa-solid fa-eye"></i> <?= $user->getTotalViews() ?> views total
                </p>

                <div class="viewers-list">
                    <?php if (!empty($viewers)): ?>
                        <?php foreach ($viewers as $viewer): ?>
                            <div class="viewer-item">
                                <?php if ($isMember): ?>
                                    <a href="otherProfile?id=<?= $viewer->getId() ?>&from=parameters" class="viewer-avatar-link">
                                        <img class="viewer-avatar" src="/public/uploads/<?= $viewer->getAvatar() ?>" alt="Avatar">
                                    </a>
                                    <p class="viewer-name"><?= htmlspecialchars($viewer->getFirstName()) ?></p>
                                <?php else: ?>
                                    <div class="viewer-blurred" onclick="window.location.href='/memberships'">
                                        <img class="viewer-avatar blurred-img" src="/public/uploads/<?= $viewer->getAvatar() ?>"
                                            alt="Avatar">
                                        <p class="viewer-name anonymized">••••••</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-data-text">No visits yet. Update your profile to get noticed!</p>
                    <?php endif; ?>

                    <?php if (!$isMember && !empty($viewers)): ?>
                        <div class="premium-overlay">
                            <p class="overlay-title">
                                <i class="fa-solid fa-crown icon-crown"></i> Someone is interested in you!
                            </p>
                            <a href="/memberships" class="btn-premium">
                                See who visited your profile
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </article>

            <article class="settings-card actions-area">
                <a href="/logout" class="btn-logout">
                    Log out <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </a>

                <form action="parameters" method="POST" class="delete-account-form"
                    onsubmit="return confirm('Are you sure you want to delete your account ? This action cannot be undone.');">
                    <button type="submit" name="deleteAccount" class="btn-delete">
                        <i class="fa-solid fa-trash"></i> Delete account ?
                    </button>
                </form>
            </article>

        </section>
    </main>
    <script src="/views/scripts/notificationsScript.js"></script>
    <script src="/views/scripts/script.js"></script>
</body>

</html>