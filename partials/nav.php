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
                <button class="nav-item dropbtn">Task Menu</i></button>
                <div class="dropdown-content">
                <?php if (user_admin_check()) : ?>
                    <li><a href="createQuestion.php">Create Question</a></li>
                    <li><a href="createExam.php">Create Exam</a></li>
                <?php else : ?>
                    <li><a href="takeExam.php">Take Exam</a></li>
                    <li><a href="reviewExam.php">Review Exam</a></li>

                <?php endif; ?>
                </div>
            </div> 
        <?php endif; ?>

        
        <?php if (user_login_check()) : ?>
            <li class="nav-item"><a href="logout.php">Logout</a></li>
        <?php endif; ?>

    </ul>
</nav>
