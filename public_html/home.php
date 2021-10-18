<?php // author: Jiyuan Zhang
require_once(__DIR__ . '/lib.php');
?>

<?php use_template('header.php', true, true); ?>

<title>Home</title>

<?php use_template('resource.php', true, true); ?>
<?php use_template('nav.php', true, true); ?>

<?php
if (user_login_check()) {
    user_reload();
}
?>
    
<h1>Home</h1>
<?php if (user_login_check()): ?>


<div class="center-form">
<h5 class="center">Welcome, <?php write(user_get_username()); ?>!</h5>

<div class="inner-title"><b>User Info</b></div>

<hr/>
<div class="inner-title"><b>User Roles</b></div>
<div class="container">
    <?php $roles = user_get_roles(false); ?>
    <?php if ($roles) : ?>
        <?php foreach ($roles as $role) : ?>
            <?php 
                $desc = [];
                $status = role_get_desc($role['role'], $desc);
                addStatus($status, FLASH_SUCC);
            ?>
            <div class="role-desc role-<?php write($role['active'] ? 'active' : 'inactive'); ?>">
                <?php write(strtoupper(get($desc, 'name', 'unknown'))); ?>:
                <?php write(get($desc, 'desc', 'Unknown Role Description')); ?>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <div>None</div>
    <?php endif; ?>
</div>
</div>
    
<?php else: ?>

<div class="center-form">
<h5 class="center">Welcome, Guest!</h5>
</div>

<?php endif; ?>

<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>
