<?php
function run_autograder(int $exam): Status {
    $db = getDB();
    $stmtSub = $db->prepare(
        "SELECT s.id, s.answer, s.point, ep.point as possible, ep.with_problem as problem
            FROM Submissions s
            JOIN ExamParts ep
                ON ep.id = s.for_part
            WHERE s.point = NULL AND ep.for_exam = :exam
            ORDER BY ep.with_problem"
    );

    $stmtProb = $db->prepare(
        "SELECT p.id, t.case_order as order, t.input, t.output, t.weight
            FROM ExamParts ep
            JOIN Problem p
                ON ep.with_problem = p.id
            JOIN Testcases t
                ON p.id = t.for_problem
            WHERE ep.for_exam = :exam
            ORDER BY p.id"
    );

    $stmtApply = $db->prepare(
        "UPDATE Submissions SET point = :grade WHERE id = :id"
    );

    $message = 'GRD_UNKNOWN';

    try {
        $stmtSub->execute([":exam" => $exam]);
        $submissions = $stmtSub->fetch(PDO::FETCH_ASSOC);
        $stmtProb->execute([":exam" => $exam]);
        $rawprob = $stmtProb->fetch(PDO::FETCH_ASSOC);

        if (!$rawprob) {
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

        foreach($problems as $testcases) {
            $sum = $testcases[0];
            unset($testcases[0]);
            foreach($testcases as $testcase) {
                $testcase["point"] *= $sum * 1.0 * $testcase["weight"] / $sum;
            }
        }

        if ($submissions) {
            foreach ($submissions as $submission) {
                $pid = $submission["problem"];
                $testcases = $problems[$pid];
                foreach($testcases as $testcase) {
                    $result = run_judgement($submission["answer"], $testcase["input"], $testcase["output"]);
                    if ($result[0]->isSuccess()) {
                        $submission["point"] += $testcase["point"];
                    }
                    else {
                        // add comment about error
                    }
                }
            }
        } else {
            $message = 'GRD_NOPENDING';
            goto Fail;
        }

        foreach($submissions as $submission) {
            $stmtApply->execute([":grade" => $submission["point"], ":id" => $submission["id"]]);
        }
    
        Fail:;
    } catch (Exception $e) {
        $message = 'GRD_INTERERR';
    }

    return new Status($message);
}
