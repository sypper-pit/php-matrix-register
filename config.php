<?php
// config.php

if (basename($_SERVER['SCRIPT_FILENAME']) == 'config.php') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

define('API_MATRIX_USER', '<syt_key_use_get_api.php>');
define('BOT_LOGIN', 'regis');
define('MATRIX_SERVER_NAME', 'site.com');
define('MATRIX_SERVER_URL', 'https://site.com');
define('SHARED_SECRET', '<registration_shared_secret_in_homserver.yaml>');
define('MIN_PASS', '8');
?>
