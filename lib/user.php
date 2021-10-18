<?php // author: Jiyuan Zhang
// checks
/**
 * Check if a user is logged in.
 */
function user_login_check() : bool {
    start_session();

    return isset($_SESSION["user"]);
}

// actions
/**
 * Logout the current user.
 */
function user_logout() : Status {
    start_session();

    $_SESSION["user"] = null;
    // end_session();

    return new Status('USR_LOUTSUCC');
}

/**
 * Perform user login.
 */
function list_users(array &$users) : Status {
    start_session();
    
    $db = getDB();
    $stmt = $db->prepare(
        "SELECT id, email, COALESCE(username, email) AS username
            FROM Users
            ORDER BY username ASC"
    );

    $message = 'USR_UNKNOWN';
    $users = [];

    try {
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $message = 'USR_SUCCESS';
    } catch (Exception $e) {
        $message = 'USR_INTERERR';
    }

    return new Status($message);
}

/**
 * Perform user login.
 */
function user_login(string $user, string $password) : Status {
    start_session();
    
    $db = getDB();
    $stmt = $db->prepare(
        "SELECT id, email, COALESCE(username, email) AS username, password, extra
            FROM Users
            WHERE email = :user OR username = :user
            LIMIT 1"
    );

    $message = 'USR_UNKNOWN';

    try {
        $stmt->execute([":user" => $user]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $upass = $user["password"];
            if (password_verify($password, $upass)) {
                unset($user["password"]);
                $_SESSION['user'] = $user;

                $message = 'USR_LINSUCC';
            } else {
                $message = 'USR_PWDFAIL';
            }
        } else {
            $message = 'USR_NOTFOUND';
        }
    } catch (Exception $e) {
        $message = 'USR_INTERERR';
    }

    if ('USR_LINSUCC' === $message) {
        $stmt = $db->prepare(
            "SELECT id, role_id, is_active
                FROM UserRoles
                WHERE user_id = :user"
        );
    
        try {
            $stmt->execute([":user" => user_get_id()]);
            $role = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($role) {
                $_SESSION['roles'] = $role;
            } else {
                $_SESSION['roles'] = [];
            }
        } catch (Exception $e) {
            $message = 'USR_ROLEFAIL';
        }
    }

    return new Status($message);
}

/**
 * Perform user registration.
 */
function user_register(string $username, string $email, string $password) : Status {
    //do our registration
    $db = getDB();
    $stmt = $db->prepare(
        "INSERT INTO Users (email, username, password)
            VALUES (:email, :username, :password)"
    );

    $message = 'USR_UNKNOWN';

    $hash = password_hash($password, PASSWORD_BCRYPT);
    try {
        $stmt->execute([":email" => $email, ":password" => $hash, ":username"=>$username]);
        $message = 'USR_REGSUCC';
    }
    catch (PDOException $e) {
        $code = get($e->errorInfo, 0, "");
        if ($code === "23000") {
            preg_match("/Users.(\w+)/", $e->errorInfo[2], $matches);
            if (isset($matches[1])) {
                if ('email' === $matches[1]) {
                    $message = 'USR_DUPEMAIL';
                }
                elseif ('username' === $matches[1]) {
                    $message = 'USR_DUPUSRNM';
                }
            } else {
                $message = 'USR_DUPLICATE';
            }
        } else {
            $message = 'USR_INTERERR';
        }
    }

    return new Status($message);
}

/**
 * Perform user password reset.
 * 
 * The user must login first.
 */
function user_password_reset(string $current_pwd, string $new_pwd) : Status {
    $db = getDB();
    $stmt = $db->prepare("SELECT password FROM Users WHERE id = :id");

    $message = 'USR_UNKNOWN';

    try {
        $stmt->execute([":id" => user_get_id()]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (isset($result["password"])) {
            if (password_verify($current_pwd, $result["password"])) {
                $query = "UPDATE Users SET password = :password WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->execute([
                    ":id" => user_get_id(),
                    ":password" => password_hash($new_pwd, PASSWORD_BCRYPT)
                ]);

                $message = 'USR_RSTSUCC';
            } else {
                $message = 'USR_PWDFAIL';
            }
        }
    } catch (Exception $e) {
        $message = 'USR_INTERERR';
    }

    return new Status($message);
}

/**
 * Perform user info change.
 * 
 * The user must login first.
 */
function user_detail_update(string $email, string $username) : Status {
    $db = getDB();
    $stmt = $db->prepare("UPDATE Users SET email = :email, username = :username WHERE id = :id");

    $message = 'USR_UNKNOWN';

    try {
        $stmt->execute([":email" => $email, ":username" => $username, ":id" => user_get_id()]);
        
        $message = 'USR_UPDSUCC';
    } catch (Exception $e) {
        $code = get($e->errorInfo, 0, "");
        if ($code === "23000") {
            preg_match("/Users.(\w+)/", $e->errorInfo[2], $matches);
            if (isset($matches[1])) {
                if ('email' === $matches[1]) {
                    $message = 'USR_DUPEMAIL';
                }
                elseif ('username' === $matches[1]) {
                    $message = 'USR_DUPUSRNM';
                }
            } else {
                $message = 'USR_DUPLICATE';
            }
        } else {
            $message = 'USR_INTERERR';
        }
    }

    return new Status($message);
}

