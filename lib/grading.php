<?php // author: Jiyuan Zhang

function change_grade(int $submission, int | null $grade) {
    $db = getDB();
    $stmtSub = $db->prepare(
        "SELECT s.id, ep.point as possible
            FROM Submissions s
            JOIN ExamParts ep
                ON ep.id = s.for_part
            WHERE s.id = :sub"
    );

    $stmtApply = $db->prepare(
        "UPDATE Submissions SET point = :grade WHERE id = :id"
    );

    $stmtClear = $db->prepare(
        "UPDATE Submissions SET point = NULL WHERE id = :id"
    );

    $message = 'GRD_UNKNOWN';

    try {
        $stmtSub->execute([":sub" => $submission]);
        $sub = $stmtSub->fetch(PDO::FETCH_ASSOC);

        if ($grade != null) {
            $grade = min($grade, $sub['possible']);
            $grade = max($grade, 0);

            $stmtApply->execute([":grade" => $grade, ":id" => $submission]);
        }
        else {
            $stmtClear->execute([":id" => $submission]);
        }

        $message = 'GRD_SUCCESS';
    } catch (Exception $e) {
        $message = 'GRD_INTERERR';
    }

    return new Status($message);
}

function autograde_exam(int $exam): Status {
    $db = getDB();
    $stmtSub = $db->prepare(
        "SELECT s.id, s.answer, s.point, ep.with_problem as problem
            FROM Submissions s
            JOIN ExamParts ep
                ON ep.id = s.for_part
            WHERE s.point is NULL AND ep.for_exam = :exam
            ORDER BY ep.with_problem"
    );

    $stmtProb = $db->prepare(
        "SELECT p.id, t.case_order as order, t.input, t.output, t.weight, ep.point
            FROM ExamParts ep
            JOIN Problems p
                ON ep.with_problem = p.id
            JOIN Testcases t
                ON p.id = t.for_problem
            WHERE ep.for_exam = :exam
            ORDER BY p.id ASC, t.case_order ASC"
    );

    $argSub = [":exam" => $exam];
    $argProb = [":exam" => $exam];

    return __autograde_core($db, $stmtSub, $argSub, $stmtProb, $argProb);
}

function autograde_all(): Status {
    $db = getDB();
    $stmtSub = $db->prepare(
        "SELECT s.id, s.answer, s.point. s.message, ep.with_problem as problem
            FROM Submissions s
            JOIN ExamParts ep
                ON ep.id = s.for_part
            WHERE s.point is NULL
            ORDER BY ep.with_problem"
    );

    $stmtProb = $db->prepare(
        "SELECT p.id, t.case_order as order, t.input, t.output, t.weight, ep.point
            FROM ExamParts ep
            JOIN Problems p
                ON ep.with_problem = p.id
            JOIN Testcases t
                ON p.id = t.for_problem
            ORDER BY p.id ASC, t.case_order ASC"
    );

    $argSub = [];
    $argProb = [];

    return __autograde_core($db, $stmtSub, $argSub, $stmtProb, $argProb);
}

