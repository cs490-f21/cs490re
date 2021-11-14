<?php // author: Jiyuan Zhang
require_once(__DIR__ . '/lib.php');
?>

<?php use_template('header.php', true, true); ?>

<title>Audit Question</title>

<?php use_template('resource.php', true, true); ?>
<?php use_template('nav.php', true, true); ?>

<?php

if (!user_admin_check()) {
    die(header("Location: home.php"));
}

$selected_question = -1;
if(isset($_GET["id"]) && validate_number($_GET["id"], 1, 2147483646)) {
    $selected_question = intval($_GET["id"]);
}

$question = null;
if ($selected_question > 0) {
    $results = [];
    $status = collect_result_submission($selected_question, $results);
    if (!$status->isSuccess() || count($results) == 0) {
        goto Fail;
    }

    $question = $results[0];
    $submission = $question['id'];
    $comments = [];
    $status = get_comments($submission, $comments);
    $question['comments'] = $comments;
}

if (isset($_POST['sub_present']) && $_POST['sub_present'] == "1") {
    $breakdown_id = get($_POST, 'b_id', null);
    $grades = get($_POST, 'new_grade', null);

    $valid = true;

    if(!isset($_POST["sub_id"]) || !validate_number($_POST["sub_id"], 1, 2147483646)) {
        $valid = false;
    }
    
    if(!isset($_POST["sub_score"]) || !validate_number($_POST["sub_score"], 0, 2147483646)) {
        $valid = false;
    }

    if(!isset($_POST["sub_answer"]) || !validate_string($_POST["sub_answer"], 4096)) {
        $valid = false;
    }

    if(!isset($_POST["sub_comment"]) || !validate_string($_POST["sub_comment"], 1024)) {
        $valid = (strlen($_POST["sub_comment"]) == 0);
    }

    $sub_id = intval($_POST["sub_id"]);
    $sub_score = intval($_POST["sub_score"]);
    $sub_answer = $_POST["sub_answer"];
    $sub_comment = $_POST["sub_comment"];

    if ($sub_id !== $selected_question) {
        $valid = false;
    }

    if ($sub_score > $question['possible']) {
        $valid = false;
    }

    if (!$valid) {
        addFlash("Invalid data provided.", FLASH_ERRO);
        goto Fail;
    }

    if (strlen($sub_comment) > 0) {
        $status = add_comment($sub_id, $sub_comment);
        $valid &= $status->isSuccess();
        addStatus($status, FLASH_SUCC);
    }

    for($i = 0; $i < count($breakdown_id); $i++)  {
        if ($grades[$i] != null) {
            $status = change_grade($breakdown_id[$i], $grades[$i]);
            $valid &= $status->isSuccess();
        }
        if ($i == 4) {
            addStatus($status, FLASH_SUCC);
        }
    }

    if ($valid) {
        addFlash("Changes saved.", FLASH_SUCC);
        die(header('Location: '.$_SERVER['REQUEST_URI']));
    }
}

Fail:;
?>

<?php if ($question === null): ?>
    <?php addFlash("Question not found", FLASH_ERRO); ?>
    <div style="text-align:center">
        <a class="btn btn-primary" href="javascript:window.close();">Close</a>
    </div>
