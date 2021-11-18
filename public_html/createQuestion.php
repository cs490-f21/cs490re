<?php require_once(__DIR__ . '/lib.php'); ?>

<?php use_template('header.php', true, true); ?>

<title>Create your question</title>

<?php use_template('resource.php', true, true); ?>
<?php use_template('nav.php', true, true); ?>

<?php
if (!user_login_check()) {
    die(header("Location: login.php"));
}

if (user_login_check()) {
    user_reload();
}

if (!user_admin_check()) {
    die(header("Location: home.php"));
}

if (isset($_POST['submit'])) {
    $type = get($_POST, "type", null);
    $title = get($_POST, "title", null);
    $level = get($_POST, "level", null);
    $const = get($_POST, "constraint", null);
    $desc = get($_POST, "desc", null);
    $cases = get($_POST, "cases", null);

    $sortedCases = array(array($cases[0], $cases[1]), array($cases[2], $cases[3]),array($cases[4], $cases[5]),array($cases[6], $cases[7]),array($cases[8], $cases[9]));  

    $flag = true;

    if (!isset($type) || empty($type)) {
        addFlash("Question type is undefined", FLASH_WARN);
        $flag = false;
    }
    if (!isset($title) || empty($title)) {
        addFlash("Question must have a title", FLASH_WARN);
        $flag = false;
    }
    if (!isset($level) || empty($level)){
        addFlash("Question must have a difficulty", FLASH_WARN);
        $flag = false;
    }
    if (!isset($desc) || empty($desc)) {
        addFlash("Question must have a description", FLASH_WARN);
        $flag = false;
    }
    $flag = validate_cases($sortedCases);

    if ($flag) {
        create_problem($title, $type, $const, $level, $desc, $sortedCases); 
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
               <option value="">Select here</option>
                <option value="1">For Loop</option>
                <option value="2">While Loop</option>
                <option value="3">Recursion</option>
                <option value="4">Conditional</option>
                <option value="5">Strings</option>
                <option value="6">Lists</option>

            </select>
        </div>
        <div class="mb-3">
            <label for="constraint">Question Constraint:</label>
            <select name="constraint">         
               <option value="0">No Constraint</option>
                <option value="1">For Loop</option>
                <option value="2">While Loop</option>
                <option value="3">Recursion</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="level">Difficulty:</label>
            <select name="level">
                <option value="">Select here</option>
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
            <input type="text" name="cases[]" placeholder="Test Case 1">
        </div>
        <div class="mb-3">
            <label for="cases[]">Test Case 1 Expected Output:</label>
            <input type="text" name="cases[]" placeholder="Output for Test Case 1">
        </div>
        <div class="mb-3">
            <label for="cases[]">Test Case 2:</label>
            <input type="text" name="cases[]" placeholder="Test Case 2">
        </div>
        <div class="mb-3">
            <label for="cases[]">Test Case 2 Expected Output:</label>
            <input type="text" name="cases[]" placeholder="Output for Test Case 2">
        </div>
        <div class="mb-3">
            <label for="cases[]">Test Case 3:</label>
            <input type="text" name="cases[]" placeholder="Test Case 3">
        </div>
        <div class="mb-3">
            <label for="cases[]">Test Case 3 Expected Output:</label>
            <input type="text" name="cases[]" placeholder="Output for Test Case 3">
        </div>
        <div>
            <input type="submit" class="btn btn-primary" name="submit">
        </div>
        <div class="mb-3">
            <label for="cases[]">Test Case 4:</label>
            <input type="text" name="cases[]" placeholder="Test Case 3">
        </div>
        <div class="mb-3">
            <label for="cases[]">Test Case 4 Expected Output:</label>
            <input type="text" name="cases[]" placeholder="Output for Test Case 3">
        </div>
        <div>
            <input type="submit" class="btn btn-primary" name="submit">
        </div>
        <div class="mb-3">
            <label for="cases[]">Test Case 5:</label>
            <input type="text" name="cases[]" placeholder="Test Case 3">
        </div>
        <div class="mb-3">
            <label for="cases[]">Test Case 5 Expected Output:</label>
            <input type="text" name="cases[]" placeholder="Output for Test Case 3">
        </div>
        <div>
            <input type="submit" class="btn btn-primary" name="submit">
        </div>
    </form> 
</div>

<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>

