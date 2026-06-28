<?php
require_once 'Controller.php';
require_once ROOT_PATH . 'repositories/UserRepository.php';
require_once ROOT_PATH . 'models/enums/Interests.php';
require_once ROOT_PATH . 'models/enums/Genders.php';

class AuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if (isset($_POST['passwordModified'])) {

            $currentPwd = $_POST['currentPassword'] ?? '';
            $newPwd = $_POST['password'] ?? '';

            if (!empty($newPwd) && $this->isPasswordCorrect($currentPwd)) {
                $this->updatePassword($newPwd);

                $_SESSION['passwordMessage'] = "Password updated successfully!";
                header('Location: /parameters');
                exit;
            } else {
                $_SESSION['errorMessage'] = "Current password is incorrect.";
            }
        }
    }
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST["email"]);
            $password = trim($_POST["password"]);

            $user = $this->userRepo->findByEmail($email);

            if ($user) {
                if (password_verify($password, $user->getPassword())) {
                    $_SESSION["userId"] = $user->getId();
                    $_SESSION["role"] = $user->getRole();

                    if ($_SESSION["role"] == "admin") {
                        $this->logger->log(
                            LogType::ACTION,
                            " User logged in.",
                            $user->getId()
                        );
                        header("Location: /admin/adminDashboard");
                        exit;
                    } else if ($user->getActive() == 0) {
                        $message = "Account deactivated.";
                        $_SESSION["errorMessage"] = $message;
                        header("Location: /preLogin/login");
                        exit;
                    }
                    $this->logger->log(
                        LogType::ACTION,
                        "User logged in.",
                        $user->getId()
                    );
                    header('Location: /user/dashboard');
                    exit;
                } else {
                    $_SESSION['errorMessage'] = "Wrong email or password.";
                    header("Location: /preLogin/login");
                    exit;
                }
            } else {
                $_SESSION['errorMessage'] = "Wrong email or password.";
                header("Location: /preLogin/login");
                exit;
            }
        }
    }

    public function register(): void
    {
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

            $genderEnum = Genders::tryFrom($gender ?? '');
            if (!$genderEnum) {
                $this->errorRedirection(
                    'Invalid gender selection',
                    '/preLogin/register'
                );
            }

            $interestEnum = Interests::tryFrom($interest ?? '');
            if (!$interestEnum) {
                $this->errorRedirection(
                    'Invalid interest selection',
                    '/preLogin/register'
                );
            }

            if (isset($_POST["birthdate"])) {
                $currentDate = new DateTime();
                $usersBirthday = new DateTime($birthdate);

                $difference = $currentDate->diff($usersBirthday);
                $usersAge = $difference->y;

                if ($usersAge > 100 || $usersAge < 18) {
                    $this->errorRedirection(
                        'Age is incorrect',
                        '/preLogin/register'
                    );
                }
            }

            if ($this->userRepo->emailExists($email)) {
                $this->errorRedirection(
                    'This email adress is already registered.',
                    '/preLogin/register'
                );
            }
            if (isset($_FILES["avatar"])) {
                // Prevents user from uploading files too heavy : php only allows 2Mo 
                if ($_FILES["avatar"]["error"] === UPLOAD_ERR_INI_SIZE) {
                    $this->errorRedirection(
                        'The image is too heavy. Max size allowed is 2MB.',
                        '/preLogin/register'
                    );
                }

                $temporaryName = $_FILES["avatar"]["tmp_name"];
                $name = $_FILES["avatar"]["name"];
                // Gets the extension to move it later
                $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                // Generates a unique id for the file name to prevent issues
                $uniqueId = uniqid();
                $fileName = "{$uniqueId}_avatar.{$extension}";
                // Final location of the file
                $finalLocation = ROOT_PATH . "/public/uploads/{$fileName}";
                // Moves the uploaded file to the images folder
                if (move_uploaded_file($temporaryName, $finalLocation)) {
                } else {
                    // Error while moving file
                    $this->errorRedirection(
                        'Failed to upload image.',
                        '/preLogin/register'
                    );
                }
            }
            $this->emailVerification();
        }
    }

    private function emailVerification()
    {

        $verificationCode = strval(rand(100000, 999999));

        $_SESSION['temp_user_data'] = $_POST;
        $_SESSION['temp_user_avatar'] = $_FILES['avatar'] ?? null;
        $_SESSION['email_verification_code'] = $verificationCode;

        mail($_POST['email'], "Your verification code", "Your code is: "
            . $verificationCode);

        header('Location: verifyEmail');
        exit;
    }

    public function verifyCode()
    {
        if (isset($_GET['action']) && $_GET['action'] === 'resend') {
            if (isset($_SESSION['temp_user_data']['email'])) {
                $email = $_SESSION['temp_user_data']['email'];
                $newCode = strval(rand(100000, 999999));

                $_SESSION['email_verification_code'] = $newCode;

                $to = $email;
                $subject = "Verification code";
                $message = "Your new code is " . $newCode;
                $headers = "From: no-reply@loove.local";

                mail($to, $subject, $message, $headers);

                $_SESSION['successMessage'] = "A new code has been sent to "
                    . htmlspecialchars($email);
            } else {
                $_SESSION['errorMessage'] = "Session expired. 
                Please register again.";
                header('Location: /preLogin/register');
                exit;
            }

            header('Location: verifyEmail');
            exit;
        }

        if (isset($_POST['submitVerification'])) {
            $enteredCode = $_POST['verification_code'] ?? '';
            $correctCode = $_SESSION['email_verification_code'] ?? null;

            if ($correctCode && $enteredCode === $correctCode) {
                $userData = $_SESSION['temp_user_data'];

                $this->userRepo->register([
                    'email' => $userData['email'],
                    'password' => $userData['password'],
                    'firstName' => $userData['firstName'],
                    'lastName' => $userData['lastName'],
                    'gender' => $userData["gender"] ?? null,
                    'birthdate' => $userData['birthdate'],
                    'interest' => $userData["interest"] ?? null,
                    'avatar' => $userData['fileName'] ?? 'default.png'
                ]);
                $to = $userData['email'];
                $subject = "Welcome to Loove!";
                $message = "Hello " . $userData['firstName'] .
                    ",\nYour account has been created successfully.";
                $headers = "From: no-reply@loove.local";

                mail($to, $subject, $message, $headers);

                unset($_SESSION['temp_user_data']);
                unset($_SESSION['temp_user_avatar']);
                unset($_SESSION['email_verification_code']);

                $this->logger->log(
                    LogType::ACTION,
                    'New account created.',
                    null
                );

                header('Location: /preLogin/login');
                exit;
            } else {
                $_SESSION['errorMessage'] = "Invalid verification code. 
                Please try again.";
            }
        }
    }

    private function errorRedirection(string $error, $location)
    {
        $_SESSION["errorMessage"] = $error;
        header("Location: " . $location);
        exit;
    }


    public function logout(): void
    {
        $this->logger->log(
            LogType::ACTION,
            "Logged out.",
            $this->userId
        );
        $_SESSION = array();
        session_destroy();
        header('Location: /');
        exit;
    }

    private function isPasswordCorrect(string $password): bool
    {
        return password_verify($password, $this->user->getPassword());
    }

    public function updatePassword(string $password)
    {
        $this->userRepo->updatePassword(
            $this->userId,
            password_hash($password, PASSWORD_DEFAULT)
        );

        $this->logger->log(
            LogType::ACTION,
            "Changed their password",
            $this->userId
        );
    }
}