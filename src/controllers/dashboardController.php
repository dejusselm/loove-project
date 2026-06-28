<?php
require_once 'Controller.php';
require_once ROOT_PATH . 'repositories/UserRepository.php';
require_once ROOT_PATH . 'models/User.php';

class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->requireAuth();

        if (!isset($_SESSION['searchSeed'])) {
            $_SESSION['searchSeed'] = rand(1, 9999);
        }

    }

    public function index()
    {
        if ($this->user->getActive() == 0) {
            $message = "Account deactivated : ";
            unset($_SESSION["userId"]);
            unset($_SESSION["role"]);
            $_SESSION["errorMessage"] = $message;
            header('Location: /');
            exit;
        }
        if (isset($_GET['action']) && $_GET['action'] === 'shuffle') {
            $_SESSION['searchSeed'] = rand(1, 9999);

            header('Location: dashboard');
            exit;
        }
    }

    public function currentProfile(array $profiles): ?User
    {
        if (isset($_SESSION['currentProfileId'])) {
            foreach ($profiles as $profile) {
                if ($profile->getId() == $_SESSION['currentProfileId']) {
                    return $profile;
                } else {
                    $_SESSION['currentProfileId'] = $profiles[0];
                    return $profiles[0];
                }
            }
        }
    }

}
