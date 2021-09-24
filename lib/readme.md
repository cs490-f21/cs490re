Put all backend libraries, or to say anything we don't want users to access with their URL bar here.

**How to use libraries**
`<?php require_once(__DIR__ . '/lib.php'); ?>`

If there is no `lib.php` in the folder, then you probably should **not** create PHP scripts in that folder. 

**How to create a new library**
1. Create and write your code. Import `pconfig.php` and *single library file* when needed.

2. Register your new library in the `libloader.php`.
