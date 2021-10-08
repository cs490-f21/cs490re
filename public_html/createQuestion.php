<?php require_once(__DIR__ . '/lib.php'); ?>

<?php use_template('header.php', true, true); ?>

<title>Create your question</title>

<?php use_template('resource.php', true, true); ?>
<?php use_template('nav.php', true, true); ?>

<?php
if (user_login_check()) {
    user_reload();
}
if (!user_admin_check()) {
    die(header("Location: home.php"));
}

if(isset($_POST['submit'])) {
    $type = get($_POST, "type", null);
    $title = get($_POST, "title", null);
    $level = get($_POST, "level", null);
    $desc = get($_POST, "desc", null);
    $cases = get($_POST, "cases", null);

    $flag = true;

    if(!isset($type)) {
        addFlash("Question type is undefined", FLASH_WARN);
        $flag = false;
    }
    if(!isset($title)) {
        addFlash("Questions must have a title", FLASH_WARN);
        $flag = false;
    }
    if(!isset($level)){
        addFlash("Questions must have a difficulty", FLASH_WARN);
        $flag = false;
    }
    if(!isset($desc)) {
        addFlash("Questions must have a description", FLASH_WARN);
        $flag = false;
    }
    if(!isset($cases)) {
        addFlash("Missing test cases or results", FLASH_WARN);
        $flag = false;
    }
    if($flag) {
        create_problem($title, $type, $level, $desc, $cases); 
        addFlash("Question successfully created", FLASH_SUCC);
    }
}
?>


<div>
    <h1> Add Your Question To The Question Bank</h1>
    <form class="input-form" method="POST"> 
        <div class="mb-3">
            <label for="title">Title:</label>
            <input type="text" class="form-control" name="title" placeholder="Question Title"> 
        </div>
        <div class="mb-3">
            <label for="type">Question type:</label>
            <select name="type">
                <option value="1">Loops</option>
                <option value="2">Conditional</option>
                <option value="3">Dictionary</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="level">Difficulty:</label>
            <select name="level">
                <option value="1">Easy</option>
                <option value="2">Medium</option>
                <option value="3">Hard</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="desc">Description:</label>
            <textarea type="text" class="form-control" name="desc" placeholder="Question Description" rows="5"></textarea>
        </div>

        <h1> Test Cases: </h1>
        <div class="mb-3">
            <label for="cases[]">Test Case 1:</label>
            <input type="text" name="cases[0][0]" placeholder="Test Case 1">
        </div>
        <div class="mb-3">
            <label for="cases[]">Test Case 1 Expected Output:</label>
            <input type="text" name="cases[0][1]" placeholder="Output for Test Case 1">
        </div>
        <div class="mb-3">
            <label for="cases[]">Test Case 2:</label>
            <input type="text" name="cases[1][0]" placeholder="Test Case 2">
        </div>
        <div class="mb-3">
            <label for="casess[]">Test Case 2 Expected Output:</label>
            <input type="text" name="cases[1][1]" placeholder="Output for Test Case 2">
        </div>
        <div class="mb-3">
            <label for="cases[]">Test Case 3:</label>
            <input type="text" name="cases[2][0]" placeholder="Test Case 3">
        </div>
        <div class="mb-3">
            <label for="cases[]">Test Case 3 Expected Output:</label>
            <input type="text" name="results[2][1]" placeholder="Output for Test Case 3">
        </div>
        <div>
            <input type="submit" class="btn btn-primary" name="submit">
        </div>
    </form> 
</div>


<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>

