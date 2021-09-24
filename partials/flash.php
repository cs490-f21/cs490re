<!-- Flash dispalyer -->

<?php require_once (__DIR__ . '/lib.php'); ?>

<div class="container" id="flash">
    <?php $messages = getAllFlashes(); ?>
    <?php if ($messages) : ?>
        <?php foreach ($messages as $msg) : ?>
            <div class="alert alert-<?php write(get($msg, 'level', FLASH_INFO)); ?> alert-server alert-dismissible fade show" role="alert">
                <?php write(get($msg, "text", "Message not available.")); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
    moveMeUp(document.getElementById("flash"));
</script>
