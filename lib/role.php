<?php

$role_table = null;

/**
 * Speculatively Load the whole role table into memory.
 * 
 * You can force a reload by setting `$flush = true`.
 */
function role_init(bool $flush = false) : Status {
    global $role_table;

    if (isset($role_table) && (!$flush)) {
        return new Status('ROL_SUCCESS');
    }

    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Roles");

    $message = 'ROL_UNKNOWN';

    try {
        $stmt->execute();
        $role_table = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $message = 'ROL_SUCCESS';
    } catch (Exception $e) {
        $message = 'ROL_INTERERR';
    }

    return new Status($message);
}

/**
 * Get the description of a role based on the role id.
 */
function role_get_desc(int $roleid, array &$desc) : Status {
    global $role_table;

    $status = role_init();
    if (!$status->is('ROL_SUCCESS')) {
        return $status;
    }

    if (isset($role_table)) {
        foreach ($role_table as $role_row) {
            if (intval($role_row['id']) === $roleid) {
                $desc = ['name' => $role_row['name'], 'desc' => $role_row['description']];
                return new Status('ROL_SUCCESS'); 
            }
        }

        return new Status('ROL_NOTFOUND');
    }

    return new Status('ROL_INTERERR');
}

/**
 * Get the id of a role based on the role name.
 */
function role_get_id(string $rolename, int &$id) : Status {
    global $role_table;

    $status = role_init();
    if (!$status->is('ROL_SUCCESS')) {
        return $status;
    }

    if (isset($role_table)) {
        foreach ($role_table as $role_row) {
            if ($role_row['name'] === $rolename) {
                $id = intval($role_row['id']);
                return new Status('ROL_SUCCESS'); 
            }
        }

        return new Status('ROL_NOTFOUND');
    }

    return new Status('ROL_INTERERR');
}
