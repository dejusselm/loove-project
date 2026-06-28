<?php
include ROOT_PATH . 'controllers/UserController.php';
include ROOT_PATH . 'controllers/MatchController.php';

$profileId = $_GET['id'] ?? null;
$userController = new UserController();
$matchController = new MatchController();

$userController->registerView($profileId);

if (isset($_GET['action']) && isset($_GET['profile_id']) && ($_GET['action'] === 'like' || $_GET['action'] === 'dislike')) {
    $matchController->handleInteraction();
}

extract($userController->getTargetProfileData($profileId));

$from = $_GET['from'] ?? 'dashboard';
$backUrl = $from;

include ROOT_PATH . 'views/components/head.php';
?>
<title><?= $profile->getFirstName() ?>'s Profile</title>
<link href="/views/style/profile.css" rel="stylesheet">
</head>

<body>
    <header>
        <a href="<?= $backUrl ?>"><i class="fa-solid fa-arrow-left"></i></a>
        <h1>Profile</h1>
    </header>
    <main>
        <section id="profile">
            <img src="/public/uploads/<?php echo htmlspecialchars($profile->getAvatar()) ?>">
            <?php if (!$matchController->hasUserInteracted($profileId)): ?>
                <article>
                    <i class="fa-warning fa-solid" style="cursor: pointer;"
                        onclick="openReportModal(<?= $profile->getId() ?>, '<?= htmlspecialchars($profile->getFirstName()) ?>')"></i>
                    <a href="dashboard?action=dislike&profile_id=<?= $profileId; ?>">
                        <i class=" fa-solid fa-circle-xmark"></i>
                    </a>
                    <a href="dashboard?action=like&profile_id=<?= $profileId; ?>">
                        <i class="fa-solid fa-heart"></i>
                    </a>
                </article>
            <?php endif; ?>
            <?php if ($userController->user->isMember()): ?>
                <a href="/user/chat?id=<?= $profileId ?>"><i class="fa-paper-plane fa-solid"></i></a>
            <?php endif; ?>

            <article>
                <h3><?= $profile->getFirstName() ?></h3>
                <?php if ($profile->isMember()) {
                    echo '<div class="membership"><h3> Member </h3><i class="fa-crown fa-solid"></i></div>';
                } ?>
                <p>
                    <?= $profile->getAge(), ' yo' ?>
                    <?php if ($profile->getGender() == 'female') {
                        echo '<i class="fa-solid fa-venus" style=color:purple;></i>';
                    } else if ($profile->getGender() == 'male') {
                        echo '<i class="fa-solid fa-mars" style="color:blue"></i>';
                    } else {
                        echo '<i class="fa-solid fa-genderless" style="color:yellow"></i>';
                    }
                    ?>
                </p>

            </article>
            <article id="gallery-section">
                <div class="gallery-header">
                    <p><i class="fa-solid fa-images"></i> My Photos</p>
                </div>
                <div class="profile-gallery">
                    <?php
                    if (!empty($photos)):
                        foreach ($photos as $photo): ?>
                            <div class="gallery-item">
                                <img src="/public/uploads/<?= htmlspecialchars($photo['photo_path']) ?>" alt="Secondary photo">
                            </div>
                        <?php endforeach;
                    endif; ?>
                </div>
            </article>
            <article class="modify" id="location">
                <p><i class="fa-solid fa-location-dot"></i>Lives in
                    <?= $profile->getCity() ?? "Not specified."; ?>
                </p>

            </article>
            <article class="modify" id="description">
                <p><i class="fa-solid fa-quote-left"></i>
                    <?= $profile->getDescription() ?? "No description."; ?>
                    <i class="fa-solid fa-quote-right"></i>
                </p>
            </article>
            <article class="modify" id="interest">
                <p><i class="fa-solid fa-heart"></i></i>Looking for a
                    <?php if ($profile->getInterest() !== 'all') {
                        echo htmlspecialchars($profile->getInterest()) . ' ';
                    }
                    echo $profile->getRelationship(); ?> !
                </p>
            </article>
            <article class="modify" id="hobbies">
                <div>
                    <p><i class="fa-solid fa-star"></i>Hobbies / interests :</p>
                    <ul>
                        <?php if (!empty($hobbies)): ?>
                            <?php foreach ($hobbies as $hobby): ?>
                                <li><?= htmlspecialchars($hobby->getName()) ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><i class='fa-solid fa-circle-exclamation'></i> No hobbies selected yet !</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </article>
            <article class="modify" id="advanced-features">
                <div>
                    <p><i class="fa-solid fa-wand-magic-sparkles"></i> Additional Information :</p>
                    <ul style="list-style: none; padding-left: 1.5em; margin: 0;">
                        <li><strong>Astrology :</strong>
                            <?= htmlspecialchars($profile->getFeatures()['astrology'] ?? "Not defined "); ?>
                        </li>
                        <li><strong>Education :</strong>
                            <?= htmlspecialchars($profile->getFeatures()['studies_level'] ?? "Not defined "); ?>
                        </li>
                        <li><strong>Profession :</strong>
                            <?= htmlspecialchars($profile->getFeatures()['job'] ?? "Not defined"); ?>
                        </li>
                    </ul>
                </div>
            </article>
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
</body>

</html>