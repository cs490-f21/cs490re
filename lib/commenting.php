<?php
function add_comment(int $submission, string $content, int $user = -1) {
    if ($user == 0) {
        if (!user_login_check())
            return new Status('USR_UNAUTHED');
        
        $user = user_get_id();
    }

    $db = getDB();
    $stmtSub = $db->prepare(
        "INSERT INTO Comments(for_submission, from_user, content) VALUES (:sub, :usr, :txt);"
    );

    $message = 'CMT_UNKNOWN';

    try {
        $stmtSub->execute([":sub" => $submission, ":usr" => $user, ":txt" => $content]);
        $sub = $stmtSub->fetch(PDO::FETCH_ASSOC);

        $message = 'CMT_SUCCESS';
    } catch (Exception $e) {
        $message = 'CMT_INTERERR';
    }

    return new Status($message);
}

function get_comments(int $submission, array &$comments) {
    $db = getDB();
    $stmtSub = $db->prepare(
        "SELECT c.*, u.username FROM Comments c
            JOIN Users u
                ON u.id = c.from_user
            WHERE for_submission = :sub 
            ORDER BY created ASC;"
    );

    $message = 'CMT_UNKNOWN';
    $comments = [];

    try {
        $stmtSub->execute([":sub" => $submission]);
        $comments = $stmtSub->fetchAll(PDO::FETCH_ASSOC);

        $message = 'CMT_SUCCESS';
    } catch (Exception $e) {
        $message = 'CMT_INTERERR';
    }

    return new Status($message);
}
