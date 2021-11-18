<?php // author: Jiyuan Zhang

function change_grade(int $breakdown, int | null $grade) {
    $db = getDB();
    $stmtSub = $db->prepare(
        "SELECT maxscore
            FROM Breakdowns
            WHERE id = :brk"
    );

    $stmtApply = $db->prepare(
        "UPDATE Breakdowns SET manualscore = :grade WHERE id = :id"
    );

    $stmtClear = $db->prepare(
        "UPDATE Breakdowns SET manualscore = NULL WHERE id = :id"
    );

    $message = 'GRD_UNKNOWN';

    try {
        $stmtSub->execute([":brk" => $breakdown]);
        $sub = $stmtSub->fetch(PDO::FETCH_ASSOC);

        if ($grade != null) {
            $grade = min($grade, $sub['maxscore']);
            $grade = max($grade, 0);

            $stmtApply->execute([":grade" => $grade, ":id" => $breakdown]);
        }
        else {
            $stmtClear->execute([":id" => $breakdown]);
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
        "SELECT s.id, s.answer, s.from_student as student, ep.with_problem as problem, ep.for_exam as exam
            FROM Submissions s
            JOIN ExamParts ep
                ON ep.id = s.for_part
            WHERE ep.for_exam = :exam
            ORDER BY ep.with_problem"
    );

    $stmtProb = $db->prepare(
        "SELECT p.id, t.case_order as order, t.input, t.output, t.weight, ep.point, p.const
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
        "SELECT s.id, s.answer, s.from_student as student, ep.with_problem as problem, ep.for_exam as exam
            FROM Submissions s
            JOIN ExamParts ep
                ON ep.id = s.for_part
            ORDER BY ep.with_problem"
    );

    $stmtProb = $db->prepare(
        "SELECT p.id, t.case_order as order, t.input, t.output, t.weight, ep.point, p.const
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
        "INSERT INTO Breakdowns (for_submission, subject, expected, result, maxscore, autoscore)
            VALUES (:submission, :subject, :expected, :result, :maxscore, :autoscore)
            ON CONFLICT ON CONSTRAINT breakdowns_for_submission_subject_key
            DO UPDATE 
            SET expected = EXCLUDED.expected, result = EXCLUDED.result, maxscore = EXCLUDED.maxscore, autoscore = EXCLUDED.autoscore"
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

        // dictionarilize, { problem_id: [total_weight, testcases...] }
        $problems = [];
        foreach($rawprob as $problem) {
            if (!isset($problems[$problem["id"]])) {
                $problems[$problem["id"]] = [0];
            }

            array_push($problems[$problem["id"]], $problem);
            $problems[$problem["id"]][0] += $problem["weight"];
        }

        // distribute total weights to testcases, { problem_id: [contract_weight, testcases...] }
        foreach($problems as $key => $testcases) {
            // +2: 1 for naming, 1 for constraint
            $sum = array_shift($problems[$key]) + 2;

            // adjust contract weights
            $contract_weight = 0;
            if (count($problems[$key]) > 0) {
                $contract_weight = $problems[$key][0]["point"] * 1.0 / $sum;
            }

            // adjust test case weights
            foreach($problems[$key] as $index => $testcase) {
                $problems[$key][$index]["point"] *= 1.0 * $testcase["weight"] / $sum;
            }

            // append to head
            array_unshift($problems[$key], $contract_weight);
        }

        // perform grading
        $results = [];
        foreach ($submissions as $id => $submission) {
            $pid = $submission["problem"];
            if (!isset($problems[$pid])) continue;

            // get testcases
            $testcases = $problems[$pid];
            // get contract weight
            $contract_weight = array_shift($testcases);

            // static checkers
            if (count($testcases) > 0) {
                // check and fix name
                $code = $submission["answer"];
                $casename = "";
                $funcname = "";
                $match = fix_function_name($code, $testcases[0]["input"], $casename, $funcname);
                $submission["answer"] = $code;

                array_push($results, [
                    $submission["id"], // for_submission
                    "Function Name", // subject
                    $casename, // expected
                    $funcname, // result
                    $contract_weight, // maxscore
                    $match ? $contract_weight : 0 // autoscore
                ]);

                // check constraint
                $code = $submission["answer"];
                $constraint = $testcases[0]["const"];
                $match = check_function_constraint($code, $constraint, $casename);

                array_push($results, [
                    $submission["id"], // for_submission
                    "Constraint", // subject
                    describe_constraint($constraint), // expected
                    $match ? "Fulfilled" : "Failed", // result
                    $contract_weight, // maxscore
                    $match ? $contract_weight : 0 // autoscore
                ]);
            }

            // judge runners
            $i = 1;
            foreach($testcases as $testcase) {
                $code = $submission["answer"];
                $result = run_judgement($code, $testcase["input"], $testcase["output"]);

                array_push($results, [
                    $submission["id"], // for_submission
                    $testcase["input"], // subject
                    $testcase["output"], // expected
                    $result[0]->isSuccess() ? $testcase["output"] : $result[1], // result
                    $testcase["point"], // maxscore
                    $result[0]->isSuccess() ? $testcase["point"] : 0 // autoscore
                ]);

                $i++;
            }
        }

        foreach($submissions as $submission) {
            updateExamStatus($submission["exam"], $submission["student"], 2);
        }

        foreach($results as $result) {
            $stmtApply->execute([
                ":submission" => $result[0], 
                ":subject" => $result[1],
                ":expected" => $result[2],
                ":result" => $result[3],
                ":maxscore" => intval($result[4]),
                ":autoscore" => intval($result[5]),
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
                ep.point as possible,
                s.answer, s.created as answer_time,
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
                ep.point as possible,
                s.answer, s.created as answer_time,
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
                ep.point as possible,
                s.answer, s.created as answer_time,
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
    $stmtBrk = $db->prepare(
        "SELECT *, COALESCE(manualscore, autoscore, maxscore) as finalscore FROM Breakdowns
            WHERE for_submission = :submission ORDER BY id"
    );

    try {
        $stmtSub->execute($argSub);
        $results = $stmtSub->fetchAll(PDO::FETCH_ASSOC);

        foreach($results as $key => $result) {
            $stmtBrk->execute([":submission" => $result["id"]]);
            $results[$key]["breakdowns"] = $stmtBrk->fetchAll(PDO::FETCH_ASSOC);
        }

        $message = 'GRD_SUCCESS';

    } catch (Exception $e) {
        $message = 'GRD_INTERERR';
    }

    return new Status($message);
}
