<?php
include ROOT_PATH . 'controllers/UserController.php';
include ROOT_PATH . 'controllers/MatchController.php';

$profileId = $_GET['id'] ?? null;
$userController = new UserController();

extract($userController->getTargetProfileData($profileId));

include ROOT_PATH . 'views/components/head.php';
?>
<title><?= $profile->getFirstName() ?>'s Profile</title>
<link href="/views/style/profile.css" rel="stylesheet">
</head>

<body>
    <header>
        <a href="adminDashboard"><i class="fa-solid fa-arrow-left"></i></a>
        <h1>Profile</h1>
    </header>
    <main>
        <section id="profile">
            <img src="/public/uploads/<?php echo htmlspecialchars($profile->getAvatar()) ?>">
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
    </main>
    <script src="/views/scripts/notificationsScript.js"></script>
</body>

</html>