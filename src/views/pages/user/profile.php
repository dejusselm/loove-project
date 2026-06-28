<?php
include ROOT_PATH . 'controllers/UserController.php';

$userController = new UserController();
extract($userController->profileData());

include ROOT_PATH . 'views/components/head.php';
?>
<title>Profile</title>
<link href="/views/style/profile.css" rel="stylesheet">
</head>

<body>
    <header class="profile-header">
        <a href="dashboard" class="back-link"><i class="fa-solid fa-arrow-left"></i></a>
        <h1>Profile</h1>
        <div class="header-actions">
            <a href="parameters" class="settings-link"><i class="fa-solid fa-gear"></i></a>
        </div>
    </header>

    <main class="profile-main">
        <section id="profile-section" class="profile-container">

            <div class="avatar-container">
                <img src="/public/uploads/<?php echo htmlspecialchars($user->getAvatar()) ?>" class="main-avatar"
                    alt="Avatar principal">
            </div>

            <article id="gallery-section" class="profile-card">
                <div class="gallery-header">
                    <p><i class="fa-solid fa-images"></i> My Photos</p>
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
                        <div class="gallery-item empty-slot" onclick="openModal('photos')">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                    <?php endfor; ?>
                </div>
            </article>

            <h3 class="profile-name"><?= $user->getFirstName(), ' ', $user->getLastName() ?></h3>

            <?php if ($user->isMember()): ?>
                <div class="membership-badge">
                    <h3>Member</h3>
                    <i class="fa-crown fa-solid"></i>
                </div>
            <?php endif; ?>

            <p class="profile-age-gender">
                <?= $user->getAge(), ' yo' ?>
                <?php if ($user->getGender() == 'female') {
                    echo '<i class="fa-solid fa-venus icon-gender-female"></i>';
                } else if ($user->getGender() == 'male') {
                    echo '<i class="fa-solid fa-mars icon-gender-male"></i>';
                } else {
                    echo '<i class="fa-solid fa-genderless icon-gender-other"></i>';
                }
                ?>
            </p>

            <article class="modify-card" id="location">
                <p><i class="fa-solid fa-location-dot"></i> Lives in
                    <?= $user->getCity() ?? "( <i class='fa-solid fa-circle-exclamation'></i> field is not defined. )"; ?>
                </p>
                <?php if (isset($_SESSION['errorMessage'])) {
                    echo '<span class="error-msg">' . $_SESSION['errorMessage'] . '</span>';
                } ?>
                <i class="fa-solid fa-pencil edit-icon" onclick="openModal('location')"></i>
            </article>

            <article class="modify-card" id="description">
                <p><i class="fa-solid fa-quote-left"></i>
                    <?= $user->getDescription() ?? " <i class='fa-solid fa-circle-exclamation'></i> field is not defined."; ?>
                    <i class="fa-solid fa-quote-right"></i>
                </p>
                <i class="fa-solid fa-pencil edit-icon" onclick="openModal('description')"></i>
            </article>

            <article class="modify-card" id="interest">
                <p>
                    <i class="fa-solid fa-heart"></i>

                    <?php
                    $gender = $user->getInterest();
                    $relationship = $user->getRelationship();

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
                <i class="fa-solid fa-pencil edit-icon" onclick="openModal('preferences')"></i>
            </article>

            <article class="modify-card" id="hobbies">
                <div class="hobbies-content">
                    <p><i class="fa-solid fa-star"></i> Hobbies / interests :</p>
                    <ul class="hobbies-list">
                        <?php if (!empty($userHobbies)): ?>
                            <?php foreach ($userHobbies as $hobby): ?>
                                <li class="hobby-tag-item"><?= htmlspecialchars($hobby->getName()) ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><i class='fa-solid fa-circle-exclamation'></i> No hobbies selected yet !</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <i class="fa-solid fa-pencil edit-icon" onclick="openModal('hobby')"></i>
            </article>

            <?php $features = $user->getFeatures(); ?>
            <article class="modify-card" id="advanced-features">
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
                <i class="fa-solid fa-pencil edit-icon" onclick="openModal('advanced')"></i>
            </article>
        </section>
    </main>

    <div id="location-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('location')">&times;</span>
            <h3>Change your city :</h3>
            <form action="profile" method="POST" id="location-form" class="modal-form">
                <div class="textarea-wrapper">
                    <textarea name="location" placeholder="Paris..." required autofocus></textarea>
                </div>
                <button type="submit" name="locationModified" class="btn-submit"><i
                        class="fa-solid fa-check"></i></button>
            </form>
        </div>
    </div>

    <div id="description-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('description')">&times;</span>
            <h3>Change your description ( 200 characters max ):</h3>
            <form action="profile" method="POST" id="description-form" class="modal-form">
                <div id="textarea" class="textarea-wrapper">
                    <textarea name="description" maxlength="200" required
                        placeholder="<?= htmlspecialchars($user->getDescription() ?? ""); ?> " autofocus></textarea>
                </div>
                <button type="submit" name="descriptionModified" class="btn-submit"><i
                        class="fa-solid fa-check"></i></button>
            </form>
        </div>
    </div>

    <div id="preferences-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('preferences')">&times;</span>
            <h3>Change what you're looking for:</h3>
            <form action="profile" method="POST" id="preferences-form" class="modal-form">
                <span class="field-title">Gender interest :</span>
                <div class="radio-group">
                    <div class="radio-item">
                        <input type="radio" id="interestChoice1" name="interest" value="male" checked />
                        <label for="interestChoice1">Male</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="interestChoice2" name="interest" value="female" />
                        <label for="interestChoice2">Female</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="interestChoice3" name="interest" value="non-binary" />
                        <label for="interestChoice3">Non binary</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="interestChoice4" name="interest" value="all" />
                        <label for="interestChoice4">All</label>
                    </div>
                </div>

                <span class="field-title">Relationship :</span>
                <div class="radio-group">
                    <div class="radio-item">
                        <input type="radio" id="relationChoice1" name="relation" value="anything" checked />
                        <label for="relationChoice1">Anything</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="relationChoice2" name="relation" value="significant-other" />
                        <label for="relationChoice2">Significant other</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="relationChoice3" name="relation" value="friend" />
                        <label for="relationChoice3">Friend</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="relationChoice4" name="relation" value="one-night-stand" />
                        <label for="relationtChoice4">One night stand</label>
                    </div>
                </div>
                <button type="submit" name="preferencesModified" class="btn-submit"><i
                        class="fa-solid fa-check"></i></button>
            </form>
        </div>
    </div>

    <div id="hobby-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('hobby')">&times;</span>
            <h3>Select your hobbies / interests (5 max)</h3>
            <form action="profile" method="POST" id="hobby-form" class="modal-form">
                <div class="tags-div">
                    <?php foreach ($allHobbies as $hobby): ?>
                        <div class="hobby-tag">
                            <input type="checkbox" name="hobbies[]" value="<?= $hobby->getId() ?>"
                                id="hobby-<?= $hobby->getId() ?>" class="hobby-checkbox">
                            <label for="hobby-<?= $hobby->getId() ?>" class="hobby-name">
                                <?= htmlspecialchars($hobby->getName()) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" name="hobbiesModified" class="btn-save-hobbies btn-submit"><i
                        class="fa-solid fa-check"></i></button>
            </form>
        </div>
    </div>

    <div id="advanced-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('advanced')">&times;</span>
            <h3>Update your advanced info :</h3>
            <form action="profile" method="POST" id="advanced-form" class="modal-form">
                <div class="form-group-select">
                    <label for="astrology">Astrological Sign :</label>
                    <select name="astrology" id="astrology" class="modal-select">
                        <option value="">Select your sign...</option>
                        <?php
                        $signs = ['Aries', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo', 'Libra', 'Scorpio', 'Sagittarius', 'Capricorn', 'Aquarius', 'Pisces'];
                        $currentAstro = $features['astrology'] ?? '';
                        foreach ($signs as $sign): ?>
                            <option value="<?= $sign ?>" <?= $currentAstro === $sign ? 'selected' : '' ?>><?= $sign ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group-select">
                    <label for="studies_level">Education Level :</label>
                    <select name="studies_level" id="studies_level" class="modal-select">
                        <option value="">Select your education level...</option>
                        <?php
                        $levels = ['High School', 'Bachelor', 'Master', 'PhD', 'Self-taught', 'Other'];
                        $currentLevel = $features['studies_level'] ?? '';
                        foreach ($levels as $level): ?>
                            <option value="<?= $level ?>" <?= $currentLevel === $level ? 'selected' : '' ?>><?= $level ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group-select">
                    <label for="job">Profession :</label>
                    <input type="text" name="job" id="job" maxlength="50" placeholder="Developer, Nurse, Student..."
                        value="<?= htmlspecialchars($features['job'] ?? ''); ?>" class="modal-input-text">
                </div>
                <button type="submit" name="featuresModified" class="btn-submit"><i class="fa-solid fa-check"></i>
                    Save</button>
            </form>
        </div>
    </div>

    <div id="photos-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('photos')">&times;</span>
            <h3>Manage your photos</h3>

            <form action="profile" method="POST" enctype="multipart/form-data" id="photos-form" class="modal-form">
                <div class="photo-upload-group">
                    <label for="photo">Upload a new picture :</label>
                    <input type="file" name="photo" id="photo" accept="image/png, image/jpeg, image/webp"
                        onchange="handlePhotoChange(this,'photo')" required class="file-input-dashed">
                    <div class="preview-container">
                        <img id="photoPreview" src="#" alt="Photo Preview" class="photo-preview-circle">
                    </div>
                </div>
                <button type="submit" name="photoModified" class="btn-submit"><i class="fa-solid fa-cloud-arrow-up"></i>
                    Upload</button>
            </form>
        </div>
    </div>

    <script src="/views/scripts/profileScript.js"></script>
    <script src="/views/scripts/notificationsScript.js"></script>
    <script src="/views/scripts/photo.js"></script>
</body>

</html>