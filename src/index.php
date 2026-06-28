<?php
require_once 'config.php';

$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($requestPath, '/');

if ($path === '') {
    include ROOT_PATH . 'views/pages/preLogin/home.php';
    exit;
}

$customRoutes = [
    'parameters' => 'views/pages/user/parameters.php',
    'profile' => 'views/pages/user/profile.php',
    'dashboard' => 'views/pages/user/dashboard.php',
    'otherProfile' => 'views/pages/user/otherProfile.php',

];

if (array_key_exists($path, $customRoutes)) {
    $fileToInclude = ROOT_PATH . $customRoutes[$path];
} else {

    $fileToInclude = ROOT_PATH . 'views/pages/' . $path . '.php';
}

if (file_exists($fileToInclude)) {
    include $fileToInclude;
    exit;
} else {
    http_response_code(404);
    echo "<h1>Error 404 : Page not found</h1>";
}
?>