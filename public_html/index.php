<?php require_once(__DIR__ . '/lib.php'); ?>

<?php
    if (user_login_check()) {
        die(header("Location: home.php"));
    } else {
        die(header("Location: login.php"));
    }
?>
