<?php
require_once 'BaseRepository.php';

class ViewsRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function logView(int $profileId, int $viewerId): void
    {
        if ($profileId === $viewerId) {
            return;
        }

        $checkQuery = "
        SELECT COUNT(*) 
        FROM profile_views 
        WHERE profile_id = :profileId 
          AND viewer_id = :viewerId 
          AND DATE(viewed_at) = CURRENT_DATE
    ";

        $checkStmt = $this->sql->db->prepare($checkQuery);
        $checkStmt->execute([
            'profileId' => $profileId,
            'viewerId' => $viewerId
        ]);

        $alreadyViewedToday = $checkStmt->fetch(PDO::FETCH_COLUMN) > 0;

        if ($alreadyViewedToday) {
            return;
        }

        $query = "
        INSERT INTO profile_views (profile_id, viewer_id) 
        VALUES (:profileId, :viewerId)
    ";

        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'profileId' => $profileId,
            'viewerId' => $viewerId
        ]);
    }

    public function getTotalViews(int $userId): int
    {
        $query = "SELECT COUNT(*) FROM profile_views WHERE profile_id = :userId";
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute(['userId' => $userId]);

        return (int) $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function getProfileViewers(int $userId): array
    {
        $query = "
        SELECT u.id, u.first_name, u.avatar, pv.viewed_at 
        FROM profile_views pv
        JOIN users u ON pv.viewer_id = u.id
        WHERE pv.profile_id = :userId
        ORDER BY pv.viewed_at DESC
        LIMIT 10
    ";

        $stmt = $this->sql->db->prepare($query);
        $stmt->execute(['userId' => $userId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        foreach ($rows as $row) {
            $users[] = $this->listToUser($row);
        }

        return $users;
    }
}