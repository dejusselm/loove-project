<?php
require_once ROOT_PATH . '/database/db.php';

abstract class BaseRepository
{
    protected SqlConnect $sql;

    public function __construct()
    {
        $this->sql = new SqlConnect();
    }

    protected function listToUser(array $list): User
    {
        return new User(
            $list['id'] ?? 0,
            $list['email'] ?? '',
            $list['password'] ?? '',
            $list['gender'] ?? 'other',
            $list['last_name'] ?? '',
            new DateTime($list['birthdate'] ?? 'now'),
            $list['first_name'] ?? '',
            $list['role'] ?? 'user',
            $list['interest'] ?? 'all',
            $list['active'] ?? 1,
            $list['avatar'] ?? 'default.jpg',
            $list['description'] ?? '',
            $list['relationship'] ?? 'anything',
            $list['longitude'] ?? 0.0,
            $list['latitude'] ?? 0.0,
            $list['city'] ?? '',
            $list['features'] ?? null,
        );
    }
}