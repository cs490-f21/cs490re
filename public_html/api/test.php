<?php require_once(__DIR__ . '/lib.php'); ?>

<?php
$ret = run_judgement("def add(a, b):\n    return ??! + b", "add(5, 6)", "11");
$ret = [];
