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
        <a href="/preLogin/home"><img class="logo" src="/views/images/logoLoovePink.png"></a>
    </header>
    <main>
        <section>
            <form id="regForm" action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
                <div class="registerHead">
                    <h1>Register :</h1>
                    <div style="text-align:center;margin-top:40px;flex-direction:row;">
                        <span class="step"></span>
                        <span class="step"></span>
                        <span class="step"></span>
                        <span class="step"></span>
                    </div>
                </div>
                <?php if (isset($_SESSION['errorMessage'])): ?>
                    <div style="color:red;">
                        <span>
                            <?php
                            echo $_SESSION['errorMessage'];
                            unset($_SESSION['errorMessage']);
                            ?>
                        </span>
                        <button type="button" onclick="this.parentElement.style.display='none';">
                            x
                        </button>
                    </div>
                <?php endif; ?>

                <div class="tab">Email :
                    <p><input type="email" name="email" placeholder="E-mail..." oninput="clearError(this)" required
                            pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"></p>
                    Password ( at least 12 characters ) :
                    <p><input type="password" id="password" name="password" placeholder="Password..."
                            oninput="clearError(this)" required minlength="12">
                    </p>
                    <p><input type="password" id="confirmPassword" name="confirmPassword"
                            placeholder="Confirm password..." oninput="clearError(this); validatePassword('register)"
                            required minlength="12">
                        <span id="pswdErrorSpan"></span>
                    </p>

                </div>

                <div class="tab">Name :
                    <p><input type="text" name="firstName" placeholder="First name..." oninput="clearError(this)"
                            required pattern="[A-Za-zÀ-ÿ\s\-]{2,}">
                    </p>
                    <p><input type="text" name="lastName" placeholder="Last name..." oninput="clearError(this)" required
                            pattern="[A-Za-zÀ-ÿ\s\-]{2,}">
                    </p>
                    Birthday :
                    <p><input type="date" name="birthdate" placeholder="dd" oninput="clearError(this)"
                            value="2000-01-01" min="1926-01-02" max="2008-12-31" required></p>
                </div>
                <div class="tab">
                    <span class="field-title">Gender :</span>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" id="genderChoice1" name="gender" value="male" checked />
                            <label for="genderChoice1">Male</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="genderChoice2" name="gender" value="female" />
                            <label for="genderChoice2">Female</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="genderChoice3" name="gender" value="non-binary" />
                            <label for="genderChoice3">Non-binary</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="genderChoice4" name="gender" value="other" />
                            <label for="genderChoice4">Other</label>
                        </div>
                    </div>

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
                </div>

                <div class="tab">Profile picture (only under 2Mo) :
                    <p><input type="file" id="avatar" name="avatar" accept="image/png, image/jpeg, image/webp"
                            onchange="handlephotoChange(this,'avatar')" required></p>
                    <img id="avatarPreview" src="#" alt="Avatar Preview"
                        style="display: none; width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                </div>

                <div style="overflow:auto;">
                    <div class="container" style="width:100%">
                        <button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
                        <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
                    </div>
                </div>

                <input type="hidden" value="1" name="submitted" />

            </form>
        </section>
    </main>
    <p><a href="/preLogin/login">Login page</a></p>
    <script src="/views/scripts/script.js"></script>
    <script src="/views/scripts/photo.js"></script>
</body>