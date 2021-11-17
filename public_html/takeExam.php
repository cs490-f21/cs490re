<?php require_once(__DIR__ . '/lib.php'); ?>

<?php use_template('header.php', true, true); ?>

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
<title>Exam in Progress</title>

<form method="POST">
    <?php 
    if(!empty($_POST["exam_id"])) {
        $questions = generateExam((int)$_POST["exam_id"]);
        $q_order = 1;
    }
    ?>
    <h1> Taking exam <?php write(getExamName($_POST["exam_id"])); ?>. Good luck!! </h1>
    <?php foreach($questions as $q) : ?>
    <div>
        <?php 
            write($q_order . ") [" . $q["point"] . " points] " . $q["description"]); 
            $q_order++;
        ?>
    </div><br>
    <div>
        <label style="font-weight: bold" for="solutions[]">Write your code here:</label><br>
        <textarea type="text" name="solutions[]" placeholder="Code Here" rows="15" cols="100"></textarea><br><br>
    </div><hr/>
    <?php endforeach; ?>
    <div>
        <input id="submit" type="submit" class="btn btn-primary" name="submit_exam" value="Submit Exam"> 
    </div>
</form>

<?php elseif(isset($_POST["submit_exam"])): ?>
<?php
    addFlash("Exam submitted", FLASH_SUCC);
?>

<?php else: ?>
<title>Select exam</title>
<h1>Select the appropriate exam id provided.</h1>
<form method="POST" class="center-form">
    <?php $exam_id = generateExamId(); ?>
    <?php 
    $ids = [];
    foreach($exam_id as $id) {
        array_push($ids, $id['id']);
    }    
    $filtered = filterExams(user_get_id(), $ids); 
    ?>
    <div class="mb-3">
        <label for="exam">Select an Exam ID:</label><br>
        <select class="form-select" id="exam" name="exam_id">
            <option value="">Select here</option>
            <?php foreach($filtered as $filter) :?>
                <option value="<?php write($filter) ?>"><?php write($filter) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <input type="submit" class="btn btn-primary" name="submit">
    </div>
</form>
<?php endif; ?>


<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>