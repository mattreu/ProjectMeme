<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Project Meme</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/styles.css'); ?>">
</head>
<body>
    <div id="container">
        <h1>Register</h1>
        <div id="content">
            <?php echo validation_errors(); ?>

            <?php if ($this->session->flashdata('error')): ?>
                <p><?= $this->session->flashdata('error'); ?></p>
            <?php endif; ?>

            <?php echo form_open(base_url('index.php/auth/register')) ?>

                <h5>Username</h5>
                <input type="text" name="username" value="<?php echo set_value('username'); ?>" size="50" />

                <h5>Email Address</h5>
                <input type="text" name="email" value="<?php echo set_value('email'); ?>" size="50" />

                <h5>Password</h5>
                <input type="password" name="password" value="" size="50" />

                <h5>Password Confirm</h5>
                <input type="password" name="passconf" value="" size="50" />

                <div><input type="submit" value="Submit" /></div>
            </form>
            <p>I already have an account. <a href="<?=base_url('index.php/auth/login')?>">Login</a></p>
        </div>
    </div>
</body>
</html>