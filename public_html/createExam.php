<?php require_once(__DIR__ . '/lib.php'); ?>

<?php use_template('header.php', true, true); ?>

<title>Create your exam</title>

<?php use_template('resource.php', true, true); ?>
<?php use_template('nav.php', true, true); ?>

<?php


if (user_login_check()) {
    user_reload();
}

if (!user_admin_check()) {
    die(header("Location: home.php"));
}


?>

<table>
    <tr>
        <th> Checkbox </th>
        <th> Description </th>
    </tr>
    <form>
        <tr>
            <td><input type="checkbox"> </td>
            <td> Test2 </td>
        </tr>
        <tr>
            <td> <input type="checkbox"> </td>
            <td> Test4 </td>
        </tr>
    </form>
</table>