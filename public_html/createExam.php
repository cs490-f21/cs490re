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

if (isset($_POST["submit"])) {
    $title = get($_POST, "title", null);
    $desc = get($_POST, "desc", null);
    $points = get($_POST, "points", null);
    $q_ids = get($_POST, "qid", null);
    $flag = true;

    if (!isset($title) || empty($title)) {
        addFlash("Exam must have a title", FLASH_WARN);
        $flag = false;
    }
    if (!isset($desc) || empty($desc)) {
        addFlash("Exam must have a description", FLASH_WARN);
        $flag = false;

    }
    if(empty($points)) {
        addFlash("Questions do not have points", FLASH_WARN);
        $flag = false;
    }
    else {
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
    }
    if ($flag) {
        addFlash("Exam created", FLASH_SUCC);
        createExam($title, $desc, $points, $q_ids);
    }
}
?>
<div>
    <h1>Create The Exam</h1>
</div>
<div class="container">
    <div class="row">
        <div class="col" style="text-align: center">
            <h1>Assign Points To Questions</h1>
            <form method="POST" class="form-control">
                <div class="mb-3">
                    <label> Title: </label>
                    <input type="text" name="title" class="form-control" placeholder="Exam title"> 
                </div>
                <div class="mb-3">
                    <label> Description: </label>
                    <textarea type="text" name="desc" class="form-control" placeholder="Question Description" rows="5"></textarea>
                </div>
                <table> 
                    <tr>
                        <th> Selected Questions </th>
                        <th> Points </th>
                    </tr>
                </table></br>
                <div>
                    <input type="submit" class="btn btn-primary" name="submit" value="Create Exam">
                </div>   
            </form> 
        </div>
        <div class="col" style="text-align: center">
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
            <div><h1>Question Bank</h1></div>
            <table class="question-table">
                <tr class="row">
                    <th class="col select">  </th>
                    <th class="col"> Description </th>
                </tr>
                <?php $questions = load_problems(); ?>
                <?php foreach($questions as $q): ?> 
                <tr name="<?php write($q['type'] . $q['level'])?>" class="row bank">
                    <td class="col select">
                        <input type="checkbox" name="q_id[]" value="<?php write($q['id']); ?>" onclick="handle(<?php write($q['id']); ?>, <?php write(json_encode($q)); ?>)" > 
                    </td>
                    <td class="col">
                        <p id="<?php write($q['id']); ?>"> 
                            <b><u>Title:</u></b> <?php write($q['title']); ?> <br>                                  
                            <b><u>Description:</u></b> <?php write($q['description']); ?> <br>
                            <b><u>Type:</u></b> <?php write(display_type($q['type'])); ?> <br>
                            <b><u>Level:</u></b> <?php write(display_level($q['level'])); ?> <br>
                        </p>
                    </td>
                </tr>
                <?php endforeach; ?> 
            </table><br>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#type").change(function() {
            let type = document.getElementById("type").value;
            let level = document.getElementById("level").value;
            let row = document.querySelectorAll(".bank");
            let name = type + level;    
            for(var i = 0; i < row.length; i++) {  
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
            let row = document.querySelectorAll(".bank");
            let name = type + level;
            for(var i = 0; i < row.length; i++) {        
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
            let row = document.querySelectorAll(".bank");
            let keyword = document.querySelector('#keyword').value;
            keyword = keyword.toLowerCase();
            for(var i = 0; i < row.length; i++) {        
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
                let arr = document.querySelectorAll(".bank");
                for(let i = arr.length - 1; i >= 0; i--)
                    arr[i].style.display = "table-row";
            }
            else {
                document.getElementById('qtype').style.display = "block";
                document.getElementById('difficulty').style.display = "block";
                document.getElementById('kword').style.display = "none";

            }
        })
    });

    function handle(id, data) {
        console.log(data.id);
        var boxid = document.getElementsByName('q_id[]');
        for (var i=0; i < boxid.length; i++) {
            if (boxid[i].value == id) {
                if (boxid[i].checked) {
                    addToForm(id, data);
                }
                else{
                    removeFromForm(id);
                }
            }
        }
    }    

    function addToForm(id, data) {
        var table = document.getElementsByTagName('table');
        var row = document.createElement("tr");
        row.setAttribute("id", id);

        var td1 = document.createElement("td");
        var desc = document.createElement("p");
        desc.innerHTML = "<b><u>Id:</u></b> " + data.id + "</br>" + 
                         "<b><u>Title:</u></b> " + data.title + "</br>" + 
                         "<b><u>Description:</u></b> " + data.description + "</br>";
        td1.appendChild(desc);   

        var td2 = document.createElement("td");
        var input = document.createElement("input");
        input.setAttribute("type", "text");
        input.setAttribute("name", "points[]");
        input.setAttribute("placeholder","Enter Points");
        td2.appendChild(input);

        var hidden = document.createElement("input");
        hidden.setAttribute("type", "hidden");
        hidden.setAttribute("name", "qid[]");
        hidden.setAttribute("value", data.id);

        table[0].appendChild(row);
        row.appendChild(hidden);
        row.appendChild(td1);
        row.appendChild(td2);
    }

    function removeFromForm(id) {
        var table = document.getElementsByTagName('table');
        var row = document.getElementById(id);
        table[0].removeChild(row);        
    }

</script>

<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>