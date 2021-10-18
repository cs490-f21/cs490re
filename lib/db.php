<?php // author: Jiyuan Zhang

/**
 * Get PDO database instance
 */
function getDB() : PDO {
    global $db;

    if (!isset($db)) {
        try {
            require_once (__DIR__ . "/db.config.php");
            $connection_string = $db_url;
            $db = new PDO($connection_string, $db_user, $db_pass);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
        catch(Exception $e) {
            var_export($e);
            $db = null;
        }
    }

    return $db;
}
