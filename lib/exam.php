<?php

function getSelectedProblems(array $ids) {
    $db = getDB();
    $results = [];
    for($i = 0; $i < count($ids); $i++) {
        $stmt = $db->query("SELECT * FROM Problems WHERE id=$ids[$i]");
        $results[$i] = $stmt->fetch();
    }
    return $results;
}

function createExam(string $title, string $desc, array $points, array $question) : Status {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Exams (title, description, point) VALUES (:title, :desc, :totalpoints)");

    $message = '';
    try {
        $stmt->execute(array(":title" => $title, ":desc" => $desc, ":totalpoints" => array_sum($points)));
        $e_id = $db->query("SELECT MAX(id) from Exams")->fetch();
        $stmt = $db->prepare("INSERT INTO ExamParts (for_exam, part_order, with_problem, point) 
            VALUES (:e_id, :order, :problem, :pointworth)");
        $order = 1;
        for($i = 0; $i < count($question); $i++) {
            $stmt->bindValue(':e_id', implode("", $e_id));
            $stmt->bindValue(':order', $order);
            $stmt->bindValue(':problem', $question[$i]);
            $stmt->bindValue(':pointworth', $points[$i]);
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

function generateExamId() {
    $db = getDB();
    $stmt = $db->query("SELECT id, title FROM Exams");
    return $stmt->fetchAll();
}

function generateExam(int $id) {
    $db = getDB();
    $stmt = $db->query("SELECT id, title, description FROM Problems WHERE id in (SELECT with_problem FROM ExamParts WHERE $id=for_exam)");
    return $stmt->fetchAll();
}

function getExamPartDetails(int $exam_id) {
    $db = getDB();
    $stmt = $db->query("SELECT * from ExamParts where for_exam=$exam_id");
    return $stmt->fetchAll();

}

function submitExam(array $p_id, int $u_id, array $answers) : Status {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Submissions (for_part, from_student, answer) VALUES (:part, :student, :answer)");
    $message = '';
    try {
        for($i = 0; $i < count($p_id); $i++) {
            $stmt->bindValue(':part', $p_id[$i]);
            $stmt->bindValue(':student', $u_id);
            $stmt->bindValue(':answer', $answers[$i]);
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

function updateExamStatus(int $exam, int $student, int $status) : Status {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO ExamStatus (for_exam, from_student, status) VALUES ($exam,$student,$status)
    ON CONFLICT ON CONSTRAINT examstatus_for_exam_from_student_key
    DO UPDATE SET status = EXCLUDED.status
    ");
    $message = '';
    try {
        $stmt->execute();
        $message = 'INS_SUCCESS';
    }
    catch (PDOexception $e){ 
        $message = 'INS_FAIL';
    }  
    return new Status($message);

}

function getExamStatus(int $exam, int $student) {
    $db = getDB();
    $stmt = $db->prepare("SELECT status from ExamStatus WHERE for_exam = :exam AND from_student = :student LIMIT 1");
    $message = '';
    $status = 0;
    try {
        $stmt->execute([':exam' => $exam, ':student' => $student]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $status = $result["status"];
        }
        $message = 'INS_SUCCESS';
    }
    catch (PDOexception $e){ 
        $message = 'INS_FAIL';
    }  

    return $status;
}





?>