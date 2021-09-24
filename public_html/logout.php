<?php require_once(__DIR__ . '/lib.php'); ?>

<?php use_template('header.php', true, true); ?>

<title>Logout</title>

<?php use_template('resource.php', true, true); ?>
<?php use_template('nav.php', true, true); ?>

<?php
if (user_login_check()) {
    $status = user_logout();
    addStatus($status);
}

die(header("Location: login.php"));
?>

<!-- do not use flash.php for logout page -->
<?php use_template('footer.php', true, true); ?>
