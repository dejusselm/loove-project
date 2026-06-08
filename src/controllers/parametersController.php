<?php


require_once '../database/db.php';

$sql = new SqlConnect();
$userId = $_SESSION['userId'];

if (isset($_POST["deleteAccount"])) {
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

    $_SESSION["errorMessage"] = "Account deleted.";
    header('Location: ../views/login.php');
    exit;
}

?>