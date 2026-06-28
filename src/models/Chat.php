<?php
require_once 'Enums/ChatStatus.php';
require_once ROOT_PATH . 'repositories/UserRepository.php';

class Chat
{
    private int $id;
    public string $content;
    public int $senderId;
    public int $recipientId;
    public ChatStatus $status;
    public DateTime $date;

    private UserRepository $userRepo;

    public function __construct(
        int $id,
        string $content,
        int $senderId,
        int $recipientId,
        ChatStatus $status,
        DateTime $date
    ) {
        $this->id = $id;
        $this->content = $content;
        $this->senderId = $senderId;
        $this->recipientId = $recipientId;
        $this->status = $status;
        $this->date = $date;
        $this->userRepo = new UserRepository();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getSenderId(): int
    {
        return $this->senderId;
    }

    public function getRecipientId(): int
    {
        return $this->recipientId;
    }
    public function getSender(): User
    {
        return $this->userRepo->findOne($this->senderId);
    }

    public function getRecipient(): User
    {
        return $this->userRepo->findOne($this->recipientId);
    }

    public function getStatus(): ChatStatus
    {
        return $this->status;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }
    public function getFormattedDate(): string
    {
        $interval = (new DateTime())->diff($this->getDate());

        if ($interval->days >= 7) {
            return $this->date->format('d/m');
        }

        if ($interval->days >= 1) {
            return $this->date->format('l');
        }
        return $this->date->format('H:i');
    }
}