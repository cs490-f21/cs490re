<?php require_once(__DIR__ . '/lib.php'); ?>

<?php use_template('header.php', true, true); ?>

<title>Create your exam</title>

<?php use_template('resource.php', true, true); ?>
<?php use_template('nav.php', true, true); ?>

<?php

if (user_login_check()) {
    user_reload();
}

if (!user_admin_check()) {
    die(header("Location: home.php"));
}

if (isset($_POST["submit"])){
    $list = get($_POST, "q_id", null);

    $flag = true;
    
    if (empty($list)){
        addFlash("Exams must have a question");
        $flag = false;
    }
    if ($flag) {
        session_start();
        $_SESSION["list"] = $list;
        header("Location: createExam2.php");
    }
}

?>

<form method="POST">
    <table>
    <tr>
        <th> Checkbox </th>
        <th> Description </th>
    </tr>
        <?php $questions = load_problems(); ?>
        <?php foreach($questions as $q): echo "<tr>" ?> 
        <?php echo "<td>";  ?>
            <input type="checkbox" name="q_id[]" value="<?php echo $q['id']; ?>" > 
        <?php echo "</td>"; ?>
        <?php echo "<td>"; ?> 
            <p id="<?php echo $q['id']; ?>"> 
                <b><u>Id:</u></b> <?php echo $q['id']; ?> <br>
                <b><u>Title:</u></b> <?php echo $q['title']; ?> <br>                                  
                <b><u>Description:</u></b> <?php echo $q['description']; ?> <br>
                <b><u>Type:</u></b> <?php echo display_type($q['type']); ?> <br>
                <b><u>Level:</u></b> <?php echo display_level($q['level']); ?> <br>
            </p>
        <?php echo "</td>" ?>
        <?php endforeach; ?> 
    </table>
    <div>
            <input type="submit" class="btn btn-primary" name="submit">
    </div>
</form>

<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>