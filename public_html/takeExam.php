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

?>

<?php if(isset($_POST["exam_id"])): ?>
<form method="POST">
    <?php 
    if(!empty($_POST["exam_id"])) {
        $questions = generateExam((int)$_POST["exam_id"]);
        $q_order = 1;
    }
    ?>
    <h1> Taking exam <?php echo getExamName($_POST["exam_id"]); ?>. Good luck!! </h1>
    <?php foreach($questions as $q) : ?>
    <div>
        <?php echo $q_order . ") " . $q["description"]; $q_order++;?>
    </div>
    <div>
        <label for="solutions[]">Write your code here:</label>
        <textarea type="text" name="solutions[]" placeholder="Code Here" rows="15" cols="100"></textarea><br><br>
    </div>
    <?php endforeach; ?>
    <div>
        <input id="submit" type="submit" class="btn btn-primary" name="submit_exam">
    </div>
</form>

<?php elseif(isset($_POST["submit_exam"])): ?>
<?php
    addFlash("Exam submitted", FLASH_SUCC);
?>

<?php else: ?>
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
<?php endif; ?>


<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>