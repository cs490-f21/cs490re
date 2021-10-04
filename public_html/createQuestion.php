<?php require_once(__DIR__ . '/lib.php'); ?>

<?php use_template('header.php', true, true); ?>

<title>Create your question</title>

<?php use_template('resource.php', true, true); ?>
<?php use_template('nav.php', true, true); ?>

<?php
if (user_login_check()) {
    user_reload();
}
if (!user_admin_check()) {
    die(header("Location: home.php"));
}

if(isset($_POST['submit'])) {
    $type = get($_POST, "type", null);
    $title = get($_POST, "title", null);
    $level = get($_POST, "level", null);
    $desc = get($_POST, "desc", null);

    $flag = true;
    $totalCases = 0;

    if(!isset($type)) {
        addFlash("Question type is undefined", FLASH_WARN);
        $flag = false;
    }
    if(!isset($title)) {
        addFlash("Questions must have a title", FLASH_WARN);
        $flag = false;
    }
    if(!isset($level)){
        addFlash("Questions must have a difficulty", FLASH_WARN);
        $flag = false;
    }
    if(!isset($desc)) {
        addFlash("Questions must have a description", FLASH_WARN);
        $flag = false;
    }
    if($flag) {
        create_problem($title, $type, $level, $desc);
    }
}

?>

<div>
    <h1> Fill in your question <h1>
    <form method="POST"> 
        <input type="text" name="title" placeholder="title"> </input> 
        <input type="text" name="type" placeholder="type"> </input> 
        <input type="text" name="level" placeholder="level"> </input> 
        <input type="text" name="desc" placeholder="description"> </input> 
        <input type="submit" class="btn btn-primary" name="submit"> </input> 
    </form> 
</div>


<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>

