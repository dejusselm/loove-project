<?php
require_once 'BaseRepository.php';
require_once 'UserRepository.php';
require_once ROOT_PATH . 'models/User.php';
require_once ROOT_PATH . 'controllers/GeoLocalisation.php';
class SearchRepository extends BaseRepository
{
    private ?UserRepository $userRepo;

    public function __construct()
    {
        parent::__construct();

        $this->userRepo = new UserRepository();
    }

    public function search(array $filters, int $currentUserId): array
    {

        $currentUser = $this->userRepo->findOne($currentUserId);
        $query = "SELECT DISTINCT u.*, 
              TIMESTAMPDIFF(YEAR, u.birthdate, CURDATE()) AS age,
              CASE WHEN m.type = 'premium' THEN 1 ELSE 0 END AS is_premium
              FROM users u ";
        if (!empty($filters['hobbies'])) {
            $query .= " INNER JOIN user_hobbies uh ON u.id = uh.user_id ";
        }

        $query .= " LEFT JOIN memberships m ON u.id = m.user_id AND m.type = 'premium' ";

        $query .= " WHERE 1=1 ";
        $params = [];
        $params["userId"] = $currentUserId;
        $query .= " AND u.role='user' 
                AND u.id != :userId";

        $query .= " AND u.id NOT IN (
            SELECT profile_id 
            FROM matches 
            WHERE user_id = :userId
        )";

