<?php

function create_problem(string $title, int $type, int $const, int $level, string $desc, array $cases) : Status {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Problems (title, type, const, level, description) 
                            VALUES (:title, :type, :const, :level, :desc)");

    $message = '';
    try {
        $stmt->execute(array(":title" => $title, ":type" => $type, ":const" => $const, ":level" => $level, ":desc" => $desc));
        $q_id = $db->query("SELECT MAX(id) from Problems")->fetch();
        $stmt = $db->prepare("INSERT INTO Testcases (for_problem, case_order, title, input, output, weight) 
            VALUES (:q_id, :order, :title, :case, :result, :weight)");
        $order = 1;
        foreach($cases as $case) {    
            $stmt->bindValue(':q_id', implode("", $q_id));
            $stmt->bindValue(':order', $order);
            $stmt->bindValue(':title', "Testcase");
            $stmt->bindValue(':case', $case[0]);
            $stmt->bindValue(':result', $case[1]);
            $stmt->bindValue(':weight', 1);
            $order++;
            $result = $stmt->execute();
            $result = [];
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

function validate_cases(array $cases) {
    $min_cases = 1;
    $case_count = 0;
    $isValid = true;
    foreach($cases as $case) {
        if(isset($case[0]) && isset($case[1])) {
            $case_count++;
        } else{
            addFlash("Case or expected output is missing, or both", FLASH_WARN);
            return $isValid = false;
        }
    }
    if ($case_count < $min_cases) {
        addFlash("Minimum of 1 test case required");
        $isValid = false;
    }
    return $isValid;
}

function display_type(int $num): string {
    
    if($num == 1) {
        return "For Loop";
    }
    if($num == 2) {
        return "While Loop";
    }
    if($num == 3) {
        return "Recursion";
    }
}

function display_level(int $num): string {
    
    if($num == 1) {
        return "Easy";
    }
    if($num == 2) {
        return "Medium";
    }
    if($num == 3) {
        return "Hard";
    }
}

?> 
