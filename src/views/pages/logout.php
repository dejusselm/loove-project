<?php
include ROOT_PATH . 'controllers/AuthController.php';
$authController = new AuthController();
$authController->logout();