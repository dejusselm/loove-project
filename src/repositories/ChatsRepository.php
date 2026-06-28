<?php
require_once 'BaseRepository.php';
require_once ROOT_PATH . 'models/Chat.php';
require_once ROOT_PATH . 'models/Enums/ChatStatus.php';
class ChatsRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function registerMessage(Chat $chat): void
    {
        $query = "INSERT INTO messages(content,sender_id,recipient_id,status,date)
            VALUES(:content,:sender_id,:recipient_id,:status,:date)";

        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'content' => $chat->getContent(),
            'sender_id' => $chat->getSenderId(),
            'recipient_id' => $chat->getRecipientId(),
            'status' => $chat->getStatus()->value,
            'date' => $chat->getDate()->format('Y-m-d H:i:s')
        ]);
    }

    public function getAllChats(int $firstUserId, int $secondUserId): array
    {
        $query = "SELECT * FROM messages
        WHERE (sender_id=:userA AND recipient_id=:userB)
        OR (sender_id=:userB AND recipient_id=:userA)
        ORDER BY date ASC;
        ";
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'userA' => $firstUserId,
            'userB' => $secondUserId
        ]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $chats = [];

        foreach ($results as $result) {
            $chat = new Chat(
                $result['id'],
                $this->decryptMessage($result['content']),
                $result['sender_id'],
                $result['recipient_id'],
                ChatStatus::from($result['status']),
                new DateTime($result['date'])
            );
            $chats[] = $chat;
        }

        return $chats;
    }

    public function getConversationsList(int $userId): array
    {
        $query = "
            SELECT DISTINCT u.*,
               (SELECT id FROM messages 
                WHERE (sender_id = u.id AND recipient_id = :userId) 
                    OR (sender_id = :userId AND recipient_id = u.id)
                ORDER BY date DESC LIMIT 1) AS last_message_id,

                (SELECT date FROM messages 
                WHERE (sender_id = u.id AND recipient_id = :userId) 
                    OR (sender_id = :userId AND recipient_id = u.id)
                ORDER BY date DESC LIMIT 1) AS last_message_date
            FROM users u
            WHERE u.id != :userId
            AND (
                u.id IN(SELECT profile_id FROM matches WHERE user_id = :userId AND type='match'
                )
                OR u.id IN (
                    SELECT user_id FROM matches WHERE profile_id = :userId AND type='match'
                )
                OR u.id IN (
                    SELECT sender_id FROM messages WHERE recipient_id = :userId
                )      
                OR u.id IN (
                    SELECT recipient_id FROM messages WHERE sender_id = :userId
                )
            )
            AND u.active = 1 
            ORDER BY last_message_date DESC
        ";

        $stmt = $this->sql->db->prepare($query);
        $stmt->execute(['userId' => $userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $conversations = [];
        foreach ($results as $result) {
            $lastChatObject = null;

            if (!empty($result['last_message_id'])) {
                $lastChatObject = $this->findChatById((int) $result['last_message_id']);
            }

            $conversations[] = [
                'user' => $this->listToUser($result),
                'lastChat' => $lastChatObject
            ];
        }
        return $conversations;
    }

    public function countNonRead(int $senderId, int $recipientId)
    {
        $query = "
        SELECT COUNT(*) FROM messages
        WHERE sender_id = :senderId 
        AND recipient_id=:recipientId
        AND status != 'read';
    ";
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'senderId' => $senderId,
            'recipientId' => $recipientId
        ]);
        return (int) $stmt->fetchColumn();
    }

    public function sendMessage(string $content, int $senderId, int $recipientId): void
    {
        $encryptedContent = $this->encryptMessage($content);

        $query = "
            INSERT INTO messages (content, sender_id,recipient_id)
            VALUES(:content, :senderId, :recipientId);
        ";
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'content' => $encryptedContent,
            'senderId' => $senderId,
            'recipientId' => $recipientId
        ]);
    }

    public function markAsRead(int $userId, int $contactId): void
    {
        $query = "
        UPDATE messages 
        SET status = 'read' 
        WHERE sender_id = :contactId 
          AND recipient_id = :currentUserId 
          AND status != 'read'
    ";

        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'contactId' => $contactId,
            'currentUserId' => $userId
        ]);
    }

    public function markAsDelivered(int $userId): void
    {
        $query = "
        UPDATE messages 
        SET status = 'delivered' 
        WHERE recipient_id = :userId
          AND status = 'sent'
    ";

        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'userId' => $userId
        ]);
    }

    public function findChatById(int $id): ?Chat
    {
        $query = "SELECT * FROM messages WHERE id = :id";
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        $chat = new Chat(
            $result['id'],
            $this->decryptMessage($result['content']),
            $result['sender_id'],
            $result['recipient_id'],
            ChatStatus::from($result['status']),
            new DateTime($result['date'])
        );

        return $chat;
    }

    private function encryptMessage(string $content): string
    {
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivLength);

        $encryptedText = openssl_encrypt($content, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);

        return base64_encode($iv) . '.' . $encryptedText;
    }

    private function decryptMessage(string $encryptedData): string
    {
        if (!str_contains($encryptedData, '.')) {
            return $encryptedData;
        }

        list($encodedIv, $encryptedText) = explode('.', $encryptedData, 2);
        $iv = base64_decode($encodedIv);

        $decrypted = openssl_decrypt($encryptedText, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);

        return $decrypted ?: '[Corrupted chat]';
    }
}