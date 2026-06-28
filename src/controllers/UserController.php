<?php
require_once 'Controller.php';
require_once ROOT_PATH . 'repositories/UserRepository.php';
require_once ROOT_PATH . 'repositories/HobbiesRepository.php';
require_once 'GeoLocalisation.php';
class UserController extends Controller
{
    private GeoLocalisation $geoService;
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();

        $this->geoService = new GeoLocalisation();

        if (
            isset($_POST['descriptionModified'])
            || isset($_POST['featuresModified'])
        ) {
            $this->updateFeatures();
        }
        if (isset($_POST['locationModified'])) {
            $this->updateLocation();
        }

        if (isset($_POST['hobbiesModified'])) {
            $this->updateHobbies();
        }
        if (isset($_POST['deleteAccount'])) {
            $this->deleteAccount();
        }

        if (isset($_POST['preferencesModified'])) {
            $this->updatePreferences();
        }

        if (isset($_POST['photoModified'])) {
            $this->updatePhoto();
        }
    }

    public function profileData(): array
    {
        return [
            'user' => $this->user,
            'userHobbies' => $this->userRepo->findUserHobbies(
                $this->userId
            ),
            'allHobbies' => $this->hobbiesRepo->all(),
            'userPhotos' => $this->user->getPhotos(),
            'profiles' => $this->getProfiles()
        ];
    }

    private function getProfiles(): array
    {
        $preferences = [
            'interest' => $this->user->getInterest(),
            'relation' => $this->user->getRelationship()
        ];
        $array = $this->searchRepo->getDashboardProfiles(
            $this->userId,
            $preferences
        );

        return $array;
    }

    public function updateFeatures()
    {
        unset($_SESSION["errorMessage"]);

        if (isset($_POST['description'])) {
            $newDescription = substr(trim($_POST['description']), 0, 200);
        } else {
            $newDescription = $this->user->getDescription() ?? '';
        }

        $this->userRepo->updateFeatures(
            $this->userId,
            $newDescription
        );

        $this->logger->log(LogType::ACTION, "Updated their profile", $this->userId);
        header('Location: profile');
        exit;
    }

    public function updateLocation()
    {
        unset($_SESSION["errorMessage"]);
        $cityInput = trim($_POST['location']);
        $coords = $this->geoService->getCoordinates($cityInput);

        if ($coords) {
            $latitude = $coords['latitude'];
            $longitude = $coords['longitude'];
            $this->userRepo->updateLocation(
                $this->userId,
                $longitude,
                $latitude,
                $cityInput
            );
            $this->logger->log(LogType::ACTION, "Updated their profile", $this->userId);
            header("Location: profile");
            exit;
        } else {
            header("Location: profile");
            exit;
        }
    }

    public function updatePreferences()
    {
        unset($_SESSION["errorMessage"]);
        $relation = $_POST['relation'] ?? null;
        $interest = $_POST['interest'] ?? null;

        if ($relation && $interest) {
            $this->userRepo->updatePreferences(
                $this->userId,
                [
                    'relation' => $relation,
                    'interest' => $interest
                ]
            );
            $this->logger->log(LogType::ACTION, "Updated their profile", $this->userId);
            header('Location: profile');
            exit;
        }
    }
    public function updateHobbies()
    {
        unset($_SESSION["errorMessage"]);
        if (is_array($_POST['hobbies'])) {
            $selectedHobbies = $_POST['hobbies'];
            $this->userRepo->updateHobbies(
                $this->userId,
                $selectedHobbies
            );
            $this->logger->log(LogType::ACTION, "Updated their profile", $this->userId);
            header('Location: profile');
            exit;
        }
    }

    public function updatePhoto()
    {
        if (isset($_FILES["photo"])) {
            // Prevents user from uploading files too heavy : php only allows 2Mo 
            if ($_FILES["photo"]["error"] === UPLOAD_ERR_INI_SIZE) {
                $_SESSION["errorMessage"] = 'The image is too heavy. Max size allowed is 2MB.';
                header("Location: profile");
                exit;
            }

            $currentPhotosCount = $this->userRepo->countPhotos($this->userId);

            if ($currentPhotosCount >= 4) {
                header('Location: /user/parameters?error=max_photos_reached');
                exit;
            }

            $temporaryName = $_FILES["photo"]["tmp_name"];
            $name = $_FILES["photo"]["name"];
            // Gets the extension to move it later
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            // Generates a unique id for the file name to prevent issues
            $uniqueId = uniqid();
            $fileName = "{$uniqueId}_photo.{$extension}";

            $this->userRepo->updatePhoto($this->userId, $fileName);
            // Final location of the file
            $finalLocation = ROOT_PATH . "/public/uploads/{$fileName}";
            // Moves the uploaded file to the images folder
            if (move_uploaded_file($temporaryName, $finalLocation)) {
                $this->logger->log(LogType::ACTION, "Updated their profile", $this->userId);
                header("Location: profile");
                exit;
            } else {
                $_SESSION["errorMessage"] = 'Failed to upload image.';
                header("Location: profile");
                exit;
            }
        }
    }

    public function deleteAccount()
    {
        $user = $this->userRepo->findOne($this->userId);
        if ($user) {
            $avatar = $user->getAvatar();
            if ($avatar) {
                $filePath = ROOT_PATH . "/public/uploads/{$avatar}";
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                if ($user->getPhotos()) {
                    foreach ($user->getPhotos() as $photo) {
                        $filePath = ROOT_PATH .
                            "/public/uploads/{$photo['photo_path']}";
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }


                $to = $user->getEmail();
                $subject = "Account deletion";
                $message = "Your account has correctly been deleted";
                $headers = "From: no-reply@loove.local";

                mail($to, $subject, $message, $headers);

                $this->userRepo->deleteUser($this->userId);
            }
        }
        $_SESSION["errorMessage"] = "Account deleted.";
        unset($_SESSION["userId"]);
        unset($_SESSION["role"]);
        header('Location: /');
        exit;

    }

    public function getTargetProfileData(?int $profileId): ?array
    {

        if (!$profileId || $profileId === $this->userId) {
            header('Location: dashboard');
            exit;
        }

        $profile = $this->userRepo->findOne($profileId);

        if (
            !$profile || !$profile->getActive() ||
            $profile->getRole() === 'admin'
        ) {
            header('Location: dashboard');
            exit;
        }

        return [
            'profile' => $profile,
            'hobbies' => $this->userRepo->findUserHobbies($profileId),
            'photos' => $profile->getPhotos()
        ];
    }

    public function registerView(int $profileId)
    {
        $this->viewsRepo->logView($profileId, $this->userId);
    }

    public function registerNotification(string $message, ?string $type)
    {
        $this->notifRepo->registerNotification($this->userId, $message, $type);
    }

    public function registerReport()
    {
        if (isset($_POST['submitReport'])) {
            $reportedId = (int) $_POST['reported_user_id'];
            $reason = substr(htmlspecialchars($_POST['report_reason']), 0, 150) ?? 'No reason specified.';
            if (!$this->userRepo->wasReportedToday($this->userId, $reportedId)) {
                $this->userRepo->registerReport($this->userId, $reason, $reportedId, );
                $_SESSION['flashMessage'] = "Thank you. The report has been sent to administrators.";
            } else {
                $_SESSION['flashMessage'] = "You already reported this account today.";
            }

            header('Location: dashboard');
            exit;
        }

    }

}

