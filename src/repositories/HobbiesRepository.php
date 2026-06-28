<?php
require_once ROOT_PATH . 'database/db.php';
require_once ROOT_PATH . 'models/Hobby.php';
require_once 'BaseRepository.php';

class HobbiesRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function all(): array
    {
        $hobbyArray = [];
        $query = 'SELECT * FROM hobbies';
        $result = $this->sql->db->query($query);
        foreach ($result as $hobby) {
            $hobbyArray[] = new Hobby($hobby['id'], $hobby['name']);
        }
        return $hobbyArray;
    }

    public function findById(int $hobbyId): string
    {
        $query = '
        SELECT name from hobbies
        WHERE id=:id';
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute(['id' => $hobbyId]);
        $name = $stmt->fetch(PDO::FETCH_ASSOC);
        return $name['name'];
    }
}