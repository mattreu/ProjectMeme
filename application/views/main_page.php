<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Project Meme</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/styles.css'); ?>">
</head>
<body>

<div id="container">
	<h1>Welcome to Project Meme!</h1>

	<div id="content">
		<?php if ($is_logged): ?>
            <a href="<?= base_url('index.php/content') ?>">Browse memes</a>
        <?php else: ?>
            <p>To access this site contents you need to <a href="<?php echo base_url('index.php/auth/login')?>">login</a> to your account or <a href="<?php echo base_url('index.php/auth/register')?>">create</a> one.</p>
        <?php endif; ?>
	</div>

	<footer>Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></footer>
</div>

</body>
</html>
