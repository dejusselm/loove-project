<?php
require_once("../database/db.php");

$sql = new SqlConnect();

if (isset($_GET['action']) && isset($_GET['id'])) {
    $userId = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'toggle') {
        $updateQuery = "
        UPDATE users 
        SET active = NOT active 
        WHERE id=:id
        ";
        $updateReq = $sql->db->prepare($updateQuery);
        $updateReq->execute(['id' => $userId]);
    } else if ($action === 'delete') {
        $avatarQuery = "
        SELECT avatar FROM users 
        WHERE id=:id
        ";
        $avatarReq = $sql->db->prepare($avatarQuery);
        $avatarReq->execute(['id' => $userId]);
        $avatar = $avatarReq->fetch(PDO::FETCH_ASSOC);
        if (!empty($avatar['avatar'])) {
            $filePath = "../public/uploads/{$avatar['avatar']}";
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $deleteQuery = "
        DELETE FROM users 
        WHERE id= :id
        ";
        $deleteReq = $sql->db->prepare($deleteQuery);
        $deleteReq->execute(['id' => $userId]);
    }
    header('Location: ./adminDashboard.php');
    exit;
}

$query = "
SELECT u.id, u.email, u.first_name, u.last_name, u.avatar, u.active, COUNT(r.id) 
as report_count, GROUP_CONCAT(CONCAT(r.type, ':', IFNULL(r.description, 'No message'),
 ':', IFNULL(r.date, 'Unknown date')) SEPARATOR '||') 
as  reasons 
FROM users u 
LEFT JOIN reports r ON u.id = r.reported_user_id 
WHERE role='user'
GROUP BY u.id  
ORDER BY report_count DESC, u.id DESC 

";
$req = $sql->db->query($query);
$users = $req->fetchAll(PDO::FETCH_ASSOC);

?>