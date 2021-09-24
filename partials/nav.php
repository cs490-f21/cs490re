<!-- Flash dispalyer -->

<?php require_once (__DIR__ . '/lib.php'); ?>

<nav>
    <ul class="nav-rol">
        <li class="nav-title">CS490</li>
        <li class="nav-sep">|</li>

        <li class="nav-item"><a href="home.php">Home</a></li>

        <?php if (user_login_check()) : ?>
            <li class="nav-item"><a href="profile.php">Profile</a></li>
        <?php endif; ?>

        <?php if (!user_login_check()) : ?>
            <li class="nav-item"><a href="login.php">Login</a></li>
            <li class="nav-item"><a href="register.php">Register</a></li>
        <?php endif; ?>

        <?php if (user_login_check()) : ?>
            <div class="dropdown">
                <button class="nav-item dropbtn">Folded menu</i></button>
                <div class="dropdown-content">
                    <a href="home.php">Item 1</a>
                    <a href="home.php">Item 2</a>
                    <a href="home.php">Item 3</a>
                </div>
            </div> 
        <?php endif; ?>
        
        <?php if (user_login_check()) : ?>
            <li class="nav-item"><a href="logout.php">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>
