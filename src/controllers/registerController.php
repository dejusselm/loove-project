<?php
require_once '../database/db.php';


if (isset($_POST['submitted'])) {
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $birthdate = $_POST["birthdate"];
    $gender = $_POST["gender"];
    $interest = $_POST["interest"];
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];


    $firstName = ucwords(strtolower($firstName), " -");
    $lastName = ucwords(strtolower($lastName), " -");

    // Verify age to prevent incorrect ages 
    if (isset($_POST["birthdate"])) {
        $currentDate = new DateTime();
        $usersBirthday = new DateTime($birthdate);

        $difference = $currentDate->diff($usersBirthday);
        $usersAge = $difference->y;

        if ($usersAge > 100 || $usersAge < 18) {
            $_SESSION["errorMessage"] = "Age is incorrect. ";
            header('Location: ./register.php');
            exit;
        }

    }

    $sql = new SqlConnect();

    // Verifies if an account with the given email already exists in the database
    $verifyQuery = "
    SELECT * FROM users
    WHERE email=:email";
    $verifyReq = $sql->db->prepare($verifyQuery);
    $verifyReq->execute(["email" => $email]);
    $emailData = $verifyReq->fetch(PDO::FETCH_ASSOC);
    if ($emailData) {
        // If there is data linked to this email
        $_SESSION['errorMessage'] = "This email adress is already registered.";
        // Prevents registering and redirects
        header('Location:  ./register.php');
        exit;
    }

    // Uploaded file for the user's avatar
    if (isset($_FILES["avatar"])) {
        // Prevents user from uploading files too heavy : php only allows 2Mo 
        if ($_FILES["avatar"]["error"] === UPLOAD_ERR_INI_SIZE) {
            $_SESSION['errorMessage'] = "The image is too heavy. Max size allowed is 2MB.";
            header('Location: ./register.php');
            exit;
        }

        $temporaryName = $_FILES["avatar"]["tmp_name"];
        $name = $_FILES["avatar"]["name"];
        // Gets the extension to move it later
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        // Generates a unique id for the file name to prevent issues
        $uniqueId = uniqid();
        $fileName = "{$uniqueId}_avatar.{$extension}";
        // Final location of the file
        $finalLocation = "../public/uploads/{$fileName}";
        // Moves the uploaded file to the images folder
        if (move_uploaded_file($temporaryName, $finalLocation)) {
        } else {
            // Error while moving file
            $_SESSION['registerError'] = "Failed to upload image.";
            header('Location: ./register.php');
            exit;
        }
    }

    // If not, registers the new account in the database
    $registerQuery = "INSERT INTO users (email, password, first_name, last_name, gender, birthdate, interest,avatar) 
    VALUES (:email, :password ,:firstName, :lastName, :gender, :birthdate, :interest,:avatar)";
    $registerReq = $sql->db->prepare($registerQuery);
    $registerReq->execute([
        'email' => $email,
        'password' => $password,
        'firstName' => $firstName,
        'lastName' => $lastName,
        'gender' => $gender,
        'birthdate' => $birthdate,
        'interest' => $interest,
        'avatar' => $fileName
    ]);

    header('Location:  ./login.php');
    exit;
}

?>