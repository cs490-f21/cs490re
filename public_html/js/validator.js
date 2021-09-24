"use strict";

// Refer to /lib/validator.php for documents
// This file should always synchronize with /lib/validator.php

function validate_email(email) {
    var re = /^[^\s@]+@[^\s@]+$/;
    return (typeof email === "string") &&
        (email.length === email.trim().length) &&
        re.test(email);
}

function validate_username(username) {
    var re = /^\w+/;
    return (typeof username === "string") && 
        (username.length === username.trim().length) &&
        (username.length >= 3) &&
        re.test(username);
}

function validate_userlogin(user) {
    return (typeof user === "string") && (
        validate_email(user) ||
        validate_username(user)
    );
}

function validate_password(password, strengthCheck) {
    var re1 = /\s/;
    var basic = (typeof password === "string") && 
                (password.length === password.trim().length) &&
                (password.length >= 6) &&
                (!re1.test(password));
    
    if (!basic) {
        return false;
    }

    if (!strengthCheck) {
        return true;
    }

    var re2 = /[A-Z]/;
    var re3 = /[a-z]/;
    var re4 = /[0-9]/;
    var re5 = /[^\w\s]/;
    upper = re2.test(password);
    lower = re3.test(password);
    number = re4.test(password);
    symbol = re5.test(password);

    return upper && lower && number && symbol;
}

function validate_number(val, min, max) {
    return (typeof val === "string") && 
        (val.length === val.trim().length) &&
        Number.isInteger(parseFloat(val)) && 
        (parseFloat(val) >= min) &&
        (parseFloat(val) <= max);
}

function validate_between(val, min, max) {
    return (typeof val === "number") && 
        (val >= min) &&
        (val <= max);
}

function intval(val) {
    return parseFloat(val);
}

function validate_string(val, length) {
    return (typeof val === "string") && 
        (val.length === val.trim().length) &&
        (val.length >= 0) &&
        (val.length <= length);
}
