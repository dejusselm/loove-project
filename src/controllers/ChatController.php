<?php
require_once ROOT_PATH . 'repositories/ChatsRepository.php';
require_once 'Controller.php';
require_once ROOT_PATH . 'models/Chat.php';
class ChatController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->requireAuth();
    }

    public function index(): array
    {
        $contacts = $this->chatRepo->getConversationsList($this->userId);

        $conversations = [];

        $totalUnreadChat = 0;


        foreach ($contacts as $contact) {
            $contactId = $contact['user']->getId();
            $isRejected = $this->matchRepo->isRejected($this->userId, $contactId);

            if ($isRejected && empty($contact['lastChat'])) {
                continue;
            }

            $unreadCount = 0;
            if (!$isRejected) {
                $unreadCount = $this->chatRepo->countNonRead($contactId, $this->userId);
                $totalUnreadChat += $unreadCount;
            }

            $conversations[] = [
                'profile' => $contact['user'],
                'unreadCount' => $unreadCount,
                'lastChat' => $contact['lastChat'],
                'isClosed' => $isRejected
            ];
        }
        return [
            'conversations' => $conversations,
            'totalUnread' => $totalUnreadChat
        ];
    }

    public function show(): array
    {
        $contactId = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$contactId) {
            header('Location: messages');
            exit;
        }

        $isRejected = $this->matchRepo->isRejected($this->userId, $contactId);
        if ($isRejected) {
            $contact = $this->userRepo->findOne($contactId);
            $_SESSION['flashMessage'] = "The conversation with 
            {$contact->getFirstName()} has ended (Profile unlinked or rejected).";
            header('Location: messages');
            exit;
        }

        $hasMatch = $this->matchRepo->isMatch($this->userId, $contactId);

        $chats = $this->chatRepo->getAllChats($this->userId, $contactId);

        if (!$hasMatch && (!$this->user->isMember()) && empty($chats)) {
            header('Location: messages');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['content'])) {
            $content = trim($_POST['content']);
            $receiverId = intval($_POST['receiver_id'] ?? $contactId);

            $this->chatRepo->sendMessage($content, $this->userId, $receiverId);
            $this->logger->log(
                LogType::ACTION,
                'Sent a chat to user ' . $receiverId,
                $this->userId
            );
            header("Location: chat?id=" . $contactId);
            exit;
        }

        $this->chatRepo->markAsRead($this->userId, $contactId);

        $chats = $this->chatRepo->getAllChats($this->userId, $contactId);

        return [
            'chats' => $chats,
            'contactId' => $contactId,
            'contact' => $this->userRepo->findOne($contactId),
            'user' => $this->userRepo->findOne($this->userId)
        ];
    }

    public function markAsDelivered(): void
    {
        $this->chatRepo->markAsDelivered($this->userId);
    }

    public function isRejected(int $contactId)
    {
        return $this->matchRepo->isRejected($this->userId, $contactId);
    }
}