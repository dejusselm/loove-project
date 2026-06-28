<?php
require_once ROOT_PATH . 'database/db.php';
require_once ROOT_PATH . 'repositories/UserRepository.php';
require_once ROOT_PATH . 'repositories/SearchRepository.php';
require_once ROOT_PATH . 'repositories/HobbiesRepository.php';
require_once ROOT_PATH . 'repositories/ChatsRepository.php';
require_once ROOT_PATH . 'repositories/MatchesRepository.php';
require_once ROOT_PATH . 'repositories/MembershipsRepository.php';
require_once ROOT_PATH . 'repositories/ViewsRepository.php';
require_once ROOT_PATH . 'repositories/NotificationsRepository.php';
require_once ROOT_PATH . 'controllers/LogController.php';


class Controller
{
    protected UserRepository $userRepo;
    protected HobbiesRepository $hobbiesRepo;
    protected SearchRepository $searchRepo;
    protected MatchesRepository $matchRepo;
    protected ChatsRepository $chatRepo;
    protected MembershipsRepository $memberRepo;
    protected ViewsRepository $viewsRepo;
    protected NotificationsRepository $notifRepo;
    protected LogController $logger;
    protected ?int $userId;
    public User $user;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->userRepo = new UserRepository();

        $this->hobbiesRepo = new HobbiesRepository();

        $this->searchRepo = new SearchRepository();

        $this->matchRepo = new MatchesRepository();

        $this->chatRepo = new ChatsRepository();

        $this->memberRepo = new MembershipsRepository();

        $this->viewsRepo = new ViewsRepository();

        $this->notifRepo = new NotificationsRepository();

        $this->logger = new LogController();

        if (!empty($_SESSION['userId'])) {
            $this->userId = $_SESSION['userId'];
            $this->user = $this->userRepo->findOne((int) $_SESSION['userId']);
        }

    }
    protected function requireAuth(): void
    {
        if (!isset($_SESSION["userId"])) {
            header("Location: /");
            exit;
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireAuth();
        $user = $this->userRepo->findOne($_SESSION["userId"]);

        if (!$user || $user->getRole() !== 'admin') {
            header("Location: dashboard");
            exit;
        }
    }

}