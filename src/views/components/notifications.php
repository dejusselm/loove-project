<?php

set_include_path(get_include_path() . PATH_SEPARATOR . '/var/www/html/');

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', '/var/www/html/');
}

require_once ROOT_PATH . '/config.php';
require_once ROOT_PATH . 'database/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once ROOT_PATH . '/controllers/UserController.php';


$userController = new UserController();

$notifications = $userController->user->getNotifications();

echo json_encode($notifications ?? []);
exit;