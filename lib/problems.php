<?php

function create_problem(string $title, int $type, int $level, string $desc, array $cases) : Status {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Problems (title, type, level, description) 
                            VALUES (:title, :type, :level, :desc)");

    $message = '';
    try {
        $stmt->execute([":title" => $title, ":type" => $type, ":level" => $level, ":desc" => $desc]);
        $q_id = $db->query("SELECT MAX(id) from Problems")->fetch();
        foreach($cases as $case) {    
            $stmt = $db->prepare("INSERT INTO Testcases (for_problem, input, output) VALUES (:q_id, :cases, :result)");
            $stmt->bindValue(':q_id', implode("", $q_id));
            $stmt->bindValue(':cases', $case[0]);
            $stmt->bindValue(':result', $case[1]);
            $stmt->execute();
        }
        $message = 'INS_SUCCESS';
    }
    catch (PDOexception $e){ 
        $message = 'INS_FAIL';
    }   
    return new Status($message);
}

function load_problems(){
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Problems");
    $stmt->execute();
    return $stmt->fetchAll();
}


?> 
