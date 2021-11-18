<?php // author: Jiyuan Zhang
require_once(__DIR__ . '/lib.php');
?>

<?php use_template('header.php', true, true); ?>

<title>Login</title>
<style type="text/css">
body {
  background-image: url('static/eberhardt_HDR.jpg');
  background-repeat: no-repeat;
  background-size: cover;
}
</style>

<?php use_template('resource.php', true, true); ?>
<?php use_template('nav.php', true, true); ?>

<?php
if (user_login_check()) {
    die(header("Location: home.php"));
}

$disp_name = "";

if (isset($_POST["submit"])) {
    $name = get($_POST, "name", null);
    $password = get($_POST, "password", null);

    $disp_name = $name;

    $isValid = true;
    if (!validate_userlogin($name)) {
        addFlash("Invalid user identifier provided", FLASH_WARN);
        $isValid = false;
    }

    if (!validate_password($password, false)) {
        addFlash("Invalid password provided", FLASH_WARN);
        $isValid = false;
    }

    if ($isValid) {
        $status = user_login($name, $password);
        addStatus($status);

        if ($status->is('USR_LINSUCC')) {
            die(header("Location: home.php"));
        }
    }
}
?>
<div>
    
    <form class="center-form" method="POST" onsubmit="return validate(this);" style="background-color: #fff;">
        <h1>Login</h1>
        <hr>
        <div class="mb-3">
            <label for="name">Identity: </label>
            <input type="name" class="form-control" id="name" name="name" value="<?php write($disp_name); ?>" placeholder="Email or Username" required />
        </div>
        <div class="mb-3">
            <label for="password">Password: </label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required />
        </div>
        <div>
            <input type="submit" class="btn btn-primary" name="submit" value="Login" />
        </div>
    </form>
</div>
<script>
    function validate(form) {
        let name = form.name.value;
        let password = form.password.value;
        let isValid = true;
        if (!validate_userlogin(name)) {
            addFlash("Invalid user identifier provided", FLASH_WARN);
            isValid = false;
        }

        if (!validate_password(password, false)) {
            addFlash("Invalid password provided", FLASH_WARN);
            isValid = false;
        }

        return isValid;
    }
</script>

<?php use_template('flash.php', true, true); ?>
<?php use_template('footer.php', true, true); ?>
