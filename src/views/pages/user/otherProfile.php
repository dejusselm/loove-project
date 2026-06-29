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
<title><?= htmlspecialchars($profile->getFirstName()) ?>'s Profile</title>
<link href="/views/style/profile.css" rel="stylesheet">
</head>

<body>
    <header class="profile-header">
        <a href="<?= htmlspecialchars($backUrl) ?>" class="back-link"><i class="fa-solid fa-arrow-left"></i></a>
        <h1>Profile</h1>
    </header>

    <main class="profile-main">
        <section id="profile-section" class="profile-container">

            <div class="avatar-container">
                <img src="/public/uploads/<?php echo htmlspecialchars($profile->getAvatar()) ?>" class="main-avatar"
                    alt="Main avatar">
            </div>

            <?php if (!$matchController->hasUserInteracted($profileId)): ?>

                <article class="match-actions-container">
                    <a href="otherProfile?id=<?= $profile->getId() ?>&action=dislike&profile_id=<?= $profile->getId() ?>&from=<?= urlencode($from) ?>"
                        id="reject" class="match-btn btn-reject">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                    <a href="otherProfile?id=<?= $profile->getId() ?>&action=like&profile_id=<?= $profile->getId() ?>&from=<?= urlencode($from) ?>"
                        id="like" class="match-btn btn-like">
                        <i class="fa-solid fa-heart"></i>
                    </a>
                </article>
            <?php endif; ?>
            <article class="report-container-box">
                <i class="fa-warning fa-solid btn-trigger-report"
                    onclick="openReportModal(<?= $profile->getId() ?>)"></i>
            </article>


            <article id="gallery-section" class="profile-card">
                <div class="gallery-header">
                    <p><i class="fa-solid fa-images"></i> Photos</p>
                </div>
                <div class="profile-gallery">
                    <?php
                    if (!empty($userPhotos)):
                        foreach ($userPhotos as $photo): ?>
                            <div class="gallery-item">
                                <img src="/public/uploads/<?= htmlspecialchars($photo['photo_path']) ?>" alt="Secondary photo">
                            </div>
                        <?php endforeach;
                    endif;

                    $emptySlots = 4 - (isset($userPhotos) ? count($userPhotos) : 0);
                    for ($i = 0; $i < $emptySlots; $i++): ?>
                        <div class="gallery-item empty-slot-view">
                            <i class="fa-solid fa-image"></i>
                        </div>
                    <?php endfor; ?>
                </div>
            </article>

            <h3 class="profile-name">
                <?= htmlspecialchars($profile->getFirstName()) . ' ' . htmlspecialchars($profile->getLastName()) ?>
            </h3>

            <?php if ($profile->isMember()): ?>
                <div class="membership-badge">
                    <h3>Member</h3>
                    <i class="fa-crown fa-solid"></i>
                </div>
            <?php endif; ?>

            <p class="profile-age-gender">
                <?= $profile->getAge(), ' yo' ?>
                <?php if ($profile->getGender() == 'female') {
                    echo '<i class="fa-solid fa-venus icon-gender-female"></i>';
                } else if ($profile->getGender() == 'male') {
                    echo '<i class="fa-solid fa-mars icon-gender-male"></i>';
                } else {
                    echo '<i class="fa-solid fa-genderless icon-gender-other"></i>';
                }
                ?>
            </p>

            <article class="info-view-card" id="location">
                <p><i class="fa-solid fa-location-dot"></i> Lives in
                    <?= htmlspecialchars($profile->getCity() ?? "Not defined"); ?>
                </p>
            </article>

            <article class="info-view-card" id="description">
                <p><i class="fa-solid fa-quote-left"></i>
                    <?= htmlspecialchars($profile->getDescription() ?? "No description provided."); ?>
                    <i class="fa-solid fa-quote-right"></i>
                </p>
            </article>

            <article class="info-view-card" id="interest">
                <p>
                    <i class="fa-solid fa-heart"></i>

                    <?php
                    $gender = $profile->getInterest();
                    $relationship = $profile->getRelationship();

                    if ($gender === 'male') {
                        $lookingFor = 'Looking for a man';
                    } elseif ($gender === 'female') {
                        $lookingFor = 'Looking for a woman';
                    } elseif ($gender === 'other') {
                        $lookingFor = 'Looking for a non-binary person';
                    } else {
                        $lookingFor = 'Open to everyone';
                    }

                    echo $lookingFor;

                    if ($relationship !== 'anything') {
                        switch ($relationship) {
                            case 'friend':
                                echo ' for friendship';
                                break;
                            case 'significant-other':
                                echo ' for a serious relationship';
                                break;
                            case 'one-night-stand':
                                echo ' for a one night stand';
                                break;

                            default:
                                echo ' (' . htmlspecialchars($relationship) . ')';
                        }
                    }
                    echo '.';
                    ?>
                </p>
            </article>

            <article class="info-view-card" id="hobbies">
                <div class="hobbies-content">
                    <p><i class="fa-solid fa-star"></i> Hobbies / interests :</p>
                    <ul class="hobbies-list">
                        <?php if (!empty($userHobbies)): ?>
                            <?php foreach ($userHobbies as $hobby): ?>
                                <li class="hobby-tag-item"><?= htmlspecialchars($hobby->getName()) ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No hobbies selected yet !</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </article>

            <?php $features = $profile->getFeatures(); ?>
            <article class="info-view-card" id="advanced-features">
                <div class="advanced-content">
                    <p><i class="fa-solid fa-wand-magic-sparkles"></i> Additional Information :</p>
                    <ul class="advanced-list">
                        <li><strong>Astrology :</strong>
                            <?= htmlspecialchars($features['astrology'] ?? "Not defined"); ?>
                        </li>
                        <li><strong>Education :</strong>
                            <?= htmlspecialchars($features['studies_level'] ?? "Not defined"); ?>
                        </li>
                        <li><strong>Profession :</strong>
                            <?= htmlspecialchars($features['job'] ?? "Not defined"); ?>
                        </li>
                    </ul>
                </div>
            </article>
        </section>
    </main>

    <div id="report-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeReportModal()">&times;</span>
            <h3>Report this profile</h3>

            <form action="otherProfile?id=<?= $profile->getId() ?>" method="POST" id="report-form" class="modal-form">
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

    <script>
        function openReportModal(userId) {
            document.getElementById('reportedUserId').value = userId;
            document.getElementById('report-modal').style.display = 'flex';
        }
        function closeReportModal() {
            document.getElementById('report-modal').style.display = 'none';
        }
    </script>
    <script src="/views/scripts/notificationsScript.js"></script>
</body>

</html>