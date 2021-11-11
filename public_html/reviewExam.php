<?php // author: Jiyuan Zhang
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
}

?>

<?php if (array_search($selected_exam, $available_exam_ids) === false): ?>
    <h1>Review a exam</h1>
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
<?php else: ?>
    <?php 
    
    $results = [];
    $user = user_get_id();
    $status = collect_result_user($selected_exam, $results);
    $stage = getExamStatusDirect($selected_exam, $user);
    if (!$status->isSuccess() || count($results) == 0) {
        addFlash('You have not taken the exam.', FLASH_WARN);
        $results = [];
        goto Fail;
    }

    if ($stage != 2) {
        addFlash('You cannot view the result at this time.', FLASH_WARN);
        $results = [];
        goto Fail;
    }

    foreach ($results as $key => $value) {
        $submission = $value['id'];
        $comments = [];
        $status = get_comments($submission, $comments);
        $results[$key]['comments'] = $comments;
    }

    Fail:;
    ?>

    <?php foreach($results as $ques) : ?>
        <div>
            <label><span style="font-weight: bold;"><?php write($ques['part_order']) ?>.&nbsp</span><?php write($ques['title']) ?></label><br/>
            <div><span style="font-weight: bold;">Your score: <?php //write($ques['point']) ?> / <?php write($ques['possible']) ?></span></div><br/>
            <div><p style="font-family: "><?php write($ques['description']) ?></p></div><br/>
            <label style="font-weight: bold;">Your answer:</label><br/>
            <textarea type="text" readonly="true" rows="15" cols="100"><?php write($ques['answer']) ?></textarea><br/>
            <label style="font-weight: bold;">Comments:</label><br/>
            <?php if (count($ques['comments']) > 0): ?>
                <?php foreach($ques['comments'] as $cmt) : ?>
                    Comment from <?php write($cmt['username']) ?>: <br/>
                    <?php write($cmt['content']) ?><br/><br/>
                <?php endforeach; ?>
            <?php else: ?>
                No comments
            <?php endif; ?>
            <br>
            <label style="font-weight: bold;">Score Breakdown:</label><br/>
            <div class="container" name="table">
                <div class="row">
                    <div class="col">Checking</div>
                    <div class="col">Expected Output</div>
                    <div class="col">Your Output</div>
                    <div class="col">Autograded Score</div>
                    <div class="col">Manually Graded Score</div>
                </div>
                <div class="row">
                    <div class="col">Name</div>
                    <div class="col">Expected Name</div>
                    <div class="col">Your Name</div>
                    <div class="col">Auto: 30</div>
                    <div class="col">Manual: 20</div>
                </div>
                <div class="row">
                    <div class="col">Constraint</div>
                    <div class="col">Expected Constraint</div>
                    <div class="col">N/A</div>
                    <div class="col">Auto: 30</div>
                    <div class="col">Manual: 30</div>
                </div>
                <div class="row">
                    <div class="col">Test Case 1</div>
                    <div class="col">Output</div>
                    <div class="col">Your Output</div>
                    <div class="col">Auto: 10</div>
                    <div class="col">Manual: 10</div>
                </div>
                <div class="row">
                    <div class="col">Test Case 2</div>
                    <div class="col">Output</div>
                    <div class="col">Your Output</div>
                    <div class="col">Auto: 20</div>
                    <div class="col">Manual: 20</div>
                </div>
                <div class="row">
                    <div class="col">Test Case 3</div>
                    <div class="col">Output</div>
                    <div class="col">Your Output</div>
                    <div class="col">Auto: 30</div>
                    <div class="col">Manual: 30</div>
                </div>
                <div class="row justify-content-center">Total Score: 60</div>
            </div>
        </div><br/><hr/>
        
    <?php endforeach; ?>
<?php endif; ?>

<style>
div.row, div.col {
  border: 1px solid black;
  text-align: center;
}
</style>

<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>