<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_POST) {
    include "../controllers/registerController.php";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style/preLogin.css" rel="stylesheet">
    <title>Register</title>
</head>

<body>
    <header>
        <a href="./login.php"><img class="logo" src="./images/logoLoovePink.png"></a>
    </header>
    <main>
        <form id="regForm" action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">

            <h1>Register:</h1>
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
            <div class="tab">Login Info:
                <p><input type="email" name="email" placeholder="E-mail..." oninput="clearError(this)" required
                        pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"></p>
                Enter Password ( at least 12 characters ) :
                <p><input type="password" id="password" name="password" placeholder="Password..."
                        oninput="clearError(this)" required minlength="12">
                </p>
                <p><input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm password..."
                        oninput="clearError(this); validatePassword()" required minlength="12">
                    <span id="pswdErrorSpan"></span>
                </p>

            </div>

            <div class="tab">Name:
                <p><input type="text" name="firstName" placeholder="First name..." oninput="clearError(this)" required
                        pattern="[A-Za-zÀ-ÿ\s\-]{2,}">
                </p>
                <p><input type="text" name="lastName" placeholder="Last name..." oninput="clearError(this)" required
                        pattern="[A-Za-zÀ-ÿ\s\-]{2,}">
                </p>
                Birthday:
                <p><input type="date" name="birthdate" placeholder="dd" oninput="clearError(this)" value="2000-01-01"
                        min="1926-01-02" max="2008-12-31" required></p>
            </div>

            <div class="tab">
                Gender :
                <div>
                    <input type="radio" id="genderChoice1" name="gender" value="male" checked />
                    <label for="genderChoice1">Male</label>

                    <input type="radio" id="genderChoice2" name="gender" value="female" />
                    <label for="genderChoice2">Female</label>

                    <input type="radio" id="genderChoice3" name="gender" value="nonbinary" />
                    <label for="genderChoice3">Non binary</label>
                </div>
                Gender interest :
                <div>
                    <input type="radio" id="interestChoice1" name="interest" value="male" checked />
                    <label for="interestChoice1">Male</label>

                    <input type="radio" id="interestChoice2" name="interest" value="female" />
                    <label for="interestChoice2">Female</label>

                    <input type="radio" id="interestChoice3" name="interest" value="nonbinary" />
                    <label for="interestChoice3">Non binary</label>

                    <input type="radio" id="interestChoice4" name="interest" value="all" />
                    <label for="interestChoice4">All</label>
                </div>
            </div>


            <div class="tab">Add a profile picture (only under 2Mo) :
                <p><input type="file" id="avatar" name="avatar" accept="image/png, image/jpeg, image/webp"
                        onchange="handleAvatarChange(this)" required></p>
                <img id="avatarPreview" src="#" alt="Avatar Preview"
                    style="display: none; width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
            </div>

            <div style="overflow:auto;">
                <div style="float:right;">
                    <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
                    <button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
                </div>
            </div>

            <input type="hidden" value="1" name="submitted" />
            <div style="text-align:center;margin-top:40px;">
                <span class="step"></span>
                <span class="step"></span>
                <span class="step"></span>
                <span class="step"></span>
            </div>


        </form>
    </main>
    <p><a href="login.php">Login page</a></p>
    <script src="scripts/script.js"></script>
</body>