function __autograde_core(object $db, object $stmtSub, array $argSub, object $stmtProb, array $argProb): Status {
    $stmtApply = $db->prepare(
        "UPDATE Submissions SET point = :grade, result1 = :result1, result2 = :result2, result3 = :result3 WHERE id = :id"
    );

    $message = 'GRD_UNKNOWN';

    try {
        $stmtSub->execute($argSub);
        $submissions = $stmtSub->fetchAll(PDO::FETCH_ASSOC);
        $stmtProb->execute($argProb);
        $rawprob = $stmtProb->fetchAll(PDO::FETCH_ASSOC);

        if (!$rawprob || !$submissions) {
            $message = 'GRD_NOPENDING';
            goto Fail;
        }

        $problems = [];
        foreach($rawprob as $problem) {
            if (isset($problems[$problem["id"]])) {
                array_push($problems[$problem["id"]], $problem);
                $problems[$problem["id"]][0] += $problem["weight"];
            }
            else {
                $problems[$problem["id"]] = [$problem["weight"], $problem];
            }
        }

        foreach($problems as $key => $testcases) {
            $sum = array_shift($problems[$key]);

            foreach($problems[$key] as $index => $testcase) {
                $problems[$key][$index]["point"] *= 1.0 * $problems[$key][$index]["weight"] / $sum;
            }
        }

        foreach ($submissions as $id => $submission) {
            $pid = $submission["problem"];
            if (!isset($problems[$pid])) continue;

            $testcases = $problems[$pid];
            $submissions[$id]["point"] = 0;

            $i = 1;
            foreach($testcases as $testcase) {
                $code = $submission["answer"];
                $fixed = fix_function_name($code, $testcase["input"]);
                $result = run_judgement($code, $testcase["input"], $testcase["output"]);
                
                if ($result[0]->isSuccess()) {
                    $submissions[$id]["point"] += $testcase["point"] * ($fixed ? 1.0 : 0.8);
                    $submissions[$id]["message" . $i] = $fixed ? "Passed: Success" : "Passed: Wrong Function Name";
                }
                else {
                    $submissions[$id]["message" . $i] = $result[1];
                }

                $i++;
            }
        }

        foreach($submissions as $submission) {
            $stmtApply->execute([
                ":grade" => intval($submission["point"]), 
                ":result1" => $submission["message1"], 
                ":result2" => $submission["message2"], 
                ":result3" => $submission["message3"], 
                ":id" => $submission["id"]
            ]);
        }

        $message = 'GRD_SUCCESS';
    
        Fail:;
    } catch (Exception $e) {
        $message = 'GRD_INTERERR';
    }

    return new Status($message);
}

function collect_result_submission(int $submission, array &$results) {
    $db = getDB();
    $stmtSub = $db->prepare(
        "SELECT s.id, s.from_student, u.username, 
                ep.point as possible, s.point, 
                s.answer, s.result1, s.result2, s.result3, created as answer_time,
                ep.part_order, p.title, p.description, p.level, p.type
            FROM Submissions s
            JOIN ExamParts ep
                ON ep.id = s.for_part
            JOIN Problems p
                ON ep.with_problem = p.id
            JOIN Users u
                ON s.from_student = u.id
            WHERE s.id = :sub
            LIMIT 1"
    );

    $argSub = [":sub" => $submission];

    return __collect_result_core($db, $stmtSub, $argSub, $results);
}

function collect_result_user(int $exam, array &$results, int $user = -1) {
    if ($user == -1) {
        if (!user_login_check())
            return new Status('USR_UNAUTHED');
        
        $user = user_get_id();
    }

    $db = getDB();
    $stmtSub = $db->prepare(
        "SELECT s.id, s.from_student, u.username, 
                ep.point as possible, s.point, 
                s.answer, s.result1, s.result2, s.result3, s.created as answer_time,
                ep.part_order, p.title, p.description, p.level, p.type
            FROM Submissions s
            JOIN ExamParts ep
                ON ep.id = s.for_part
            JOIN Problems p
                ON ep.with_problem = p.id
            JOIN Users u
                ON s.from_student = u.id
            WHERE ep.for_exam = :exam AND s.from_student = :user
            ORDER BY ep.part_order ASC"
    );

    $argSub = [":exam" => $exam, ":user" => $user];

    return __collect_result_core($db, $stmtSub, $argSub, $results);
}

function collect_result_all(int $exam, array &$results) {
    $db = getDB();
    $stmtSub = $db->prepare(
        "SELECT s.id, s.from_student, u.username, 
                ep.point as possible, s.point, 
                s.answer, s.result1, s.result2, s.result3, s.created as answer_time,
                ep.part_order, p.title, p.description, p.level, p.type
            FROM Submissions s
            JOIN ExamParts ep
                ON ep.id = s.for_part
            JOIN Problems p
                ON ep.with_problem = p.id
            JOIN Users u
                ON s.from_student = u.id
            WHERE ep.for_exam = :exam
            ORDER BY s.from_student ASC, ep.part_order ASC"
    );

    $argSub = [":exam" => $exam];

    return __collect_result_core($db, $stmtSub, $argSub, $results);
}

function __collect_result_core(object $db, object $stmtSub, array $argSub, array &$results): Status {
    $message = 'GRD_UNKNOWN';
    $results = [];

    try {
        $stmtSub->execute($argSub);
        $results = $stmtSub->fetchAll(PDO::FETCH_ASSOC);

        $message = 'GRD_SUCCESS';

    } catch (Exception $e) {
        $message = 'GRD_INTERERR';
    }

    return new Status($message);
}
