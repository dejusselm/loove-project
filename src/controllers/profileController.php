<?php

require_once '../database/db.php';

$userId = $_SESSION['userId'];
$sql = new SqlConnect();


if (isset($_POST['descriptionModified'])) {
    $newDescription = trim($_POST['description']);

    $descQuery = "
    UPDATE users 
    SET description=:description
    WHERE id=:id;
    ";
    $descReq = $sql->db->prepare($descQuery);
    $descReq->execute(["id" => $userId, "description" => $newDescription]);

    header('Location: profile.php');
    exit;
}


$dataQuery = "
    SELECT first_name, last_name, birthdate, gender, interest, description, relationship, avatar, active
    FROM users
    WHERE id=:id;
";
$dataReq = $sql->db->prepare($dataQuery);
$dataReq->execute(["id" => $userId]);
$data = $dataReq->fetch(PDO::FETCH_ASSOC);

$currentDate = new DateTime();
$usersBirthday = new DateTime($data["birthdate"]);
$difference = $currentDate->diff($usersBirthday);
$usersAge = $difference->y;


if ($data['active'] == 0) {
    unset($_SESSION["userId"]);
    unset($_SESSION["role"]);
    $_SESSION["errorMessage"] = "Account deactivated.";
    header('Location: login.php');
    exit;
}

$hobbyQuery = "
SELECT name FROM hobbies h
LEFT JOIN user_hobbies uh ON  h.id=uh.hobby_id
WHERE uh.user_id=:id;
";
$hobbyReq = $sql->db->prepare($hobbyQuery);
$hobbyReq->execute(['id' => $userId]);
$hobbies = $hobbyReq->fetchAll(PDO::FETCH_ASSOC);

$allHobbiesQuery = "
SELECT id, name FROM hobbies 
ORDER BY name ASC
";
$allHobbiesReq = $sql->db->query($allHobbiesQuery);
$allHobbies = $allHobbiesReq->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['hobbies']) && is_array($_POST['hobbies'])) {
    $selectedHobbies = $_POST['hobbies'];
    $selectedHobbies = array_slice($selectedHobbies, 0, 5);

    $deleteQuery = "
    DELETE FROM user_hobbies
    WHERE user_id=:id;
    ";
    $deleteReq = $sql->db->prepare($deleteQuery);
    $deleteReq->execute(['id' => $userId]);

    $insertQuery = "
        INSERT INTO user_hobbies (user_id, hobby_id)
        VALUES (:user_id, :hobby_id);
    ";
    $insertReq = $sql->db->prepare($insertQuery);

    foreach ($selectedHobbies as $hobbyId) {
        $insertReq->execute(['user_id' => $userId, 'hobby_id' => intval($hobbyId)]);
    }

    header('Location: ../views/profile.php');
    exit;
}