<?php
include ROOT_PATH . 'controllers/SearchController.php';

$controller = new SearchController();
extract($controller->index());

if (isset($_POST['submitSearch'])) {
    $profiles = $controller->search();
}

include ROOT_PATH . 'views/components/head.php';
?>
<title>Search page</title>
<link href="/views/style/profile.css" rel="stylesheet">
<link href="/views/style/search.css" rel="stylesheet">
</head>

<body>
    <header class="search-header">
        <a href="dashboard" class="back-link"><i class="fa-solid fa-arrow-left"></i></a>
        <h1>Search</h1>
    </header>

    <button type="button" class="collapsible filters-button">
        Select filters <i class="fa-solid fa-caret-down" id="collapsible-i"></i>
    </button>

    <div class="content filters-wrapper" style="display: none;">
        <form action="search" method="POST" id="search-form" class="search-filters-form">
            <div class="form-group">
                <label>Search by name</label>
                <input type="search" placeholder="Ex : John Doe" name="name">
            </div>

            <div class="form-group">
                <label for="relation">Relationship</label>
                <select name="relation" id="relation" class="filter-select">
                    <option value="anything">Any relationship</option>
                    <option value="friends">Friends</option>
                    <option value="lovers">Lovers</option>
                    <option value="oneNightStand">One night stand</option>
                </select>
            </div>

            <div class="form-group">
                <label>Age</label>
                <div id="age" style="display: flex; gap: 10px;">
                    <input type="number" name="minAge" id="minAge" placeholder="Min." min="18" max="100"
                        style="flex: 1; padding: 8px; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px;">
                    <input type="number" name="maxAge" id="maxAge" placeholder="Max." min="18" max="100"
                        style="flex: 1; padding: 8px; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px;">
                </div>
            </div>

            <div class="form-group">
                <label>Search By city</label>
                <div id="city">
                    <input type="search" name="city" id="searchCity" placeholder="Ex : Paris">
                    <label for="radius" id="radiusSelect" class="radiusSelect" style="display: none;"></label>
                    <select name="radius" class="radiusSelect filter-select" style="display: none; margin-top: 5px;">
                        <option value="">--Select Radius--</option>
                        <option value="five">5km</option>
                        <option value="ten">10km</option>
                        <option value="twenty-five">25km</option>
                        <option value="fifty">50km</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Hobbies / Interests</label>
                <div class="tags-div" id="hobby-form">
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
            </div>

            <div id="premium-filters" style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed #ccc;">
                <span style="font-weight: bold; display: flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-crown" style="color: gold;"></i> Advanced Search
                    <?php if (!$controller->user->isMember()): ?>
                        <span style="font-size: 0.8em; color: #ff4d4d;">(Members Only)</span>
                    <?php endif; ?>
                </span>

                <?php $isMember = $controller->user->isMember(); ?>

                <div class="form-group" style="margin: 10px 0;">
                    <label for="search-astrology"
                        style="display:block; font-size:0.9em; margin-bottom:3px;">Astrological Sign :</label>
                    <select name="astrology" id="search-astrology" class="filter-select" <?= !$isMember ? 'disabled style="background:#e9e9e9; color:#999;"' : '' ?>>
                        <option value="">Any sign</option>
                        <?php
                        $signs = ['Aries', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo', 'Libra', 'Scorpio', 'Sagittarius', 'Capricorn', 'Aquarius', 'Pisces'];
                        foreach ($signs as $sign): ?>
                            <option value="<?= $sign ?>"><?= $sign ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin: 10px 0;">
                    <label for="search-education" style="display:block; font-size:0.9em; margin-bottom:3px;">Education
                        Level :</label>
                    <select name="studies_level" id="search-education" class="filter-select" <?= !$isMember ? 'disabled style="background:#e9e9e9; color:#999;"' : '' ?>>
                        <option value="">Any level</option>
                        <?php
                        $levels = ['High School', 'Bachelor', 'Master', 'PhD', 'Self-taught', 'Other'];
                        foreach ($levels as $level): ?>
                            <option value="<?= $level ?>"><?= $level ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin: 10px 0;">
                    <label for="search-job" style="display:block; font-size:0.9em; margin-bottom:3px;">Profession / Job
                        :</label>
                    <input type="text" name="job" id="search-job" placeholder="Ex: Developer" <?= !$isMember ? 'disabled style="background:#e9e9e9; color:#bfbfbf; width:100%; padding: 8px; box-sizing: border-box; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1);"' : 'style="width:100%; padding: 8px; box-sizing: border-box; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1);"' ?>>
                </div>

                <?php if (!$isMember): ?>
                    <p style="font-size: 0.85em; margin: 5px 0 15px 0;">
                        <a href="/memberships" style="color: #ff4d4d; font-weight: bold; text-decoration: none;">
                            Upgrade to Premium <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </p>
                <?php endif; ?>
            </div>

            <button type="submit" name="submitSearch" class="btn-search-submit">
                <i class="fa-solid fa-magnifying-glass"></i> Confirm
            </button>
        </form>
    </div>

    <main class="search-main">
        <section class="search-results-grid" id="results">
            <?php if (!empty($profiles)): ?>
                <?php foreach ($profiles as $profile): ?>
                    <article class="profile-result-card"
                        style="background-image: linear-gradient(180deg, rgba(27, 32, 33, 0) 50%, rgba(234, 99, 140, 0.51) 60%, rgba(137, 2, 62, 1) 100%), url('../public/uploads/<?= htmlspecialchars($profile->getAvatar()); ?>');">

                        <div class="card-blur-space"></div>

                        <div class="card-content">
                            <h4><?= htmlspecialchars($profile->getFirstName()) . ', ' . $profile->getAge() . ' yo' ?></h4>

                            <p class="card-desc">
                                <i class="fa-solid fa-quote-left"></i>
                                <?= htmlspecialchars($profile->getDescription() ?? "No description") ?>
                                <i class="fa-solid fa-quote-right"></i>
                            </p>

                            <?php if ($profile->getCity()): ?>
                                <p class="card-location">
                                    <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($profile->getCity()); ?>
                                </p>
                            <?php endif; ?>

                            <a href="otherProfile?id=<?= $profile->getId() ?>&from=search" class="card-btn-link">
                                <button type="button" class="btn-view-profile">
                                    Profile <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <?php if (isset($_POST['submitSearch'])): ?>
                    <p class="no-results-msg">No profiles found matching your criteria.</p>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </main>

    <script src="/views/scripts/searchScript.js"></script>
    <script src="/views/scripts/notificationsScript.js"></script>
</body>

</html>