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
?>
<?php if (isset($_POST["q_id"]))  : ?>
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
            <?php $list = $_POST["q_id"]; ?>
            <?php $_SESSION["list"] = $list; ?>
            <?php $selected = getSelectedProblems($list); ?>
            <?php foreach($selected as $s): ?>
            <tr>
            <td>
                <p id="<?php write($s['id']); ?>"> 
                    <b><u>Id:</u></b> <?php write($s['id']); ?> <br>                                             
                    <b><u>Title:</u></b> <?php write($s['title']); ?> <br>                                             
                    <b><u>Description:</u></b> <?php write($s['description']); ?> <br>                                             
                </p>
            </td>
            <td> <input type="text" name="points[]" placeholder="Enter Points"> </td>
            <?php endforeach; ?> 
        </table>
        <div>
            <input id="submit" type="submit" class="btn btn-primary" name="submit">
        </div>
    </form>

<?php elseif (isset($_POST["submit"])) : ?>
<?php
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
        addFlash("Exam created", FLASH_SUCC);
        createExam($title, $desc, $points, $q_ids);
    }
?>
<?php else: ?>
    <div>
        <h1>Select questions to be on the exam</h1>
    </div>
    <div class="container">
        <div class="row">
            <div class="col">
                <h1>Filter By:</h1>
                <input type="radio" id="td" name="filter"></input>
                <label for="question">Type/Difficulty</label>
                <input type="radio" id="word" name="filter"></input>
                <label for="word">Keyword</label><br>
                <div id="qtype" style="display: none;">
                    <label><b>Question Type:</b></label>
                    <select id="type">
                        <option value="0">All Types</option>
                        <option value="1">For Loop</option>
                        <option value="2">While loop</option>
                        <option value="3">Recursion</option>
                    </select>
                </div>  
                <div id="difficulty" style="display: none;">
                    <label><b>Question Difficulty:</b></label>
                    <select id="level">
                        <option value="0">All Difficulty</option>
                        <option value="1">Easy</option>
                        <option value="2">Medium</option>
                        <option value="3">Hard</option>
                    </select>
                </div>
                <div id="kword" style="display: none;">
                    <label><b>Keyword:</b></label>
                    <input type="text" id="keyword"></input>
                </div>                
            </div>
            <div class="col">
                <div>
                    <h1>Question Bank</h1>
                </div>
                <form method="POST">
                    <table>
                        <tr>
                            <th> Checkbox </th>
                            <th> Description </th>
                        </tr>
                        <?php $questions = load_problems(); ?>
                        <?php foreach($questions as $q): ?> 
                        <tr name="<?php write($q['type'] . $q['level'])?>">
                            <td> <input type="checkbox" name="q_id[]" value="<?php write($q['id']); ?>" > </td>
                            <td>
                            <p id="<?php write($q['id']); ?>"> 
                                <b><u>Id:</u></b> <?php write($q['id']); ?> <br>
                                <b><u>Title:</u></b> <?php write($q['title']); ?> <br>                                  
                                <b><u>Description:</u></b> <?php write($q['description']); ?> <br>
                                <b><u>Type:</u></b> <?php write(display_type($q['type'])); ?> <br>
                                <b><u>Level:</u></b> <?php write(display_level($q['level'])); ?> <br>
                            </p>
                            </td>
                        </tr>
                        <?php endforeach; ?> 
                    </table>
                    <div>
                            <input type="submit" class="btn btn-primary" name="selection">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    if (isset($_POST["selection"])){ 
        $list = get($_POST, "q_id", null);
        if (empty($list)){
            addFlash("Exams must have a question");
        }
    } 
    ?>
<?php endif; ?>

<script>
    $(document).ready(function() {
        $("#type").change(function() {
            let type = document.getElementById("type").value;
            let level = document.getElementById("level").value;
            let row = document.getElementsByTagName("tr");
            let name = type + level;
            for(var i = 1; i < row.length; i++) {        
                console.log(row[i].getAttribute('name').charAt(0) + " = " + type + level + "   Type = " + type);
                if (row[i].getAttribute('name').charAt(0) != type && type != 0) {
                    row[i].style.display = "none";
                }
                else if (type == 0 && (row[i].getAttribute('name').charAt(1) == level || level == 0)){
                    row[i].style.display = "table-row";
                }
                else if (row[i].getAttribute('name') == name) {
                    row[i].style.display = "table-row";
                }
                else if (row[i].getAttribute('name').charAt(0) == type && level == 0) {
                    row[i].style.display = "table-row";
                }
            }
        })
        $("#level").change(function() {
            let level = document.getElementById("level").value;
            let type = document.getElementById("type").value;
            let row = document.getElementsByTagName("tr");
            let name = type + level;
            for(var i = 1; i < row.length; i++) {        
                if (row[i].getAttribute('name').charAt(1) != level && level != 0) {
                    row[i].style.display = "none";
                }
                else if (level == 0 && (row[i].getAttribute('name').charAt(0) == type || type == 0)){
                    row[i].style.display = "table-row";
                }
                else if (row[i].getAttribute('name') == name) {
                    row[i].style.display = "table-row";
                }
                else if (row[i].getAttribute('name').charAt(1) == level && type == 0) {
                    row[i].style.display = "table-row";
                }
            }
        })
        $("#keyword").change(function() {
            let row = document.getElementsByTagName("tr");
            let keyword = document.querySelector('#keyword').value;
            keyword = keyword.toLowerCase();
            for(var i = 1; i < row.length; i++) {        
                var desc = row[i].textContent.match(/(?<=Description: ).*/);
                desc = desc[0].toLowerCase();
                if(desc.includes(keyword)) {
                    row[i].style.display = "table-row";
                }
                else {
                    row[i].style.display = "none";
                }
            }
        })
        $("#td").change(function() {
            if(document.getElementById('td').checked) {
                document.getElementById('kword').style.display = "none";
                document.getElementById('qtype').style.display = "block";
                document.getElementById('difficulty').style.display = "block";
                document.querySelector('#keyword').value = "";
            }
            else {
                document.getElementById('kword').style.display = "block";
                document.getElementById('qtype').style.display = "none";
                document.getElementById('difficulty').style.display = "none";
            }
        })
        $("#word").change(function() {
            if(document.getElementById('word').checked) {
                document.getElementById('qtype').style.display = "none";
                document.getElementById('difficulty').style.display = "none";
                document.getElementById('kword').style.display = "block";
                document.getElementById("type").value = 0;
                document.getElementById("level").value = 0;
                document.getElementsByTagName("tr").style.display = "table-row";
            }
            else {
                document.getElementById('qtype').style.display = "block";
                document.getElementById('difficulty').style.display = "block";
                document.getElementById('kword').style.display = "none";

            }
        })
    });


</script>

<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>