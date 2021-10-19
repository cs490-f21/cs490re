<?php require_once(__DIR__ . '/lib.php'); ?>

<?php use_template('header.php', true, true); ?>

<title>Create your exam</title>

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

if (empty($_SESSION["list"])) {
    die(header("Location: createExam.php"));
}

if (isset($_POST["submit"])){
    $title = get($_POST, "title", null);
    $desc = get($_POST, "desc", null);
    $points = get($_POST, "points", null);
    $q_ids = $_SESSION["list"];

    $flag = true;

    if (!isset($title) || empty($title)) {
        addFlash("Exam must have a title", FLASH_WARN);
        $flag = false;
    }
    if (!isset($desc) || empty($desc)) {
        addFlash("Exam must have a description", FLASH_WARN);
        $flag = false;
    }
    for ($i = 0; $i < count($points); $i++) {
        if(empty($points[$i])) {
            addFlash("Each question must have points assigned to it", FLASH_WARN);
            $flag = false;
            break;
        }
        if(!is_numeric($points[$i])) {
            addFlash("Points must be an integer", FLASH_WARN);
            $flag = false;
            break;
        }
    }
    if ($flag) {
        createExam($title, $desc, $points, $q_ids);
        unset($_SESSION["list"]);
        header("Location: createExam.php");
    }
}
?>

<form method="POST">
    <label> Title: </label>
    <input type="text" name="title" placeholder="Exam title"> 
    <label> Description: </label>
    <textarea type="text" name="desc" placeholder="Question Description" rows="5"></textarea>

    <table>
    <tr>
        <th> Description </th>
        <th> Points worth </th>
    </tr>
        <?php 
        if (!empty($_SESSION["list"])) {
            $selected = getSelectedProblems($_SESSION["list"]); 
        }
        ?>
        <?php foreach($selected as $s): echo "<tr>" ?> 
        <?php echo "<td>"; ?> 
            <p id="<?php echo $s['id']; ?>"> 
                <b><u>Id:</u></b> <?php echo $s['id']; ?> <br>                                             
                <b><u>Title:</u></b> <?php echo $s['title']; ?> <br>                                             
                <b><u>Description:</u></b> <?php echo $s['description']; ?> <br>                                             
            </p>
        <?php echo "</td>" ?>
        <?php echo "<td>" ?>
            <input type="text" name="points[]" placeholder="Enter Points">
        <?php echo "</td>" ?>
        <?php endforeach; ?> 
    </table>
    <div>
        <input id="submit" type="submit" class="btn btn-primary" name="submit">
    </div>
</form>

<script>
    $("#submit").click(function() {
        alert("Exam Added");
    });
</script>

<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>