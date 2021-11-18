<?php // author: Jiyuan Zhang
require_once(__DIR__ . '/lib.php');
?>

<?php use_template('header.php', true, true); ?>

<title>Audit Exam</title>

<?php use_template('resource.php', true, true); ?>
<?php use_template('nav.php', true, true); ?>

<?php

if (!user_admin_check()) {
    die(header("Location: home.php"));
}

$available_exam = generateExamId();
$available_exam_ids = [];
foreach ($available_exam as $exam) {
    array_push($available_exam_ids, $exam['id']);
}

$available_user = [];
$status = list_users($available_user);
addStatus($status, FLASH_SUCC);
$available_user_ids = [];
foreach ($available_user as $user) {
    array_push($available_user_ids, $user['id']);
}

$selected_exam = -1;
if(isset($_POST["exam_id"]) && validate_number($_POST["exam_id"], 1, 2147483646)) {
    $selected_exam = intval($_POST["exam_id"]);
}

$selected_user = -1;
if(isset($_POST["user_id"]) && validate_number($_POST["user_id"], 1, 2147483646)) {
    $selected_user = intval($_POST["user_id"]);
}

?>

<?php if (array_search($selected_exam, $available_exam_ids) === false || array_search($selected_user, $available_user_ids) === false): ?>
    <h1>Audit a student exam</h1>
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
        <div class="mb-3">
            <label for="username">Select an user: </label>
            <select class="form-select" id="user" name="user_id">
                <option selected>Please select</option>
                <?php foreach($available_user as $user) :?>
                    <option value="<?php write($user['id']) ?>"><?php write($user['username']) ?> (<?php write($user['email']) ?>)</option>
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
    $status = collect_result_user($selected_exam, $results, $selected_user);
    $stage = getExamStatusDirect($selected_exam, $selected_user);
    if (!$status->isSuccess() || count($results) == 0) {
        addFlash('The student have not taken the exam.', FLASH_WARN);
        $results = [];
        goto Fail;
    }

    if ($stage != 2) {
        addFlash('Please grade the exam first.', FLASH_WARN);
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
                <label><span style="font-weight: bold;"><?php write($ques['part_order']) ?>.&nbsp</span><?php write($ques['title']) ?></label>
                <a class="btn btn-primary" href="auditQuestion.php?id=<?php write($ques['id']) ?>" target="_blank" role="button">Edit</a><br/>
                <div><p style="font-family: "><?php write($ques['description']) ?></p></div>
            </div>
            
            <br/>            
            <label style="font-weight: bold;">Submitted answer:</label><br/>
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
                <div class="row">
                    <div class="col"><?php write($ques['breakdowns'][0]["subject"]); ?></div>
                    <div class="col"><?php write($ques['breakdowns'][0]["expected"]); ?></div>
                    <div class="col"><?php write($ques['breakdowns'][0]["result"]); ?></div>
                    <div class="col"><?php write($ques['breakdowns'][0]["autoscore"].'/'.$ques['breakdowns'][0]["maxscore"]); ?></div>
                    <div class="col">
                        <?php 
                            ($ques['breakdowns'][0]["manualscore"] == null) ? 
                            write("N/A") : write($ques['breakdowns'][0]["manualscore"].'/'.$ques['breakdowns'][0]["maxscore"]); 
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col"><?php write($ques['breakdowns'][1]["subject"]); ?></div>
                    <div class="col"><?php write($ques['breakdowns'][1]["expected"]); ?></div>
                    <div class="col"><?php write($ques['breakdowns'][1]["result"]); ?></div>
                    <div class="col"><?php write($ques['breakdowns'][1]["autoscore"].'/'.$ques['breakdowns'][1]["maxscore"]); ?></div>
                    <div class="col">
                        <?php 
                            ($ques['breakdowns'][1]["manualscore"] == null) ? 
                            write("N/A") : write($ques['breakdowns'][1]["manualscore"].'/'.$ques['breakdowns'][1]["maxscore"]); 
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col"><?php write($ques['breakdowns'][2]["subject"]); ?></div>
                    <div class="col"><?php write($ques['breakdowns'][2]["expected"]); ?></div>
                    <div class="col"><?php write($ques['breakdowns'][2]["result"]); ?></div>
                    <div class="col"><?php write($ques['breakdowns'][2]["autoscore"].'/'.$ques['breakdowns'][2]["maxscore"]); ?></div>
                    <div class="col">
                        <?php 
                            ($ques['breakdowns'][2]["manualscore"] == null) ? 
                            write("N/A") : write($ques['breakdowns'][2]["manualscore"].'/'.$ques['breakdowns'][2]["maxscore"]); 
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col"><?php write($ques['breakdowns'][3]["subject"]); ?></div>
                    <div class="col"><?php write($ques['breakdowns'][3]["expected"]); ?></div>
                    <div class="col"><?php write($ques['breakdowns'][3]["result"]); ?></div>
                    <div class="col"><?php write($ques['breakdowns'][3]["autoscore"].'/'.$ques['breakdowns'][3]["maxscore"]); ?></div>
                    <div class="col">
                        <?php 
                            ($ques['breakdowns'][3]["manualscore"] == null) ? 
                            write("N/A") : write($ques['breakdowns'][3]["manualscore"].'/'.$ques['breakdowns'][3]["maxscore"]); 
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col"><?php write($ques['breakdowns'][4]["subject"]); ?></div>
                    <div class="col"><?php write($ques['breakdowns'][4]["expected"]); ?></div>
                    <div class="col"><?php write($ques['breakdowns'][4]["result"]); ?></div>
                    <div class="col"><?php write($ques['breakdowns'][4]["autoscore"].'/'.$ques['breakdowns'][4]["maxscore"]); ?></div>
                    <div class="col">
                        <?php 
                            ($ques['breakdowns'][4]["manualscore"] == null) ? 
                            write("N/A") : write($ques['breakdowns'][4]["manualscore"].'/'.$ques['breakdowns'][4]["maxscore"]); 
                        ?>
                    </div>
                </div>
                <div class="row justify-content-center"><b> Your Current Score:
                    <?php
                        $max_score = 0;
                        $your_score = 0;
                        for($i = 0; $i < count($ques['breakdowns']); $i++) {
                            $max_score += $ques['breakdowns'][$i]['maxscore'];
                            ($ques['breakdowns'][$i]["manualscore"] == null) ? 
                                $your_score += $ques['breakdowns'][$i]["autoscore"] : $your_score += $ques['breakdowns'][$i]["manualscore"];
                        }
                        write($your_score.'/'.$max_score);
                    ?> 
                </div></b>
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