/**
 * Reload user info from database.
 */
function user_reload() : Status {
    $db = getDB();
    $stmt = $db->prepare(
        "SELECT id, email, COALESCE(username, email) AS username, extra
            FROM Users
            WHERE id = :user 
            LIMIT 1");

    $message = 'USR_UNKNOWN';

    try {
        $stmt->execute([":user" => user_get_id()]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            unset($user["password"]);
            $_SESSION['user'] = $user;

            $message = 'USR_RLDSUCC';
        } else {
            $message = 'USR_NOTFOUND';
        }
    } catch (Exception $e) {
        $message = 'USR_INTERERR';
    }

    if ('USR_RLDSUCC' === $message) {
        $stmt = $db->prepare(
            "SELECT id, role_id, is_active
                FROM UserRoles
                WHERE user_id = :user"
        );
    
        try {
            $stmt->execute([":user" => user_get_id()]);
            $role = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($role) {
                $_SESSION['roles'] = $role;
            } else {
                $_SESSION['roles'] = [];
            }
        } catch (Exception $e) {
            $message = 'USR_ROLEFAIL';
        }
    }

    return new Status($message);
}

/**
 * Check if current user can view another user's profile.
 * 
 * If the profile is visible, `$info` will contain the user info.
 */
function user_visibility_barrier(int $uid, array &$info) : Status {
    if ((user_get_id() === $uid)) {
        if ($uid === -1) {
            return new Status('USR_UNAUTHED');
        }
        else {
            return new Status('USR_PROFSELF'); // one can always view their profile
        }
    }

    $db = getDB();
    $stmt = $db->prepare(
        "SELECT *
            FROM Users
            WHERE id = :user 
            LIMIT 1");

    $message = 'USR_UNKNOWN';
    $info = [];

    try {
        $stmt->execute([":user" => $uid]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            unset($user["password"]);
            if (intval(get($user, 'visibility', '0')) === 1) {
                $message = 'USR_PROFPUBL';
                $info = $user;
            } else {
                $message = 'USR_PROFPRIV';
            }
        } else {
            $message = 'USR_NOTFOUND';
        }
    } catch (Exception $e) {
        $message = 'USR_INTERERR';
    }

    return new Status($message);
}

/**
 * Change current user's profile visibility.
 */
function user_visibility_setting(int $new) : Status {
    $uid = user_get_id();

    $db = getDB();
    $stmt = $db->prepare(
        "UPDATE Users
            SET visibility = :new
            WHERE id = :user 
            LIMIT 1");

    $message = 'USR_UNKNOWN';

    try {
        $stmt->execute([
            ":user" => $uid,
            ":new" => $new,
        ]);
        
        set($_SESSION["user"], "visibility", $new);
        $message = 'USR_VISSUCC';
    } catch (Exception $e) {
        $message = 'USR_INTERERR';
    }

    return new Status($message);
}

// getters and setters
/**
 * Safely get current user name.
 */
function user_get_username() : string {
    start_session();

    if (user_login_check()) {
        return get($_SESSION["user"], "username", user_get_email());
    }
    
    return "";
}

/**
 * Safely get current user email.
 */
function user_get_email() : string {
    start_session();

    if (user_login_check()) {
        return get($_SESSION["user"], "email", "");
    }

    return "";
}

/**
 * Safely get current user id.
 */
function user_get_id() : int {
    start_session();

    if (user_login_check()) {
        return intval(get($_SESSION["user"], "id", -1));
    }

    return -1;
}

/**
 * Safely get current user roles.
 */
function user_get_roles(bool $activeOnly = true) : array {
    start_session();

    if (user_login_check()) {
        $roles = [];
        $roledata = get($_SESSION, "roles", []);
        foreach ($roledata as $data) {
            $active = intval(get($data, 'is_active', 0));
            $role_id = intval(get($data, 'role_id', -1));
            $grant_id = intval(get($data, 'id', -1));
            if ((! $activeOnly) || (1 === $active)) {
                array_push($roles, ['active' => $active, 'role' => $role_id, 'id' => $grant_id]);
            }
        }

        return $roles;
    }

    return [];
}

/**
 * Safely get current user extra info string.
 */
function user_get_extra() : string {
    start_session();

    if (user_login_check()) {
        $extra = get($_SESSION["user"], "extra", "");

        return $extra;
    }

    return 0;
}

/**
 * Safely get current user's profile visibility.
 */
function user_get_visibility() : int {
    start_session();

    if (user_login_check()) {
        $point = get($_SESSION["user"], "visibility", 0);

        return $point;
    }

    return 0;
}

/**
 * Safely check if current user has a role.
 */
function user_has_roles(int $roleid, bool $requireActive) : bool {
    start_session();

    $roles = user_get_roles($requireActive);
    foreach ($roles as $role) {
        if ($roleid === $role['role']) {
            return true;
        }
    }

    return false;
}

/**
 * Safely check if current user is an admin.
 */
function user_admin_check() : bool {
    if (!user_login_check()) return false;

    $adminRole = -1;
    $status = role_get_id("admin", $adminRole);
    if (!$status->is('ROL_SUCCESS')) {
        $adminRole = -1;
    }

    return user_has_roles($adminRole, true);
}
