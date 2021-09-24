<?php
define('FLASH_INFO', 'info');
define('FLASH_SUCC', 'success');
define('FLASH_ERRO', 'danger');
define('FLASH_WARN', 'warning');

/**
 * Add a custome flash with specific severity level. (Default: FLASH_INFO)
 * 
 * Available levels: FLASH_INFO, FLASH_SUCC, FLASH_ERRO, FLASH_WARN
 */
function addFlash(string $msg, string $level = FLASH_INFO) : void {
    start_session();

    $message = [
        'text' => $msg,
        'level' => $level
    ];

    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $message);
    } else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $message);
    }
}

/**
 * Get all queued flash messages. This operation will also clear the flash queue.
 */
function getAllFlashes() : array {
    start_session();

    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}

/**
 * Generate an optional flash message based on the given status code.
 * 
 * You can specify one or more severity levels to be ignored.
 */
function addStatus(Status $status, array|string $ignores = []) : void {
    $msg = $status->getMessage();
    $message = $msg[0];
    $level = $msg[1];
    $report = true;

    foreach ((array) $ignores as $ignore) {
        if ($level === $ignore) {
            $report = false;
            break;
        }
    }

    if ($report) {
        addFlash($message, $level);
    }
}
