<?php
require_once ROOT_PATH . 'repositories/MatchesRepository.php';
class MatchController extends Controller
{


    public function __construct()
    {
        parent::__construct();

        $this->requireAuth();
    }
    public function registerInteraction(InteractionType $type, int $profileId)
    {
        $isMatch = $this->matchRepo->registerInteraction(
            $type,
            $this->userId,
            $profileId
        );
        if ($isMatch) {
            $this->sendMatchData($profileId);
        }
        unset($_SESSION['currentProfileId']);
    }

    public function countTodaySwipes(): int
    {
        return $this->matchRepo->countTodaySwipes($this->userId);
    }

    public function handleInteraction()
    {
        $action = $_GET['action'];
        $profileId = (int) $_GET['profile_id'];

        if ($action === 'like') {
            $this->registerInteraction(
                InteractionType::LIKE,
                $profileId
            );
            $this->logger->log(
                LogType::ACTION,
                "Liked user " . $profileId . "'s profile.",
                $this->userId
            );
        } elseif ($action === 'dislike') {
            $this->registerInteraction(
                InteractionType::REJECT,
                $profileId
            );
            $this->logger->log(
                LogType::ACTION,
                "Rejected user " . $profileId,
                $this->userId
            );
            $this->matchRepo->cleanAllAfterReject($this->userId, $profileId);
        }

        header("Location: /user/dashboard");
        exit;
    }

    public function sendMatchData(int $profileId)
    {
        $profile = $this->userRepo->findOne($profileId);

        $subject = "Loove - You've got a new match !";
        $message = "You and " . $this->user->getFirstName() .
            " matched ! Go start a conversation !";
        $headers = "From: no-reply@loove.local";

        mail($profile->getEmail(), $subject, $message, $headers);

        $this->logger->log(
            LogType::INFO,
            "User " . $this->userId . " and user " . $profileId . " matched !",
            $this->userId
        );

        $this->notifRepo->registerNotification($profileId, $message, 'match');
    }

    public function hasUserInteracted(int $profileId): bool
    {
        return $this->matchRepo->hasInteraction($this->userId, $profileId);
    }

    public function isRejected(int $profileId): bool
    {
        return $this->matchRepo->isRejected($this->userId, $profileId);
    }
}