<?php

function create_problem(string $title, string $type, string $level, string $desc, array $cases, array $results) : Status {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Problems (title, type, level, description) 
                            VALUES (:title, :type, :level, :desc)");

    $message = '';
    try {
        $stmt->execute([":title" => $title, ":type" => $type, ":level" => $level, ":desc" => $desc]);
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

function create_test_case(int $q_id, array $cases, array $results) {
    $db = getDB();
    $q_id = $db->lastInsertId();

    $query = "INSERT INTO Testcases (for_problem, input, output) VALUES";
    for($i = 0; $i < 3; $i++) {
        $query .= "(:q_id, :case_i, :result_i)";
        if($i < 3) {
            $query .= ",";
        }
    }
    $stmt = $db->prepare($query);
    $stmt->bindValue(":q_id", $q_id);
    for($i = 0; $i < 3; $i++) {
        $stmt->bindValue(":case_i", $cases[i]);
        $stmt->bindValue("result_i", $results[i]);
    }
    $stmt->execute();

}

?> 
