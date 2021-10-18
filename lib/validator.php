<?php // author: Jiyuan Zhang

// This file should always synchronize with /public_html/js/validator.js

// validate
/**
 * Check if the given string is a valid email address.
 */
function validate_email(string $email) : bool {
    return
        isset($email) &&
        (strlen($email) === strlen(trim($email))) &&
        filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}

/**
 * Check if the given string is a valid username.
 */
function validate_username(string $username) : bool {
    return 
        isset($username) && 
        (strlen($username) === strlen(trim($username))) &&
        (strlen($username) >= 3) &&
        (preg_match('/^\w+$/', $username, $matches) === 1);
}

/**
 * Check if the given string is a valid user indicator (email, username or id).
 */
function validate_userlogin(string $user) : bool {
    return 
        isset($user) && (
            validate_email($user) ||
            validate_username($user)
        );
}

/**
 * Check if the given string is a valid password or strong password.
 */
function validate_password(string $password, bool $strengthCheck) : bool {
    $basic = isset($password) && 
                (strlen($password) === strlen(trim($password))) &&
                (strlen($password) >= 6) &&
                (preg_match('/\s/', $password) === 0);
    
    if (!$basic) {
        return false;
    }

    if (!$strengthCheck) {
        return true;
    }

    $upper = preg_match('/[A-Z]/', $password) === 1;
    $lower = preg_match('/[a-z]/', $password) === 1;
    $number = preg_match('/[0-9]/', $password) === 1;
    $symbol = preg_match('/[^\w\s]/', $password) === 1;

    return $upper && $lower && $number && $symbol;
}

/**
 * Check if the given string is a valid numberical string between [min, max].
 */
function validate_number(string $score, int $min, int $max) : bool {
    return 
        isset($score) && 
        (strlen($score) === strlen(trim($score))) &&
        ctype_digit($score) && 
        (intval($score) >= $min) &&
        (intval($score) <= $max);
}

/**
 * Check if the given number is a valid integer between [min, max].
 */
function validate_between(int $score, int $min, int $max) : bool {
    return 
        isset($score) && 
        (intval($score) >= $min) &&
        (intval($score) <= $max);
}

/**
 * Check if the given string is a valid non-empty non-trimable string with length between [1, max].
 */
function validate_string(string $username, int $max) : bool {
    return 
        isset($username) && 
        (strlen($username) === strlen(trim($username))) &&
        (strlen($username) >= 1) &&
        (strlen($username) <= $max);
}
