<?php
require_once ROOT_PATH . 'database/db.php';
require_once ROOT_PATH . 'models/User.php';
require_once ROOT_PATH . 'models/Hobby.php';
require_once 'BaseRepository.php';
require_once ROOT_PATH . 'controllers/GeoLocalisation.php';

class UserRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function findOne(int $id): ?User
    {
        $stmt = $this->sql->db->prepare("SELECT * from users where id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? $this->listToUser($user) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->sql->db->prepare("SELECT * from users where email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? $this->listToUser($user) : null;
    }

    public function findUserHobbies(int $userId): array
    {
        $hobbiesArray = [];

        $hobbyQuery = "
        SELECT * FROM hobbies h
        LEFT JOIN user_hobbies uh ON  h.id=uh.hobby_id
        WHERE uh.user_id=:id;
        ";
        $hobbyReq = $this->sql->db->prepare($hobbyQuery);
        $hobbyReq->execute(['id' => $userId]);
        $hobbies = $hobbyReq->fetchAll(PDO::FETCH_ASSOC);

        foreach ($hobbies as $hobby) {
            $newHobby[] = new Hobby($hobby['id'], $hobby['name']);
            $hobbiesArray = $newHobby;
        }
        return $hobbiesArray;
    }

    public function updateHobbies(int $userId, array $newHobbies): void
    {
        $newHobbies = array_slice($newHobbies, 0, 5);

        $deleteQuery = "
        DELETE FROM user_hobbies
        WHERE user_id=:id;
        ";
        $deleteReq = $this->sql->db->prepare($deleteQuery);
        $deleteReq->execute(['id' => $userId]);

        $insertQuery = "
        INSERT INTO user_hobbies (user_id, hobby_id)
        VALUES (:user_id, :hobby_id);
        ";
        $insertReq = $this->sql->db->prepare($insertQuery);

        foreach ($newHobbies as $hobbyId) {
            $insertReq->execute(['user_id' => $userId, 'hobby_id' => intval($hobbyId)]);
        }
    }

    public function updatePreferences(int $userId, array $newPreferences): void
    {
        $query = "
        UPDATE users 
        SET relationship=:relationship, interest=:interest
        WHERE id=:id;
        ";
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'id' => $userId,
            'relationship' => $newPreferences['relation'],
            'interest' => $newPreferences['interest']

        ]);
    }

    public function updateFeatures(int $userId, string $description): void
    {
        $currentUser = $this->findOne($userId);
        $currentFeatures = [];

        if ($currentUser) {
            $rawFeatures = $currentUser->getFeatures();

            if (is_array($rawFeatures)) {
                $currentFeatures = $rawFeatures;
            } elseif (is_string($rawFeatures)) {
                $currentFeatures = json_decode($rawFeatures, true) ?? [];
            }
        }

        if (!$description) {
            $description = $currentUser->getDescription();
        }

        if (isset($_POST['studies_level'])) {
            $currentFeatures['studies_level'] = $_POST['studies_level'];
        }
        if (isset($_POST['astrology'])) {
            $currentFeatures['astrology'] = $_POST['astrology'];
        }
        if (isset($_POST['job'])) {
            $currentFeatures['job'] = trim(ucwords(strtolower($_POST['job'])));
        }

        $query = "
        UPDATE users 
        SET description = :desc, features = :features 
        WHERE id = :userId
    ";

        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'desc' => $description,
            'features' => json_encode($currentFeatures, JSON_UNESCAPED_UNICODE),
            'userId' => $userId
        ]);
    }

    public function updateLocation(
        int $id,
        float $longitude,
        float $latitude,
        string $cityName
    ) {
        $updateQuery = "
            UPDATE users 
            SET longitude = :longitude, latitude=:latitude, city=:city
            WHERE id=:user_id
        ";
        $stmt = $this->sql->db->prepare($updateQuery);
        $stmt->execute([
            'longitude' => $longitude,
            'latitude' => $latitude,
            'user_id' => $id,
            'city' => $cityName
        ]);
    }

    public function updateActive(int $id)
    {
        $updateQuery = "
        UPDATE users 
        SET active = NOT active 
        WHERE id=:user_id
        ";
        $updateReq = $this->sql->db->prepare($updateQuery);
        $updateReq->execute(['user_id' => $id]);
    }

    public function countPhotos(int $id): int
    {
        $query = "
            SELECT COUNT(*) FROM profile_photos
            WHERE user_id=:userId
        ";

        $stmt = $this->sql->db->prepare($query);
        $stmt->execute(['userId' => $id]);
        $count = $stmt->fetch(PDO::FETCH_COLUMN);

        return $count;
    }

    public function updatePhoto(int $id, string $path)
    {
        $query = "
            INSERT INTO profile_photos (user_id, photo_path)
            VALUES(:userId, :photoPath);
        ";
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute(['userId' => $id, 'photoPath' => $path]);
    }

    public function getPhotos(int $id): array
    {
        $query = "
            SELECT photo_path FROM profile_photos
            WHERE user_id=:userId
        ";

        $stmt = $this->sql->db->prepare($query);
        $stmt->execute(['userId' => $id]);
        $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $photos ?? [];

    }

    public function deleteUser(int $id)
    {
        $avatarQuery = "
        SELECT avatar FROM users 
        WHERE id=:user_id
        ";
        $avatarReq = $this->sql->db->prepare($avatarQuery);
        $avatarReq->execute(['user_id' => $id]);
        $avatar = $avatarReq->fetch(PDO::FETCH_ASSOC);
        if (!empty($avatar['avatar'])) {
            $filePath = ROOT_PATH . "/public/uploads/{$avatar['avatar']}";
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        $photosQuery = "
        SELECT photo_path FROM profile_photos 
        WHERE user_id = :user_id
    ";
        $photosReq = $this->sql->db->prepare($photosQuery);
        $photosReq->execute(['user_id' => $id]);
        $photos = $photosReq->fetchAll(PDO::FETCH_ASSOC);

        foreach ($photos as $photo) {
            if (!empty($photo['photo_path'])) {
                $filePath = ROOT_PATH . "/public/uploads/{$photo['photo_path']}";
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }
        $deleteQuery = "
        DELETE FROM users 
        WHERE id= :user_id
        ";
        $deleteReq = $this->sql->db->prepare($deleteQuery);
        $deleteReq->execute(['user_id' => $id]);
    }


    public function emailExists(string $email): bool
    {
        if ($this->findByEmail($email)) {
            return 1;
        }
        return 0;

    }

    public function register(array $data): bool
    {
        $query = "INSERT INTO users (email, password, first_name, last_name, gender, birthdate, interest, avatar) 
                  VALUES (:email, :password, :firstName, :lastName, :gender, :birthdate, :interest, :avatar)";

        $stmt = $this->sql->db->prepare($query);
        return $stmt->execute([
            'email' => $data['email'],
            'password' => $data['password'],
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'gender' => $data['gender'],
            'birthdate' => $data['birthdate'],
            'interest' => $data['interest'],
            'avatar' => $data['avatar']
        ]);
    }

    public function updatePassword(int $id, string $password)
    {
        $query = "UPDATE users SET password = :password WHERE id = :userId";

        $stmt = $this->sql->db->prepare($query);
        $result = $stmt->execute([
            'password' => $password,
            'userId' => $id
        ]);

        return $result;
    }

    public function registerReport(int $userId, string $reason, int $reportedId)
    {
        $query = "
        INSERT INTO reports (description, reported_user_id, reporter_id) 
        VALUES (:reason, :reportedId, :reporterId)
    ";

        $stmt = $this->sql->db->prepare($query);

        return $stmt->execute([
            'reason' => $reason,
            'reportedId' => $reportedId,
            'reporterId' => $userId
        ]);
    }


    public function wasReportedToday(int $userId, int $reportedId): bool
    {
        $query = "
        SELECT COUNT(*) FROM reports 
        WHERE reported_user_id = :reportedId 
        AND DATE(date) = CURRENT_DATE AND reporter_id = :reporterId
    ";

        $stmt = $this->sql->db->prepare($query);
        $stmt->execute([
            'reportedId' => $reportedId,
            'reporterId' => $userId
        ]);

        $count = $stmt->fetchColumn();

        return $count > 0;
    }

}