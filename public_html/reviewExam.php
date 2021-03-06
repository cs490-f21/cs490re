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

    <div class="wide-form">
    <?php foreach($results as $ques) : ?>
        <div>
            <div class="exam-desc">
                <label><span style="font-weight: bold;"><?php write($ques['part_order']) ?>.&nbsp</span><?php write($ques['title']) ?></label><br/>
                <div><p style="font-family: "><?php write($ques['description']) ?></p></div>
            </div>
            <br/>
            <label style="font-weight: bold;">Your answer:</label><br/>
            <textarea type="text" readonly="true" rows="15" cols="100" class="exam-input" spellcheck="false"><?php write($ques['answer']) ?></textarea><br/><br>
            <label style="font-weight: bold;">Score Breakdown:</label><br/>
            <div class="container score-table" name="table">
                <div class="row">
                    <div class="col">Checking</div>
                    <div class="col">Expected Output</div>
                    <div class="col">Your Output</div>
                    <div class="col">Autograded Score</div>
                    <div class="col">Manually Graded Score</div>
                </div>
                <?php for ($j = 0; $j < count($ques['breakdowns']); $j++): ?>
                    <div class="row">
                        <div class="col"><?php write($ques['breakdowns'][$j]["subject"]); ?></div>
                        <div class="col"><?php write($ques['breakdowns'][$j]["expected"]); ?></div>
                        <div class="col"><?php write($ques['breakdowns'][$j]["result"]); ?></div>
                        <div class="col"><?php write($ques['breakdowns'][$j]["autoscore"].'/'.$ques['breakdowns'][$j]["maxscore"]); ?></div>
                        <div class="col">
                            <?php 
                                ($ques['breakdowns'][$j]["manualscore"] === null) ? 
                                write("N/A") : write($ques['breakdowns'][$j]["manualscore"].'/'.$ques['breakdowns'][$j]["maxscore"]); 
                            ?>
                        </div>                
                    </div>
                <?php endfor; ?>
                <div class="row justify-content-center"><b> Your Current Score: 
                    <?php
                        $max_score = 0;
                        $your_score = 0;
                        for($i = 0; $i < count($ques['breakdowns']); $i++) {
                            $max_score += $ques['breakdowns'][$i]['maxscore'];
                            ($ques['breakdowns'][$i]["manualscore"] === null) ? 
                                $your_score += $ques['breakdowns'][$i]["autoscore"] : $your_score += $ques['breakdowns'][$i]["manualscore"];
                        }
                        write($your_score.'/'.$max_score);
                    ?> </b>
                </div>
            </div><br>
            <label style="font-weight: bold;">Comments:</label><br/>
            <?php if (count($ques['comments']) > 0): ?>
                <?php foreach($ques['comments'] as $cmt) : ?>
                    <div class="comment-outer">
                        <div class="comment-header">Comment from <?php write($cmt['username']) ?>: </div>
                        <div class="comment-body"><?php write($cmt['content']) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="comment-outer"><div class="comment-none">No comments</div></div>
            <?php endif; ?>
        </div><hr/>
        
    <?php endforeach; ?>
    </div>
    
<?php endif; ?>

<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>