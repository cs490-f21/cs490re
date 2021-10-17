<?php require_once(__DIR__ . '/lib.php'); ?>

<?php use_template('header.php', true, true); ?>

<title>Exam in Progress</title>

<?php use_template('resource.php', true, true); ?>
<?php use_template('nav.php', true, true); ?>

<?php

if (user_login_check()) {
    user_reload();
}

if(empty($_SESSION["exam"])) {
    die(header("Location: selectExam.php"));
}

if(isset($_POST['submit'])) {
    $user_id = user_get_id();
    $solutions = get($_POST, "solutions", null);
    $parts = getExamPartDetails($_SESSION["exam"]);
    $part_id = [];

    foreach($parts as $p){
        array_push($part_id, $p['id']);
    }

    submitExam($part_id, $user_id, $solutions);
    unset($_SESSION["exam"]);
    header("Location: selectExam.php");
}
?>

<form method="POST">
    <?php 
    if(!empty($_SESSION["exam"])) {
        $questions = generateExam((int)$_SESSION["exam"]);
        $q_order = 1;
    }
    ?>
    <h1> Taking exam <?php echo $_SESSION["exam"]; ?>. Good luck!! </h1>
    <?php foreach($questions as $q) : ?>
    <div>
        <?php echo $q_order . ") " . $q["description"]; $q_order++;?>
    </div>
    <div>
        <label for="solutions[]">Write your code here:</label>
        <textarea type="text" name="solutions[]" placeholder="Code Here" rows="15" cols="100"></textarea><br><br>
    </div>
    <?php endforeach; ?>
    <div>
        <input type="submit" class="btn btn-primary" name="submit">
    </div>

</form>



<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>
