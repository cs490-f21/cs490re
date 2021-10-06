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
    $cases = get($_POST, "cases[]", null);
    $results = get($_POST, "results[]", null);

    $flag = true;
    $totalCases = 0;

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
    if(!isset($cases) || !isset($results) || count($cases) != 3 || count($results) != 3) {
        addFlash("Missing test cases or results", FLASH_WARN);
        $flag = false;
    }
    if($flag) {
        create_problem($title, $type, $level, $desc); 
        create_test_case($cases, $results);
    }
}

?>

<div>
    <h1> Fill in your question </h1>
    <form class="input-form" method="POST"> 
        <div class="mb-3">
            <label for="title">Title:</label>
            <input type="text" class="form-control" name="title" placeholder="Question Title"> 
        </div>
        <div class="mb-3">
            <label for="type">Question type:</label>
            <input type="text" class="form-control" name="type" placeholder="Conditional, Loops, etc)">  
        </div>
        <div class="mb-3">
            <label for="level">Difficulty:</label>
            <input type="text" class="form-control" name="level" placeholder="Difficulty (Easy, Medium, Hard)">
        </div>
        <div class="mb-3">
            <label for="desc">Description:</label>
            <textarea type="text" class="form-control" name="desc" placeholder="Question Description" rows="5"></textarea>
        </div>

        <h1> Test Cases: </h1>
        <div class="mb-3">
            <label for="cases[]">Test Case 1:</label>
            <input type="text" name="cases[]" placeholder="Test Case 1">
        </div>
        <div class="mb-3">
            <label for="results[]">Test Case 1 Expected Output:</label>
            <input type="text" name="results[]" placeholder="Output for Test Case 1">
        </div>
        <div class="mb-3">
            <label for="cases[]">Test Case 2:</label>
            <input type="text" name="cases[]" placeholder="Test Case 2">
        </div>
        <div class="mb-3">
            <label for="results[]">Test Case 2 Expected Output:</label>
            <input type="text" name="results[]" placeholder="Output for Test Case 2">
        </div>
        <div class="mb-3">
            <label for="cases[]">Test Case 3:</label>
            <input type="text" name="cases[]" placeholder="Test Case 3">
        </div>
        <div class="mb-3">
            <label for="results[]">Test Case 3 Expected Output:</label>
            <input type="text" name="results[]" placeholder="Output for Test Case 3">
        </div>
        <div>
            <input type="submit" class="btn btn-primary" name="submit">
        </div>
    </form> 
</div>


<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>

