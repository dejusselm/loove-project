<?php
include ROOT_PATH . '/controllers/DashboardController.php';
include ROOT_PATH . '/controllers/UserController.php';
include ROOT_PATH . '/controllers/MatchController.php';
include ROOT_PATH . '/controllers/ChatController.php';

$dashboardController = new DashboardController();
$dashboardController->index();

$userController = new UserController();
$matchController = new MatchController();
$chatController = new ChatController();

if (isset($_GET['action']) && isset($_GET['profile_id']) && ($_GET['action'] === 'like' || $_GET['action'] === 'dislike')) {
    $matchController->handleInteraction();
}

$userController->registerReport();

extract($userController->profileData());
$currentProfile = !empty($profiles) ? $profiles[0] : null;

$chatController->markAsDelivered();

include ROOT_PATH . 'views/components/head.php';
?>
<title>Home</title>
<link href="/views/style/dashboard.css" rel="stylesheet">
</head>

<body>
    <header class="dashboard-header">
        <h2>Discover</h2>
    </header>

    <?php if (isset($_SESSION['flashMessage'])): ?>
        <div class="flash-modal">
            <div class="modal-match-content">
                <p><?= htmlspecialchars($_SESSION['flashMessage']) ?></p>
                <?php unset($_SESSION['flashMessage']); ?>
                <button type="button" class="btn-close-flash" onclick="closeFlashModal()">Awesome !</button>
            </div>
        </div>
    <?php endif; ?>

    <main class="dashboard-main">
        <?php if ($currentProfile): ?>

            <article class="discover-profile-card"
                style="background-image: linear-gradient(180deg, rgba(27, 32, 33, 0) 50%, rgba(234, 99, 140, 0.51) 60%, rgba(137, 2, 62, 1) 100%), url('../public/uploads/<?= htmlspecialchars($currentProfile->getAvatar()); ?>');">

                <div class="card-top-actions">
                    <i class="fa-solid fa-triangle-exclamation btn-trigger-report"
                        onclick="openReportModal(<?= $currentProfile->getId() ?>)"></i>
                </div>

                <div class="card-blur-space"></div>

                <div class="card-content">
                    <h4><?= htmlspecialchars($currentProfile->getFirstName()) . ', ' . $currentProfile->getAge() . ' yo' ?>
                    </h4>

                    <p class="card-desc">
                        <i class="fa-solid fa-quote-left"></i>
                        <?= htmlspecialchars($currentProfile->getDescription() ?? "No description") ?>
                        <i class="fa-solid fa-quote-right"></i>
                    </p>

                    <?php if ($currentProfile->getCity()): ?>
                        <p class="card-location">
                            <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($currentProfile->getCity()); ?>
                        </p>
                    <?php endif; ?>

                    <a href="otherProfile?id=<?= $currentProfile->getId() ?>&from=dashboard" class="card-btn-link">
                        <button type="button" class="btn-view-profile">
                            Profile <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </a>
                </div>
            </article>

            <section class="match-actions-container">
                <a href="dashboard?action=dislike&profile_id=<?= $currentProfile->getId() ?>" id="reject"
                    class="match-btn btn-reject">
                    <i class="fa-solid fa-xmark"></i>
                </a>
                <a href="dashboard?action=like&profile_id=<?= $currentProfile->getId() ?>" id="like"
                    class="match-btn btn-like">
                    <i class="fa-solid fa-heart"></i>
                </a>
            </section>

        <?php else: ?>
            <div class="no-more-profiles">
                <i class="fa-solid fa-circle-nodes"></i>
                <p>No more profiles available near you at the moment. Come back later !</p>
            </div>
        <?php endif; ?>
    </main>

    <footer class="dashboard-footer">
        <a href="search" class="footer-link"><i class="fa-solid fa-magnifying-glass"></i></a>
        <a href="messages?from=dashboard" class="footer-link"><i class="fa-regular fa-paper-plane"></i></a>
        <a href="profile" class="footer-avatar-link">
            <img src="/public/uploads/<?php echo htmlspecialchars($user->getAvatar()) ?>" alt="My Profile">
        </a>
    </footer>

    <div id="report-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeReportModal()">&times;</span>
            <h3>Report this profile</h3>

            <form action="dashboard" method="POST" id="report-form" class="modal-form">
                <input type="hidden" name="reported_user_id" id="reportedUserId" value="">

                <div class="form-group-textarea">
                    <label for="reportReason">Reason for report :</label>
                    <div class="textarea-wrapper">
                        <textarea name="report_reason" id="reportReason" rows="3" maxlength="150"
                            placeholder="Why are you reporting this profile? (Fake, inappropriate...)"
                            required></textarea>
                    </div>
                </div>

                <div class="modal-action-buttons">
                    <button type="button" class="btn-cancel" onclick="closeReportModal()">Cancel</button>
                    <button type="submit" name="submitReport" class="btn-submit-report">Send Report</button>
                </div>
            </form>
        </div>
    </div>

    <script src="/views/scripts/notificationsScript.js"></script>
    <script src="/views/scripts/report.js"></script>
</body>

</html>