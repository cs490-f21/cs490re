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
        $valid = false;
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

    if ($sub_score != $question['point']) {
        $status = change_grade($sub_id, $sub_score);
        $valid &= $status->isSuccess();
        addStatus($status, FLASH_SUCC);
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
        <div><span style="font-weight: bold;">Current score: <input type="number" name="sub_score" value="<?php write($question['point']) ?>" /> / <?php write($question['possible']) ?></span></div><br/>
        <div><p style="font-family: "><?php write($question['description']) ?></p></div><br/>
        <label style="font-weight: bold;">Submitted answer:</label><br/>
        <textarea type="text" readonly="true" name="sub_answer" rows="15" cols="100"><?php write($question['answer']) ?></textarea><br/>
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

<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>