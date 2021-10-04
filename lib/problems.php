<?php

function create_problem(string $title, string $type, string $level, string $desc) : Status {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Problem (title, type, level, description) 
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

function load_problems() : array{
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Problems");
    $stmt->execute();
    return $stmt->fetchAll();
}

?> 
