<?php

function create_problem(string $title, int $type, int $level, string $desc, array $cases) : Status {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Problems (title, type, level, description) 
                            VALUES (:title, :type, :level, :desc)");

    $message = '';
    try {
        $stmt->execute([":title" => $title, ":type" => $type, ":level" => $level, ":desc" => $desc]);
        $q_id = $db->lastInsertId("Problems");
        $stmt = $db->prepare("INSERT INTO Testcases (for_problem, input, output) VALUES (:q_id, :cases, :result)");
        for($i=0; $i<3; $i++) {
            $stmt->bindValue(':q_id', $q_id);
            $stmt->bindValue(':cases', $cases[$i][0]);
            $stmt->bindValue(':result', $cases[$i][1]);
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
