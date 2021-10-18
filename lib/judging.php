<?php // author: Jiyuan Zhang

// [statusCode, messageOutput]
function run_judgement(string $code, string $input, string $output): array {
    $buffer = [];
    $status = 'JDG_UNKNOWN';

    try {
        $retcode = 0;
        $temp = tmpfile();
        $path = stream_get_meta_data($temp)['uri'];
        fwrite($temp, build_judgement($code, $input, $output));

        exec("python \"" . $path . "\" 2>&1", $buffer, $retcode);

        fclose($temp);
        if (file_exists($path)) {
            unlink($path);
        }

        $status = 'JDG_FAILED';
        for ($i = 0; $i < count($buffer); $i++) {
            if ($buffer[$i] == "[judgerunner] passed 02c7e4f2-f04e-49a8-a99f-3c02c8aa4e64") {
                $status = 'JDG_PASSED';
                $buffer[$i] = "[Online Judge] Testcase Passed";
            }
        }

    } catch (Exception $e) {
        $status = 'JDG_INTERERR';
    }

    $log = implode("\n", $buffer);
    $log = str_replace($path, "submission.py", $log);
    return [new Status($status), $log];
}

function build_judgement(string $code, string $input, string $output): string {
    $tmpl_path = use_template("judgerunner.py", false, false);
    $tmpl = file_get_contents($tmpl_path);
    $tmpl = str_replace("{{user_function}}", $code, $tmpl);
    $tmpl = str_replace("{{user_input}}", $input, $tmpl);
    $tmpl = str_replace("{{user_output}}", $output, $tmpl);
    return $tmpl;
}
