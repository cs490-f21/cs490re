<?php require_once(__DIR__ . '/lib.php'); ?>

<?php
header("Content-Type: text/html; charset=UTF-8");

function fail() : void {
    header('HTTP/1.1 400 Bad Request');
    write('Unknown type argument');
}

function router(string $method, string $type, string $pgno, string $element, string $value, array $body) : void {
    if (!validate_number($pgno, 1, 214748363)) {
        fail();
        return;
    }

    $page = intval($pgno);

    switch ($type) {
    case "some_paginator":
        // some_paginator($page, $value, $element);
        break;
    default:
        fail();
        break;
    }
}

$requestMethod = $_SERVER["REQUEST_METHOD"];
$requestType = get($_GET, "type", "");
$requestPage = get($_GET, "page", "");
$requestElem = get($_GET, "elem", "");
$requestValue = get($_GET, "value", "");
$requestBody = [];
if ($requestMethod == 'POST') {
    $requestBody = (array) json_decode(file_get_contents('php://input'), TRUE);
}

router($requestMethod, $requestType, $requestPage, $requestElem, $requestValue, $requestBody);
