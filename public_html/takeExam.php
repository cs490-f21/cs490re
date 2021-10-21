<?php require_once(__DIR__ . '/lib.php'); ?>

<?php use_template('header.php', true, true); ?>

<title>Select exam</title>

<?php use_template('resource.php', true, true); ?>
<?php use_template('nav.php', true, true); ?>

<?php

if (!user_login_check()) {
    die(header("Location: login.php"));
}

if (user_login_check()) {
    user_reload();
}

if(isset($_POST["exam_id"])) {
    $_SESSION["exam"] = $_POST["exam_id"];
    header("Location: takeExam2.php");
}

?>

<form method="POST">
    <?php $exam_id = generateExamId(); ?>
    <?php 
    $ids = [];
    foreach($exam_id as $id) {
        array_push($ids, $id['id']);
    }    
    $filtered = filterExams(user_get_id(), $ids); 
    ?>
    <h1>Please select the appropriate exam id provided.</h1>
    <select id="exam" name="exam_id" ;>
        <option value="">Select ID here</option>
        <?php foreach($filtered as $filter) :?>
            <option value="<?php echo $filter ?>"><?php echo $filter ?></option>
        <?php endforeach; ?>
    </select>
    <div>
        <input type="submit" class="btn btn-primary" name="submit">
    </div>
</form>

<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>