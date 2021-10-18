<?php // author: Jiyuan Zhang

// output
/**
 * Escape a string for HTML output.
 */
function escape(string $value) : string {
    $escaped = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
    return $escaped;
}

/**
 * Write a correctly escaped string for HTML output.
 * 
 * You can disable escaping by setting `$escape = false`.
 */
function write(string $value, bool $escape = true) : string {
    $out = $value;
    
    if ($escape) {
        $out = escape($out);
    }

    echo($out);

    return $out;
}

/**
 * Prettify a date time string.
 */
function pretty_date(string $date) : string {
    $createDate = new DateTime($date);

    $strip = $createDate->format('m-d h:i A');
    return $strip;
}

// accessor
/**
 * Safely get a member `$k` from the container `$v`.
 * 
 * If such member does not exist, `$default` will be returned instead.
 */
function get(mixed $v, mixed $k = null, mixed $default = "") : mixed {
    if (is_array($v) && isset($k) && isset($v[$k])) {
        $returnValue = $v[$k];
    } else if (is_object($v) && isset($k) && isset($v->$k)) {
        $returnValue = $v->$k;
    } else {
        $returnValue = $default;
    }

    return $returnValue;
}

/**
 * Safely set a member `$k` of the container `$v`.
 * 
 * If such member does not exist, no operation is performed.
 */
function set(mixed &$v, mixed $k = null, mixed $value = "") : void {
    if (is_array($v) && isset($k)) {
        $v[$k] = $value;
    } else if (is_object($v) && isset($k)) {
        $v->$k = $value;
    }
}

// session
$session_started = false;

/**
 * Start a client-server session. The session should be closed by `end_session`.
 */
function start_session() : void {
    global $session_started;

    if (!$session_started) {
        session_start();

        $session_started = true;
    }
}

/**
 * End a client-server session opened by `start_session`.
 */
function end_session() : void {
    global $session_started;

    session_unset();
    session_destroy();

    $session_started = true;
}
