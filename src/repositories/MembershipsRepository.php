<?php
require_once ROOT_PATH . 'repositories/BaseRepository.php';

class MembershipsRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct();
    }


    public function setToPremium(int $userId, string $stripeId): bool
    {
        $startDate = date('Y-m-d');

        $query = "
            INSERT INTO memberships (type, user_id, debut_date, stripe_id) 
            VALUES ('premium',:user_id, :debut_date, :stripe_id )
        ";

        $stmt = $this->sql->db->prepare($query);
        return $stmt->execute([
            'user_id' => $userId,
            'debut_date' => $startDate,
            'stripe_id' => $stripeId
        ]);
    }


    public function isUserPremium(int $userId): bool
    {
        $query = "
        SELECT type FROM memberships 
        WHERE user_id = :user_id 
        LIMIT 1
        ";
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute(['user_id' => $userId]);

        $membership = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($membership && $membership['type'] === 'premium');
    }

    public function setToFree(int $userId)
    {
        $query = "
            INSERT INTO memberships (type, user_id) 
            VALUES ('free', :user_id)
        ";

        $stmt = $this->sql->db->prepare($query);
        return $stmt->execute([
            'user_id' => $userId
        ]);
    }

    public function getUserStripeId(int $userId): ?string
    {
        $query = "
        SELECT stripe_id FROM memberships
        WHERE user_id=:user_id
        ;";

        $stmt = $this->sql->db->prepare($query);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetch(PDO::FETCH_COLUMN) ?? null;
    }

    public function countTotalPremium()
    {
        $stmt = $this->sql->db->query("
        SELECT COUNT(*) 
        FROM memberships 
        WHERE type='premium'
        ");

        $count = $stmt->fetch(PDO::FETCH_COLUMN);
        return $count;
    }
}