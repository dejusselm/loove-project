<?php

require_once '../database/db.php';

if (!isset($_SESSION['userId'])) {
    header('Location: login.php');
    exit;
}

$sql = new SqlConnect();
$userId = $_SESSION['userId'];

$dataQuery = "
    SELECT active, avatar
    FROM users
    WHERE id=:id;
";
$dataReq = $sql->db->prepare($dataQuery);
$dataReq->execute(["id" => $userId]);
$data = $dataReq->fetch(PDO::FETCH_ASSOC);

if ($data['active'] == 0) {
    unset($_SESSION["userId"]);
    unset($_SESSION["role"]);
    $_SESSION["errorMessage"] = "Account deactivated.";
    header('Location: login.php');
    exit;
}