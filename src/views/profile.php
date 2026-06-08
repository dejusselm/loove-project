<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION["userId"])) {
    header("Location: login.php");
    exit;
}
include "../controllers/profileController.php";
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="style/profile.css" rel="stylesheet">
</head>

<body>
    <header>
        <a href="dashboard.php"><i class="fa-solid fa-arrow-left" style="font-size:50px"></i></a>
        <h1>Profile</h1>
        <div>
            <a href="parameters.php"><i class="fa-solid fa-gear" style="font-size:50px"></i></a><br><br>
            <a href=" login.php"><i class="fa-solid fa-arrow-right-from-bracket" style="font-size:50px"></i></a>
        </div>
    </header>
    <main>
        <section id="profile">
            <img src="../public/uploads/<?php echo htmlspecialchars($data["avatar"]) ?>">
            <article>
                <h3><?= $data["first_name"], ' ', $data["last_name"] ?></h3>
                <p>
                    <?= $usersAge, ' yo' ?>
                    <?php if ($data["gender"] == "female") {
                        echo '<i class="fa-solid fa-venus" style=color:purple;></i>';
                    } else {
                        echo '<i class="fa-solid fa-mars" style="color:blue"></i>';
                    }
                    ?>
                </p>

            </article>
            <article class="modify" id="description">
                <p>" <?= $data["description"]; ?> "</p>
                <i class="fa-solid fa-pencil" onclick="openDescriptionModal('description')"></i>
            </article>
            <article class="modify" id="interest">
                <p>I'm searching for a <?= $data["interest"], " ", $data["relationship"]; ?> !</p>
            </article>
            <article class="modify" id="hobbies">
                <div>
                    <p>My Hobbies :</p>
                    <ul>
                        <?php if (!empty($hobbies)): ?>
                            <?php foreach ($hobbies as $hobby): ?>
                                <li><?= htmlspecialchars($hobby['name']) ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No hobbies selected yet !</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <i class="fa-solid fa-pencil" onclick="openHobbyModal()"></i>
            </article>
        </section>
    </main>

    <div id="hobby-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeHobbyModal()">&times;</span>

            <h3>Select your hobbies (5 max)</h3>

            <form action="profile.php" method="POST" id="hobby-form">
                <div class="tags-div">
                    <?php foreach ($allHobbies as $hobby): ?>
                        <div class="hobby-tag">
                            <input type="checkbox" name="hobbies[]" value="<?= $hobby['id'] ?>"
                                id="hobby-<?= $hobby['id'] ?>" class="hobby-checkbox">
                            <label for="hobby-<?= $hobby['id'] ?>" class="hobby-name">
                                <?= htmlspecialchars($hobby['name']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" name="submit_hobbies" class="btn-save-hobbies"><i
                        class="fa-solid fa-check"></i></button>
            </form>
        </div>
    </div>
    <div id="description-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeDescriptionModal()">&times;</span>
            <h3>Change your description :</h3>

            <form action="profile.php" method="POST" id="description-form">
                <div>
                    <textarea name="description" required><?= htmlspecialchars($data["description"]); ?></textarea>
                </div>
                <button type="submit" name="descriptionModified"><i class="fa-solid fa-check"></i></button>
            </form>
        </div>
    </div>

    <script src="scripts/profileScript.js"></script>
</body>

</html>