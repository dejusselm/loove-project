<?php
require_once '../database/db.php';


if (isset($_POST['submitted'])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $sql = new SqlConnect();
    $loginQuery = "
        SELECT id, password, gender, first_name, last_name, role, birthdate, active, avatar FROM users  
        WHERE email = :email";
    $loginReq = $sql->db->prepare($loginQuery);
    $loginReq->execute(["email" => $email]);
    $user = $loginReq->fetch(PDO::FETCH_ASSOC);

    if ($user !== false) {
        if (password_verify($password, $user["password"])) {
            $_SESSION["userId"] = $user["id"];
            $_SESSION["role"] = $user["role"];

            if ($_SESSION["role"] == "admin") {
                header("Location: adminDashboard.php");
                exit;
            } else if ($user["active"] == 0) {
                $_SESSION['errorMessage'] = "Account is deactivated.";
                header("Location: login.php");
                exit;
            }
            header('Location: dashboard.php');
            exit;
        }
    } else {
        $_SESSION['errorMessage'] = "Wrong email or password.";
        header("Location: login.php");
        exit;
    }
}
