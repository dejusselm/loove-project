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
</head>

<body>
    <header style="display:flex">
        <a href="profile"><i class="fa-solid fa-arrow-left" style="font-size:50px"></i></a>
        <h1>Parameters</h1>
    </header>

    <main>
        <section>
            <article>
                <h3>Email and password</h3>

                <?php if (isset($_SESSION['passwordMessage'])): ?>
                    <p><?= $_SESSION['passwordMessage'] ?>
                    </p>
                    <?php unset($_SESSION['passwordMessage']); ?>
                <?php endif; ?>

                <form action="parameters" method="POST">
                    <label>Current password</label>
                    <p><input type="password" id="currentPassword" name="currentPassword" placeholder="Password..."
                            oninput="clearError(this)" required minlength="12"></p>

                    <label>New password</label>
                    <p><input type="password" id="password" name="password" placeholder="Password..."
                            oninput="clearError(this)" required minlength="12"></p>

                    <label>Confirm new password</label>
                    <p><input type="password" id="confirmPassword" name="confirmPassword"
                            placeholder="Confirm password..." oninput="clearError(this); validatePassword('update')">
                    </p>

                    <span id="pswdErrorSpan"></span>
                    <button name="passwordModified" type="submit">Confirm</button>
                </form>
            </article>
            <article>
                <a href="/memberships">
                    <h3>Membership plans</h3>
                </a>

                <?php if (isset($_SESSION['successMessage'])): ?>
                    <p>
                        <?= $_SESSION['successMessage'] ?>
                    </p>
                    <?php unset($_SESSION['successMessage']);
                endif; ?>

                <?php if ($isMember): ?>
                    <div class="membership-status"
                        style="padding: 15px; border: 1px solid #ff4d4d; border-radius: 8px; margin-bottom: 20px;">
                        <p><strong>Status:</strong> Premium Member <i class="fa-solid fa-crown" style="color: gold;"></i>
                        </p>

                        <form action="/parameters" method="POST"
                            onsubmit="return confirm('Are you sure you want to cancel your automatic renewal? You will keep your benefits until the end of the current period.');">
                            <button type="submit" name="cancelSubscription"
                                style="background-color: #666; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer;">
                                Cancel automatic renewal
                            </button>
                        </form>
                    </div>
                <?php endif ?>
                </a>
            </article>

            <article class="profile-views-section"
                style="padding: 15px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 8px;">
                <h3>Profile views</h3>
                <p style="font-size: 1.2em; font-weight: bold; color: #ff4d4d;">
                    <i class="fa-solid fa-eye"></i> <?= $user->getTotalViews() ?> views total
                </p>
                <div class="viewers-list"
                    style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px; position: relative;">

                    <?php if (!empty($viewers)): ?>
                        <?php foreach ($viewers as $viewer): ?>
                            <div class="viewer-item" style="text-align: center; width: 70px;">
                                <?php if ($isMember): ?>
                                    <a href="otherProfile?id=<?= $viewer->getId() ?>&from=parameters">
                                        <img style="width:50px; height: 50px; object-fit: cover; clip-path:circle();"
                                            src="/public/uploads/<?= $viewer->getAvatar() ?>">
                                    </a>
                                    <p
                                        style="margin: 5px 0 0 0; font-size: 0.9em; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                        <?= htmlspecialchars($viewer->getFirstName()) ?>
                                    </p>
                                <?php else: ?>
                                    <div style="cursor: pointer;" onclick="window.location.href='/memberships'">
                                        <img style="width:50px; height: 50px; object-fit: cover; clip-path:circle(); filter: blur(5px); pointer-events: none;"
                                            src="/public/uploads/<?= $viewer->getAvatar() ?>">
                                        <p style="margin: 5px 0 0 0; font-size: 0.9em; color: #999;">••••••</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="font-style: italic; color: #999;">No visits yet. Update your profile to get noticed!</p>
                    <?php endif; ?>

                    <?php if (!$isMember && !empty($viewers)): ?>
                        <div class="premium-overlay"
                            style="width: 100%; text-align: center; padding: 15px 0; background: linear-gradient(transparent, rgba(255,255,255,0.95) 30%); margin-top: 10px;">
                            <p style="margin: 0 0 10px 0; font-weight: bold; color: #333;">
                                <i class="fa-solid fa-crown" style="color: gold;"></i> Someone is interested in you!
                            </p>
                            <a href="/memberships"
                                style="display: inline-block; background-color: #ff4d4d; color: white; padding: 8px 15px; text-decoration: none; border-radius: 20px; font-weight: bold; font-size: 0.9em; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                See who visited your profile
                            </a>
                        </div>
                    <?php endif; ?>

                </div>
            </article>

            <article>
                <a href="/logout">Log out <i class="fa-solid fa-arrow-right-from-bracket"></i></a>
            </article>
            <article>
                <form action="parameters" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete your account ? This action cannot be undone.');">
                    <button type="submit" name="deleteAccount">
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