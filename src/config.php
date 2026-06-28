<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', rtrim(getenv('ROOT_PATH')));
}

define('ENCRYPTION_KEY', '%[R3*b2=KgT-@,N@t]:zf}YB!;(;zNBu');

define('DB_NAME', getenv('DB_NAME'));
define('DB_ROOT_PASSWORD', getenv('DB_ROOT_PASSWORD'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));

define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY'));