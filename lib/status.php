<?php

/**
 * Represents a status code object.
 */
class Status {
    public string $code;

    public function __construct(string $code) {
        $this->code = $code;
    }

    /**
     * Get a user-friendly description of the status code.
     */
    public function getMessage() : array {
        global $status_messages;

        $msg = get($status_messages, $this->code, [('Unknown status code: ' . $this->code), FLASH_WARN]);

        return $msg;
    }

    /**
     * Check if this status code is equal to the given one.
     */
    public function is(string $code) : bool {
        return $this->code === $code;
    }

    /**
     * Check if this status code is indicating a successful status.
     */
    public function isSuccess() : bool {
        return $this->getMessage()[1] === FLASH_SUCC;
    }
}

/**
 * Status code message registry.
 * 
 * Whenever you use a new status code somewhere, please add it to here with proper message and serverity.
 */
$status_messages = [
    // User
    'USR_LINSUCC' =>    ['Welcome back', FLASH_SUCC],
    'USR_LOUTSUCC' =>   ['You have been logged out', FLASH_SUCC],
    'USR_REGSUCC' =>    ['Register success', FLASH_SUCC],
    'USR_UPDSUCC' =>    ['Profile updated', FLASH_SUCC],
    'USR_RSTSUCC' =>    ['Password changed', FLASH_SUCC],
    'USR_VISSUCC' =>    ['Visibility changed', FLASH_SUCC],
    'USR_RLDSUCC' =>    ['---PLACEHOLDER---', FLASH_SUCC],
    'USR_PROFSELF' =>   ['---PLACEHOLDER---', FLASH_SUCC],
    'USR_PROFPUBL' =>   ['---PLACEHOLDER---', FLASH_SUCC],
    
    'USR_UNAUTHED' =>   ['You need to login first', FLASH_WARN],
    'USR_NOTALLOW' =>   ['You already have an account', FLASH_WARN],
    'USR_UNKNOWN' =>    ['Unknown internal status', FLASH_WARN],
    'USR_PROFPRIV' =>   ['You cannot view private user profile', FLASH_WARN],

    'USR_PWDFAIL' =>    ['Password mismatch', FLASH_ERRO],
    'USR_NOTFOUND' =>   ['User does not exist', FLASH_ERRO],
    'USR_ROLEFAIL' =>   ['Could not fetch roles for current user', FLASH_ERRO],
    'USR_INTERERR' =>   ['Internal server error', FLASH_ERRO],
    'USR_DUPLICATE' =>  ['Provided information is collided with others', FLASH_ERRO],
    'USR_DUPEMAIL' =>   ['The email is using by others', FLASH_ERRO],
    'USR_DUPUSRNM' =>   ['The username is taken by others', FLASH_ERRO],

    // Role
    'ROL_SUCCESS' =>    ['---PLACEHOLDER---', FLASH_SUCC],
    'ROL_NOTFOUND' =>   ['Current user has a non-exist role', FLASH_WARN],
    'ROL_UNKNOWN' =>    ['Unknown internal status', FLASH_WARN],
    'ROL_INTERERR' =>   ['Internal server error', FLASH_ERRO],

    //Database
    'INS_SUCCESS' =>    ['Question insertion success', FLASH_SUCC],
    'INS_FAIL' =>       ['Question failed to insert into database', FLASH_ERRO],

    // Judging
    'JDG_PASSED' =>     ['Testcase passed', FLASH_SUCC],
    'JDG_FAILED' =>     ['Testcase failed', FLASH_WARN],
    'JDG_UNKNOWN' =>    ['Unknown internal status', FLASH_WARN],
    'JDG_INTERERR' =>   ['Internal server error', FLASH_ERRO],

    // Grading
    'GRD_SUCCESS' =>    ['Autograde completed', FLASH_SUCC],
    'GRD_NOPENDING' =>  ['No item is pending to grade', FLASH_WARN],
    'GRD_UNKNOWN' =>    ['Unknown internal status', FLASH_WARN],
    'GRD_INTERERR' =>   ['Internal server error', FLASH_ERRO],
];
