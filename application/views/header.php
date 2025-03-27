<div class="header" style="display: grid; grid-template: 1fr / 1fr 1fr; padding:0 8px;">
    <h3><a href="<?= base_url('index.php/main') ?>">Project Meme</a></h3>
    <p style="align-self: end; justify-self: end;">
        <?php if ($is_logged): ?>
            Welcome <?= $username ?> <a href="<?= base_url('index.php/auth/logout') ?>">Logout</a>
        <?php else: ?>
            <a href="<?= base_url('index.php/auth/login') ?>">Login</a> / <a href="<?= base_url('index.php/auth/register') ?>">Register</a>
        <?php endif; ?>
    </p>
</div>