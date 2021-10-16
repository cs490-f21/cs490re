<?php require_once(__DIR__ . '/lib.php'); ?>

<?php use_template('header.php', true, true); ?>

<title>Select exam</title>

<?php use_template('resource.php', true, true); ?>
<?php use_template('nav.php', true, true); ?>

<?php

if (user_login_check()) {
    user_reload();
}

if(isset($_POST["exam_id"])) {
    $_SESSION["exam"] = $_POST["exam_id"];
    header("Location: takeExam.php");

}

?>

<form method="POST">
    <?php $exam_id = generateExamId(); ?>
    <h1>Select the appropriate exam id provided. Good Luck!!</h1>
    <select id="exam" name="exam_id" ;>
        <option value="">Select ID here</option>
        <?php foreach($exam_id as $exam) :?>
            <option value="<?php echo $exam['id'] ?>"><?php echo $exam['id'] ?></option>
        <?php endforeach; ?>
    </select>
    <div>
        <input type="submit" class="btn btn-primary" name="submit">
    </div>
</form>

<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>