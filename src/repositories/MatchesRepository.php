<?php
require_once ROOT_PATH . 'database/db.php';
require_once ROOT_PATH . 'models/Interaction.php';
require_once ROOT_PATH . 'models/Enums/InteractionType.php';
require_once 'BaseRepository.php';

class MatchesRepository extends BaseRepository
{
    public function registerInteraction(
        InteractionType $type,
        int $userId,
        int $profileId
    ): bool {
        $query = "
            INSERT INTO matches (type, user_id, profile_id) 
            VALUES (:type, :user_id, :profile_id)
            ON DUPLICATE KEY UPDATE type = :type;
        ";
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'type' => $type->value,
            'user_id' => $userId,
            'profile_id' => $profileId
        ]);

        if ($this->isDoubleLike($userId, $profileId)) {
            $this->updateToMatch($userId, $profileId);
            $_SESSION['flashMessage'] = "It's a Match ! ";
            return true;
        }
        return false;
    }

    public function hasInteraction(int $userId, int $profileId): bool
    {
        $query = "
        SELECT COUNT(*) FROM matches
        WHERE (user_id=:userId AND profile_id=:profileId);
        ";
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'userId' => $userId,
            'profileId' => $profileId
        ]);
        $count = $stmt->fetch(PDO::FETCH_COLUMN);
        return $count > 0;
    }

    public function isMatch(int $firstUserId, int $secondUserId): bool
    {
        $query = "
            SELECT COUNT(*) as match_count
            FROM matches 
            WHERE (user_id = :userA AND profile_id = :userB AND type = 'match')
               OR (user_id = :userB2 AND profile_id = :userA2 AND type = 'match')
        ";
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'userA' => $firstUserId,
            'userB' => $secondUserId,
            'userA2' => $firstUserId,
            'userB2' => $secondUserId
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $result['match_count'] === 2;
    }
    private function isDoubleLike(int $firstUserId, int $secondUserId): bool
    {
        $query = "
            SELECT COUNT(*) as match_count
            FROM matches 
            WHERE (user_id = :userA AND profile_id = :userB AND type = 'like')
               OR (user_id = :userB2 AND profile_id = :userA2 AND type = 'like')
        ";
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'userA' => $firstUserId,
            'userB' => $secondUserId,
            'userA2' => $firstUserId,
            'userB2' => $secondUserId
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $result['match_count'] === 2;
    }

    public function isRejected(int $userId, int $contactId): bool
    {
        $query = "
            SELECT COUNT(*) FROM matches
            WHERE (user_id = :contactId AND profile_id = :userId AND type='reject')
            OR (user_id = :userId AND profile_id = :contactId AND type='reject')
        ";

        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'contactId' => $contactId,
            'userId' => $userId
        ]);
        $count = $stmt->fetch(PDO::FETCH_COLUMN);
        return $count > 0;
    }

    private function updateToMatch(int $firstUserId, int $secondUserId): void
    {
        $query = "
        UPDATE matches
        SET type='match' 
        WHERE (user_id=:user_id AND profile_id=:profile_id)
        OR (user_id=:profile_id AND profile_id=:user_id)
        ;";
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'user_id' => $firstUserId,
            'profile_id' => $secondUserId
        ]);
    }

    public function countTodaySwipes(int $userId): int
    {
        $query = "
        SELECT COUNT(*) 
        FROM matches 
        WHERE user_id = :user_id 
          AND DATE(created_at) = CURDATE()
    ";
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute(['user_id' => $userId]);

        return (int) $stmt->fetchColumn();
    }

    public function cleanAllAfterReject(int $userId, int $profileId): void
    {
        try {
            $this->sql->db->beginTransaction();

            $queryCleanLikes = "
            DELETE FROM matches 
            WHERE user_id = :profileId 
              AND profile_id = :userId 
              AND type = 'like'
            ";
            $stmtLikes = $this->sql->db->prepare($queryCleanLikes);
            $stmtLikes->execute([
                'userId' => $userId,
                'profileId' => $profileId
            ]);

            $this->sql->db->commit();
        } catch (\Exception $e) {
            $this->sql->db->rollBack();
        }
    }
    public function countTotalMatches()
    {
        $stmt = $this->sql->db->query("
        SELECT COUNT(*) 
        FROM matches
        WHERE type='match'
        ");

        $count = $stmt->fetch(PDO::FETCH_COLUMN);
        return $count / 2;
    }
}