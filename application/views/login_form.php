<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Project Meme</title>
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/styles.css'); ?>">
</head>
<body>
    <div id="container">
        <h1>Login</h1>
        <div id="content">
            <?=validation_errors(); ?>

            <?php if ($this->session->flashdata('error')): ?>
                <p><?= $this->session->flashdata('error'); ?></p>
            <?php endif; ?>

            <?=form_open(base_url('index.php/auth/login')) ?>

                <h5>Email Address</h5>
                <input type="text" name="email" value="<?=set_value('email'); ?>" size="50" />

                <h5>Password</h5>
                <input type="password" name="password" value="" size="50" />

                <div><input type="submit" value="Submit" /></div>
            </form>
            <p><a href="<?=base_url('index.php/auth/register')?>">Create a new account</a></p>
        </div>
    </div>
</body>
</html>
