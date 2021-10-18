<?php 
require_once(__DIR__ . '/lib.php');
?>

<?php use_template('header.php', true, true); ?>

<title>Review Exam</title>

<?php use_template('resource.php', true, true); ?>
<?php use_template('nav.php', true, true); ?>

<?php

if (!user_login_check()) {
    die(header("Location: login.php"));
}

$available_exam = generateExamId();
$available_exam_ids = [];
foreach ($available_exam as $exam) {
    array_push($available_exam_ids, $exam['id']);
}

$selected_exam = -1;
if(isset($_POST["exam_id"]) && validate_number($_POST["exam_id"], 1, 2147483646)) {
    $selected_exam = intval($_POST["exam_id"]);
    echo $selected_exam;
}

?>

<?php if (array_search($selected_exam, $available_exam_ids) === false) ?>
    <h1>Check the status of an exam</h1>
    <form class="center-form" method="POST" onsubmit="return validate(this);">
        <div class="mb-3">
            <label for="email">Select an exam: </label>
            <select class="form-select" id="exam" name="exam_id">
                <option selected>Please select</option>
                <?php foreach($available_exam as $exam) :?>
                    <option value="<?php write($exam['id']) ?>"><?php write($exam['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <input type="submit" class="btn btn-primary" name="submit" value="Submit" />
        </div>
    </form>





<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>