<?php else: ?>
    <form method="post" onsubmit="return validate(this);">
        <label><span style="font-weight: bold;"><?php write($question['part_order']) ?>.&nbsp</span><?php write($question['title']) ?></label><br/>
        <div><p style="font-family: "><?php write($question['description']) ?></p></div><br/>
        <label style="font-weight: bold;">Submitted answer:</label><br/>
        <textarea type="text" readonly="true" name="sub_answer" rows="15" cols="100"><?php write($question['answer']) ?></textarea><br/><br>
        <label style="font-weight: bold;">Score Breakdown:</label><br/>
            <div class="container" name="table">
                <div class="row">
                    <div class="col">Checking</div>
                    <div class="col">Expected Output</div>
                    <div class="col">Your Output</div>
                    <div class="col">Autograded Score</div>
                    <div class="col">Manually Graded Score</div>
                </div>
                <input type="hidden" name="b_id[]" value="<?php write($question['breakdowns'][0]['id']); ?>"></input>
                <div class="row">
                    <div class="col"><?php write($question['breakdowns'][0]["subject"]); ?></div>
                    <div class="col"><?php write($question['breakdowns'][0]["expected"]); ?></div>
                    <div class="col"><?php write($question['breakdowns'][0]["result"]); ?></div>
                    <div class="col"><?php write($question['breakdowns'][0]["autoscore"].'/'.$question['breakdowns'][0]["maxscore"]); ?></div>
                    <div class="col">
                        <input type="text" name="new_grade[]" 
                        placeholder="Current: <?php ($question['breakdowns'][0]["manualscore"] == null) ? 
                                write($question['breakdowns'][0]["autoscore"].'/'.$question['breakdowns'][0]["maxscore"]) : 
                                write($question['breakdowns'][0]["manualscore"].'/'.$question['breakdowns'][0]["maxscore"]); ?>">
                        </input>
                    </div>                
                </div>
                <input type="hidden" name="b_id[]" value="<?php write($question['breakdowns'][1]['id']); ?>"></input>
                <div class="row">
                    <div class="col"><?php write($question['breakdowns'][1]["subject"]); ?></div>
                    <div class="col"><?php write($question['breakdowns'][1]["expected"]); ?></div>
                    <div class="col"><?php write($question['breakdowns'][1]["result"]); ?></div>
                    <div class="col"><?php write($question['breakdowns'][1]["autoscore"].'/'.$question['breakdowns'][1]["maxscore"]); ?></div>
                    <div class="col">
                        <input type="text" name="new_grade[]" 
                        placeholder="Current: <?php ($question['breakdowns'][1]["manualscore"] == null) ? 
                                write($question['breakdowns'][1]["autoscore"].'/'.$question['breakdowns'][1]["maxscore"]) : 
                                write($question['breakdowns'][1]["manualscore"].'/'.$question['breakdowns'][1]["maxscore"]); ?>">
                        </input>
                    </div>                
                </div>
                <input type="hidden" name="b_id[]" value="<?php write($question['breakdowns'][2]['id']); ?>"></input>
                <div class="row">
                    <div class="col"><?php write($question['breakdowns'][2]["subject"]); ?></div>
                    <div class="col"><?php write($question['breakdowns'][2]["expected"]); ?></div>
                    <div class="col"><?php write($question['breakdowns'][2]["result"]); ?></div>
                    <div class="col"><?php write($question['breakdowns'][2]["autoscore"].'/'.$question['breakdowns'][2]["maxscore"]); ?></div>
                    <div class="col">
                        <input type="text" name="new_grade[]" 
                        placeholder="Current: <?php ($question['breakdowns'][2]["manualscore"] == null) ? 
                                write($question['breakdowns'][2]["autoscore"].'/'.$question['breakdowns'][2]["maxscore"]) : 
                                write($question['breakdowns'][2]["manualscore"].'/'.$question['breakdowns'][2]["maxscore"]); ?>">
                        </input>
                    </div>
                </div>
                <input type="hidden" name="b_id[]" value="<?php write($question['breakdowns'][3]['id']); ?>"></input>
                <div class="row">
                    <div class="col"><?php write($question['breakdowns'][3]["subject"]); ?></div>
                    <div class="col"><?php write($question['breakdowns'][3]["expected"]); ?></div>
                    <div class="col"><?php write($question['breakdowns'][3]["result"]); ?></div>
                    <div class="col"><?php write($question['breakdowns'][3]["autoscore"].'/'.$question['breakdowns'][3]["maxscore"]); ?></div>
                    <div class="col">
                        <input type="text" name="new_grade[]" 
                        placeholder="Current: <?php ($question['breakdowns'][3]["manualscore"] == null) ? 
                                write($question['breakdowns'][3]["autoscore"].'/'.$question['breakdowns'][3]["maxscore"]) : 
                                write($question['breakdowns'][3]["manualscore"].'/'.$question['breakdowns'][3]["maxscore"]); ?>">
                        </input>
                    </div>
                </div>
                <input type="hidden" name="b_id[]" value="<?php write($question['breakdowns'][4]['id']); ?>"></input>
                <div class="row">
                    <div class="col"><?php write($question['breakdowns'][4]["subject"]); ?></div>
                    <div class="col"><?php write($question['breakdowns'][4]["expected"]); ?></div>
                    <div class="col"><?php write($question['breakdowns'][4]["result"]); ?></div>
                    <div class="col"><?php write($question['breakdowns'][4]["autoscore"].'/'.$question['breakdowns'][4]["maxscore"]); ?></div>
                    <div class="col">
                        <input type="text" name="new_grade[]" 
                        placeholder="Current: <?php ($question['breakdowns'][4]["manualscore"] == null) ? 
                                write($question['breakdowns'][4]["autoscore"].'/'.$question['breakdowns'][4]["maxscore"]) : 
                                write($question['breakdowns'][4]["manualscore"].'/'.$question['breakdowns'][4]["maxscore"]); ?>">
                        </input>
                    </div>
                </div>
                <div class="row justify-content-center"> <b> Your Current Score: 
                <?php
                    $max_score = 0;
                    $your_score = 0;
                    for($i = 0; $i < count($question['breakdowns']); $i++) {
                        $max_score += $question['breakdowns'][$i]['maxscore'];
                        ($question['breakdowns'][$i]["manualscore"] == null) ? 
                            $your_score += $question['breakdowns'][$i]["autoscore"] : $your_score += $question['breakdowns'][$i]["manualscore"];
                    }
                    write($your_score.'/'.$max_score);
                ?> </b>   
                </div>
            </div><br>
        <label style="font-weight: bold;">Comments:</label><br/>
        <?php if (count($question['comments']) > 0): ?>
            <?php foreach($question['comments'] as $cmt) : ?>
                Comment from <?php write($cmt['username']) ?>: <br/>
                <?php write($cmt['content']) ?><br/><br/>
            <?php endforeach; ?>
        <?php endif; ?>
        <label style="font-weight: bold;">Add a comment:</label><br/>
        <textarea type="text" name="sub_comment" rows="3" cols="100"></textarea><br/>
        <input type="hidden" name="sub_present" value="1" />
        <input type="hidden" name="sub_id" value="<?php write($question['id']) ?>" />
        <input type="submit" class="btn btn-primary" name="submit" value="Submit" />
    </form>
<?php endif; ?>

<style>
div.row, div.col {
  border: 1px solid black;
  text-align: center;
}
</style>

<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>