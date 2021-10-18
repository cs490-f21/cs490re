<?php // author: Jiyuan Zhang

// DB Config Loader, don't use this directly
$ini = @parse_ini_file('.env');
$db_url = '';
$db_user = '';
$db_pass = '';
if ($ini && isset($ini['DB_URL'])) {
    $db_url = $ini['DB_URL'];
    $db_user = $ini['DB_USER'];
    $db_pass = $ini['DB_PASS'];
}
else {
    $db_url = getenv('DB_URL');
    $db_user = getenv('DB_USER');
    $db_pass = getenv('DB_PASS');
}
