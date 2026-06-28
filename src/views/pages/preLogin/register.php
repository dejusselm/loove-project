<?php
require_once ROOT_PATH . 'controllers/AuthController.php';

$authController = new AuthController();
$authController->register();
include ROOT_PATH . 'views/components/head.php';
?>
<link href="/views/style/preLogin.css" rel="stylesheet">
<title>Register</title>
</head>

<body>
    <header>
        <a href="/preLogin/home"><img class="logo" src="/views/images/logoLoovePink.png" alt="Logo Loove"></a>
    </header>
    <main>
        <section>
            <div class="registerHead">
                <h1>Register :</h1>
            </div>

            <?php if (isset($_SESSION['errorMessage'])): ?>
                <div class="error-box"
                    style="color: red; margin-bottom: 1em; display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        <?php
                        echo $_SESSION['errorMessage'];
                        unset($_SESSION['errorMessage']);
                        ?>
                    </span>
                    <button type="button" onclick="this.parentElement.style.display='none';"
                        style="background: none; border: none; font-size: 1.2em; cursor: pointer; color: red;">
                        &times;
                    </button>
                </div>
            <?php endif; ?>

            <form id="regForm" action="" method="POST" enctype="multipart/form-data">

                <div class="form-group" style="margin-bottom: 1.5em;">
                    <label>Email :</label>
                    <input type="email" name="email" placeholder="E-mail..." required
                        pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}" style="width: 100%;">
                </div>

                <div class="form-group" style="margin-bottom: 1.5em;">
                    <label>Password (at least 12 characters) :</label>
                    <input type="password" id="password" name="password" placeholder="Password..." required
                        minlength="12" style="width: 100%; margin-bottom: 0.5em;">

                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm password..."
                        required minlength="12" style="width: 100%;">
                </div>

                <hr style="border: 1px solid #f1f5f9; margin: 2em 0;">

                <div class="form-group" style="margin-bottom: 1.5em;">
                    <label>Name :</label>
                    <input type="text" name="firstName" placeholder="First name..." required
                        pattern="[A-Za-zÀ-ÿ\s\-]{2,}" style="width: 100%; margin-bottom: 0.5em;">
                    <input type="text" name="lastName" placeholder="Last name..." required pattern="[A-Za-zÀ-ÿ\s\-]{2,}"
                        style="width: 100%;">
                </div>

                <div class="form-group" style="margin-bottom: 1.5em;">
                    <label>Birthday :</label>
                    <input type="date" name="birthdate" value="2000-01-01" min="1926-01-02" max="2008-12-31" required
                        style="width: 100%;">
                </div>

                <hr style="border: 1px solid #f1f5f9; margin: 2em 0;">

                <div class="form-group" style="margin-bottom: 1.5em;">
                    <span class="field-title"
                        style="font-weight: bold; color: var(--dark-grey); display: block; margin-bottom: 0.5em;">Gender
                        :</span>
                    <div class="radio-group">
                        <div class="radio-item"><input type="radio" id="genderChoice1" name="gender" value="male"
                                checked /> <label for="genderChoice1">Male</label></div>
                        <div class="radio-item"><input type="radio" id="genderChoice2" name="gender" value="female" />
                            <label for="genderChoice2">Female</label>
                        </div>
                        <div class="radio-item"><input type="radio" id="genderChoice3" name="gender"
                                value="non-binary" /> <label for="genderChoice3">Non-binary</label></div>
                        <div class="radio-item"><input type="radio" id="genderChoice4" name="gender" value="other" />
                            <label for="genderChoice4">Other</label>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5em;">
                    <span class="field-title"
                        style="font-weight: bold; color: var(--dark-grey); display: block; margin-bottom: 0.5em;">Gender
                        interest :</span>
                    <div class="radio-group">
                        <div class="radio-item"><input type="radio" id="interestChoice1" name="interest" value="male"
                                checked /> <label for="interestChoice1">Male</label></div>
                        <div class="radio-item"><input type="radio" id="interestChoice2" name="interest"
                                value="female" /> <label for="interestChoice2">Female</label></div>
                        <div class="radio-item"><input type="radio" id="interestChoice3" name="interest"
                                value="non-binary" /> <label for="interestChoice3">Non binary</label></div>
                        <div class="radio-item"><input type="radio" id="interestChoice4" name="interest" value="all" />
                            <label for="interestChoice4">All</label>
                        </div>
                    </div>
                </div>

                <hr style="border: 1px solid #f1f5f9; margin: 2em 0;">

                <div class="form-group" style="margin-bottom: 2em;">
                    <label>Profile picture (only under 2Mo) :</label>
                    <input type="file" id="avatar" name="avatar" accept="image/png, image/jpeg, image/webp"
                        onchange="handlephotoChange(this,'avatar')" required style="width: 100%;">

                    <div class="avatar-preview-container" style="text-align: center; margin-top: 1em;">
                        <div id="avatarPlaceholder"
                            style="width: 100px; height: 100px; border-radius: 50%; border: 2px dashed var(--light-pink); display: flex; align-items: center; justify-content: center; margin: 0 auto; color: var(--light-pink); font-size: 2em; background-color: #f8fafc;">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                        <img id="avatarPreview" src="#" alt="Avatar Preview"
                            style="display: none; width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 3px solid var(--light-pink); box-shadow: 0 4px 12px rgba(234, 99, 140, 0.15); margin: 0 auto;">
                    </div>
                </div>

                <input type="hidden" value="1" name="submitted" />

                <button type="submit" class="btn-primary"
                    style="width: 100%; padding: 14px; border: none; border-radius: 25px; background-color: var(--light-pink); color: white; font-weight: bold; font-size: 1.1em; cursor: pointer;">Register</button>

            </form>

            <p class="footer-link" style="margin-top: 2em; text-align: center;">Already have an account? <a
                    href="/preLogin/login">Login page</a></p>
        </section>
    </main>

    <script src="/views/scripts/photo.js"></script>
</body>

</html>