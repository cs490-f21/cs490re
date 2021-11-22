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
    $types = get($_POST, "types", null);
    $title = get($_POST, "title", null);
    $lvl = get($_POST, "lvl", null);
    $const = get($_POST, "constraint", null);
    $desc = get($_POST, "desc", null);
    $cases = get($_POST, "cases", null);

    $sortedCases = array(array($cases[0], $cases[1]), array($cases[2], $cases[3]),array($cases[4], $cases[5]),array($cases[6], $cases[7]),array($cases[8], $cases[9]));  

    $flag = true;

    if (!isset($types) || empty($types)) {
        addFlash("Question type is undefined", FLASH_WARN);
        $flag = false;
    }
    if (!isset($title) || empty($title)) {
        addFlash("Question must have a title", FLASH_WARN);
        $flag = false;
    }
    if (!isset($lvl) || empty($lvl)){
        addFlash("Question must have a difficulty", FLASH_WARN);
        $flag = false;
    }
    if (!isset($desc) || empty($desc)) {
        addFlash("Question must have a description", FLASH_WARN);
        $flag = false;
    }
    $flag = validate_cases($sortedCases);

    if ($flag) {
        create_problem($title, $types, $const, $lvl, $desc, $sortedCases); 
        addFlash("Question successfully created", FLASH_SUCC);
    }
}
?>
<div>
    <h1> Add Your Question To The Question Bank</h1>
    <div class="container">
        <div class="row">
            <div class="col">
                <form class="input-form" method="POST"> 
                    <div class="mb-3">
                        <label for="title">Title:</label>
                        <input type="text" class="form-control" name="title" placeholder="Question Title"> 
                    </div>
                    <div class="mb-3">
                        <label for="types">Question type:</label>
                        <select name="types">         
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
                        <label for="lvl">Difficulty:</label>
                        <select name="lvl">
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
                    <div class="mb-3">
                        <label for="cases[]">Test Case 4:</label>
                        <input type="text" name="cases[]" placeholder="Test Case 4">
                    </div>
                    <div class="mb-3">
                        <label for="cases[]">Test Case 4 Expected Output:</label>
                        <input type="text" name="cases[]" placeholder="Output for Test Case 4">
                    </div>
                    <div class="mb-3">
                        <label for="cases[]">Test Case 5:</label>
                        <input type="text" name="cases[]" placeholder="Test Case 5">
                    </div>
                    <div class="mb-3">
                        <label for="cases[]">Test Case 5 Expected Output:</label>
                        <input type="text" name="cases[]" placeholder="Output for Test Case 5">
                    </div>
                    <div>
                        <input type="submit" class="btn btn-primary" name="submit">
                    </div>
                </form> 
            </div>
            <div class="col" style="text-align:center">
                <h1>Filter By:</h1>
                <div class="mb-3">
                    <input type="radio" id="td" name="filter"></input>
                    <label for="question">Type/Difficulty</label>
                </div>
                <div class="mb-3">
                    <input type="radio" id="word" name="filter"></input>
                    <label for="word">Keyword</label><br>
                </div>
                <div id="qtype" style="display: none;">
                    <label><b>Question Type:</b></label>
                    <select id="type">
                        <option value="0">All Types</option>
                        <option value="1">For Loop</option>
                        <option value="2">While loop</option>
                        <option value="3">Recursion</option>
                        <option value="4">Conditional</option>
                        <option value="5">Strings</option>
                        <option value="6">Lists</option>             
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

                <div>
                    <h1>Question Bank</h1>
                </div>

                <div class="question-table">
                    <div class="row">
                        <div class="col select"></div>
                        <div class="col"> Description </div>
                    </div>
                    <?php $questions = load_problems(); ?>
                    <?php foreach($questions as $q): ?> 
                        <div name="<?php write($q['type'] . $q['level'])?>" class="row">
                            <div style="text-align:center" class="col select"> 
                                <input type="checkbox" name="q_id[]" value="<?php write($q['id']); ?>" > 
                            </div>
                                <div class="col">
                                <p id="<?php write($q['id']); ?>"> 
                                    <b><u>Id:</u></b> <?php write($q['id']); ?> <br>
                                    <b><u>Title:</u></b> <?php write($q['title']); ?> <br>                                  
                                    <b><u>Description:</u></b> <?php write($q['description']); ?> <br>
                                    <b><u>Type:</u></b> <?php write(display_type($q['type'])); ?> <br>
                                    <b><u>Level:</u></b> <?php write(display_level($q['level'])); ?> <br>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?> 
                </div>
                <br>
            </div>
        </div>
    </div>
</div>

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