        $query .= " AND u.id NOT IN (
            SELECT user_id 
            FROM matches 
            WHERE profile_id = :userId 
            AND type = 'reject'
        )";

        if (!empty($filters['name'])) {
            $query .= " AND (u.first_name LIKE :search_first OR u.last_name LIKE :search_last) ";
            $params['search_first'] = '%' . $filters['name'] . '%';
            $params['search_last'] = '%' . $filters['name'] . '%';
        }

        if ($currentUser->getInterest() !== 'all') {
            $query .= " AND u.gender = :myInterest ";
            $params["myInterest"] = $currentUser->getInterest();
        }

        $query .= " AND (u.interest = 'all' OR u.interest = :myGender)";
        $params["myGender"] = $currentUser->getGender();

        if (!empty($filters['relation']) && $filters['relation'] != 'anything') {
            $query .= " AND u.relationship = :relation ";
            $params['relation'] = $filters['relation'];
        }

        if (!empty($filters['minAge']) && !empty($filters['maxAge'])) {
            $query .= " AND TIMESTAMPDIFF(YEAR, u.birthdate, CURDATE()) BETWEEN :minAge AND :maxAge ";
            $params['minAge'] = intval($filters['minAge']);
            $params['maxAge'] = intval($filters['maxAge']);
        }

        if (!empty($filters['city'])) {
            $geoLocalisation = new GeoLocalisation();
            $coord = $geoLocalisation->getCoordinates(trim($filters['city']));

            if ($coord && isset($coord['latitude'], $coord['longitude'])) {
                $radius = !empty($filters['radius']) ? floatval($filters['radius']) : 10.0;

                $query .= " AND (
                    6371 * acos(
                        cos(radians(:lat)) 
                        * cos(radians(u.latitude)) 
                        * cos(radians(u.longitude) - radians(:lon)) 
                        + sin(radians(:lat)) 
                        * sin(radians(u.latitude))
                    )
                ) <= :radius ";

                $params["lat"] = $coord['latitude'];
                $params["lon"] = $coord['longitude'];
                $params["radius"] = $radius;
            }
        }

        if (!empty($filters['hobbies'])) {
            $hobbyPlaceholders = [];
            foreach ($filters['hobbies'] as $key => $hobbyId) {
                $hobbyPlaceholders[] = ":hobby" . $key;
                $params["hobby" . $key] = intval($hobbyId);
            }
            $query .= " AND uh.hobby_id IN (" . implode(',', $hobbyPlaceholders) . ") ";
        }

        if (!empty($filters['astrology'])) {
            $query .= " AND features->'$.astrology' = :astrology";
            $params['astrology'] = $filters['astrology'];
        }

        if (!empty($filters['studies_level'])) {
            $query .= " AND features->'$.studies_level' = :studies_level";
            $params['studies_level'] = $filters['studies_level'];
        }

        if (!empty($filters['job'])) {
            $query .= " AND JSON_UNQUOTE(features->'$.job') LIKE :job";
            $params['job'] = '%' . $filters['job'] . '%';
        }

        $query .= " ORDER BY is_premium DESC, u.id DESC ";

        $stmt = $this->sql->db->prepare($query);
        $stmt->execute($params);
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $results = [];
        foreach ($searchResults as $result) {
            $results[] = $this->listToUser($result);
        }
        return $results;
    }

    public function getAll(array $criteria = []): array
    {
        $query = "SELECT u.*,
        COUNT(r.id) as report_count, 
            GROUP_CONCAT(CONCAT(IFNULL(r.description, 'No message'),
           ':', IFNULL(r.date, 'Unknown date')) SEPARATOR '||') as reasons
           FROM users u";

        if (isset($criteria["includeReports"])) {
            $query .= " LEFT JOIN reports r ON u.id = r.reported_user_id";
        }
        $query .= " WHERE 1=1";
        $parameters = [];

        if (isset($criteria["except"])) {
            $query .= ' AND u.role != :except';
            $parameters["except"] = $criteria["except"];
        }

        if (isset($criteria["isActive"])) {
            $query .= ' AND u.active = :active';
            $parameters["active"] = (int) $criteria["active"];
        }

        if (isset($criteria["userGenderAsInterest"])) {

        }

        $query .= " GROUP BY u.id";
        if (isset($criteria["includeReports"])) {
            $query .= " ORDER BY report_count DESC, u.id DESC";
        } else {
            $query .= " ORDER BY u.id DESC";
        }
        $stmt = $this->sql->db->prepare($query);
        $stmt->execute($parameters);
        $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($allUsers as $user) {
            $userObject = new User(
                $user['id'],
                $user['email'],
                $user['password'],
                $user['gender'],
                $user['last_name'],
                new DateTime($user['birthdate']),
                $user['first_name'],
                $user['role'],
                $user['interest'],
                $user['active'],
                $user['avatar'],
                $user['description'],
                $user['relationship'],
                $user['longitude'],
                $user['latitude'],
                $user['city'],
                $user['features']
            );

            $userObject->report_count = (int) ($user['report_count'] ?? 0);
            $userObject->reasons = $user['reasons'] ?? '';

            $users[] = $userObject;
        }
        return $users;
    }


    public function getDashboardProfiles(int $userId, array $preferences): array
    {
        $query = "
        SELECT u.*,
               (CASE WHEN u.relationship = :relation THEN 1 ELSE 0 END) as match_score
        FROM users u
        WHERE u.id != :current_user_id
        AND u.role='user' 
        ";

        $params = [
            'current_user_id' => $userId
        ];

        if (!empty($preferences['interest']) && $preferences['interest'] !== 'all') {
            $query .= " AND u.gender = :interest ";
            $params['interest'] = $preferences['interest'];
        }

        $currentUser = $this->userRepo->findOne($userId);
        $query .= " AND (u.interest = 'all' OR u.interest = :myGender) ";
        $params['myGender'] = $currentUser->getGender();

        $myRelation = (!empty($preferences['relation']) && $preferences['relation'] !== 'all') ? $preferences['relation'] : 'anything';
        $params['relation'] = $myRelation;

        $query .= " AND u.id NOT IN (
            SELECT profile_id 
            FROM matches 
            WHERE user_id = :current_user_id
        )";

        $query .= " AND u.id NOT IN (
            SELECT user_id 
            FROM matches 
            WHERE profile_id = :current_user_id 
            AND type = 'reject'
        )";
        $seed = isset($_SESSION['searchSeed']) ? (int) $_SESSION['searchSeed'] : rand(1, 9999);
        $query .= " ORDER BY match_score DESC, RAND(" . $seed . ") LIMIT 10 ";

        $stmt = $this->sql->db->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $profiles = [];
        foreach ($results as $result) {
            $profiles[] = $this->listToUser($result);
        }

        return $profiles;
    }

}