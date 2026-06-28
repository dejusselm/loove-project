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

extract($userController->profileData());
$currentProfile = !empty($profiles) ? $profiles[0] : null;

include ROOT_PATH . 'views/components/head.php';
?>
<title>Home</title>
<link href="/views/style/dashboard.css" rel="stylesheet">
</head>

<body>
    <header>
        <h1>Dashboard</h1>
        <a href="search"><i class="fa-solid fa-magnifying-glass"></i></a>
        <a href="messages?from=dashboard"><i class="fa-regular fa-paper-plane"></i></a>
        <a href="profile"><img src="/public/uploads/<?php echo htmlspecialchars($user->getAvatar()) ?>"></a>
    </header>
    <main>
        <?php if (isset($_SESSION['flashMessage'])): ?>
            <div class="flash-modal" style="display: flex;">
                <div class="modal-content">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>
                        <?= htmlspecialchars($_SESSION['flashMessage']) ?> </span>
                    <button class="close-flash" onclick="this.closest('.flash-modal').remove()">&times;</button>
                </div>
            </div>
            <?php
            unset($_SESSION['flashMessage']);
            ?>
        <?php endif; ?>

        <section>
            <?php if ($matchController->countTodaySwipes() >= 10): ?>
                <h3>Max swipes limit attained. Come back tomorrow.</h3>
            <?php elseif ($currentProfile): ?>
                <article>
                    <div class="card">
                        <div class="img" src=""
                            style="background-image: linear-gradient(180deg, rgba(27, 32, 33, 0) 50%, rgba(234, 99, 140, 0.51) 60%, rgba(137, 2, 62, 1) 100%),url('../public/uploads/<?= htmlspecialchars($currentProfile->getAvatar()); ?>')">
                            <div class="blank-space"></div>
                            <div class="card-content">
                                <h4><?= $currentProfile->getFirstName() . ', ' . $currentProfile->getAge() ?></h4>
                                <p><i class="fa-solid fa-quote-left"></i>
                                    <?= $currentProfile->getDescription() ?? "No description" ?>
                                    <i class="fa-solid fa-quote-right">
                                    </i>
                                </p>
                                <p><i class="fa-solid fa-location-dot"></i>
                                    <?php
                                    if ($currentProfile->getCity()) {
                                        echo $currentProfile->city;
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="interactions">
                            <i class="fa-warning fa-solid" style="cursor: pointer;"
                                onclick="openReportModal(<?= $currentProfile->getId() ?>, '<?= htmlspecialchars($currentProfile->getFirstName()) ?>')"></i>

                            <a href="dashboard?action=like&profile_id=<?= $currentProfile->getId() ?>">
                                <i class="fa-heart fa-solid"></i>
                            </a>

                            <a href="dashboard?action=dislike&profile_id=<?= $currentProfile->getId() ?>">
                                <i class="fa-x fa-solid"></i>
                            </a>

                            <a href="dashboard?action=shuffle">
                                <i class="fa-solid fa-arrow-rotate-left"></i>
                            </a>
                        </div>
                        <a href="otherProfile?id=<?= $currentProfile->getId() ?>&from=dashboard">
                            <button>Profile <i class="fa-solid fa-arrow-right"></i></button>
                        </a>
                    </div>
                </article>

            <?php else: ?>
                <div class="no-profiles">
                    <h3>Sorry, there isn't any more profiles today.</h3>
                    <p>Come back later or change your criterias.</p>
                </div>
            <?php endif; ?>
        </section>

        <div id="reportProfileModal" class="modal"
            style="display:none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
            <div class="modal-content"
                style="background-color: #fff; margin: 15% auto; padding: 20px; border-radius: 8px; width: 350px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); font-family: sans-serif;">
                <h3 style="color: #e74c3c; margin-top: 0;"><i class="fa-solid fa-triangle-exclamation"></i> Report
                    Profile</h3>

                <form action="dashboard" method="POST">
                    <input type="hidden" name="reported_profile_id" id="reportedProfileId" value="">

                    <p id="reportModalMessage" style="font-size: 0.95em; color: #333;"></p>

                    <div style="margin-bottom: 15px;">
                        <label for="reportReason"
                            style="display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.9em;">Reason for
                            report :</label>
                        <textarea name="report_reason" id="reportReason" rows="3" maxlength="150"
                            style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; resize: none;"
                            placeholder="Why are you reporting this profile? (Fake, inappropriate...)"
                            required></textarea>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                        <button type="button" onclick="closeReportModal()"
                            style="padding: 8px 15px; background-color: #bdc3c7; color: white; border: none; border-radius: 4px; cursor: pointer;">
                            Cancel
                        </button>
                        <button type="submit" name="submitReport"
                            style="padding: 8px 15px; background-color: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                            Send Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script src="/views/scripts/notificationsScript.js"></script>
    <script src="/views/scripts/report.js"></script>
</body>

</html>