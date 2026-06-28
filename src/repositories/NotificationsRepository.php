<?php

class NotificationsRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct();
    }
    public function registerNotification(int $userId, string $message, ?string $type)
    {
        $query = "
        INSERT INTO notifications (user_id, message, type) 
        VALUES (:userId, :message,:type)
        ";

        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'userId' => $userId,
            'message' => $message,
            'type' => $type
        ]);
    }

    public function getNonRead(int $userId): ?array
    {
        $query = "SELECT id, message, type FROM notifications WHERE user_id = :userId AND is_read = 0";
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute(['userId' => $userId]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($notifications)) {
            $updateQuery = "
        UPDATE notifications 
        SET is_read = 1 
        WHERE user_id = :userId AND is_read = 0";
            $updateStmt = $this->sql->db->prepare($updateQuery);
            $updateStmt->execute(['userId' => $userId]);
        }

        return $notifications;
    }

    public function notificationExists($receiverId, $message): bool
    {
        $query = "
        SELECT COUNT(*) FROM notifications
        WHERE user_id=:userId AND is_read=0 AND message=:message;
        ";

        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'userId' => $receiverId,
            'message' => $message
        ]);

        $count = $stmt->fetch(PDO::FETCH_COLUMN);

        return $count;
    }
}