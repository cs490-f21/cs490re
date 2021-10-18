<?php // author: Jiyuan Zhang
require_once(__DIR__ . '/lib.php');
?>

<?php use_template('header.php', true, true); ?>

<title>Register</title>

<?php use_template('resource.php', true, true); ?>
<?php use_template('nav.php', true, true); ?>

<?php
if (user_login_check()) {
    addStatus(new Status('USR_NOTALLOW'));
    die(header("Location: home.php"));
}

$disp_email = "";
$disp_username = "";

if (isset($_POST["submit"])) {
    $email = get($_POST, "email", null);
    $new_password = get($_POST, "password", null);
    $confirm_password = get($_POST, "confirm", null);
    $username = get($_POST, "username", null);

    $disp_email = $email;
    $disp_username = $username;

    $isValid = true;
    if (!validate_email($email)) {
        addFlash("Invalid email provided", FLASH_WARN);
        $isValid = false;
    }

    if (!validate_username($username)) {
        addFlash("Invalid username provided", FLASH_WARN);
        $isValid = false;
    }

    if ($new_password !== $confirm_password) {
        addFlash("Passwords don't match", FLASH_WARN);
        $isValid = false;
    }

    if (!validate_password($new_password, true)) {
        addFlash("Password is not strong enough", FLASH_WARN);
        $isValid = false;
    }

    if ($isValid) {
        $status = user_register($username, $email, $new_password);
        addStatus($status);

        if ($status->is('USR_REGSUCC')) {
            die(header("Location: login.php"));
        }
    }
}
?>

<div>
    <h1>Register</h1>
    <form class="center-form" method="POST" onsubmit="return validate(this);">
        <div class="mb-3">
            <label for="email">Email: </label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" value="<?php write($disp_email); ?>" required />
        </div>
        <div class="mb-3">
            <label for="username">Username: </label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?php write($disp_username); ?>" required />
            <small id="usernameHelp" class="form-text text-muted">Minimum 3 characters in length</small>
        </div>
        <div class="mb-3">
            <label for="pw">Password: </label>
            <input type="password" class="form-control" id="pw" name="password" placeholder="Super Secret Password" required />
            <small id="passwordHelp" class="form-text text-muted">Minimum 6 characters in length
                <br/>Contains Uppercase Letters, Lowercase Letters, Numbers and Symbols</small>
        </div>
        <div class="mb-3">
            <label for="cpw">Confirm Password: </label>
            <input type="password" class="form-control" id="cpw" name="confirm" placeholder="Super Secret Password, Again" required />
        </div>
        <div>
            <input type="submit" class="btn btn-primary" name="submit" value="Register" />
        </div>
    </form>
</div>
<script>
    function validate(form) {
        let email = form.email.value;
        let username = form.username.value;
        let new_password = form.password.value;
        let confirm_password = form.confirm.value;
        let isValid = true;
        if (!validate_email(email)) {
            addFlash("Invalid email provided", FLASH_WARN);
            isValid = false;
        }

        if (!validate_username(username)) {
            addFlash("Invalid username provided", FLASH_WARN);
            isValid = false;
        }

        if (new_password !== confirm_password) {
            addFlash("Passwords don't match", FLASH_WARN);
            isValid = false;
        }

        if (!validate_password(new_password, true)) {
            addFlash("Password is not strong enough", FLASH_WARN);
            isValid = false;
        }

        return isValid;
    }
</script>

<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>
