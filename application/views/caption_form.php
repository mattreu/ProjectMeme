<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Project Meme</title>
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/styles.css'); ?>">
</head>
<body>
    <div id="container">
        <h1>Add caption</h1>
        <div id="content">
            <?=validation_errors(); ?>

            <?php if ($this->session->flashdata('error')): ?>
                <p><?= $this->session->flashdata('error'); ?></p>
            <?php endif; ?>
            <div id="form_with_preview">
                <?php 
                    $attributes = array('data-base-url' => base_url('index.php/content/caption/'), 'id' => 'caption_form');
                    $hidden = array('location' => $location);
                ?>
                <?=form_open(base_url('index.php/content/add_caption/'.$id), $attributes, $hidden) ?>
                    <h5>Caption</h5>
                    <input type="text" name="text" value="<?=set_value('text'); ?>" size="50" />
                    <h5>Black area height</h5>
                    <input type="range" min=30 max=70 value="<?=set_value('black_area_height', '50'); ?>" name="black_area_height" />
                    <h5>Text position X</h5>
                    <input type="range" min=0 max=100 value="<?=set_value('text_x', '20'); ?>" name="text_x" />
                    <h5>Text position Y</h5>
                    <input type="range" min=0 max=100 value="<?=set_value('text_y', '50'); ?>" name="text_y" />

                    <div><input type="submit" value="Submit" /></div>
                </form>
                <div id="preview">
                    <img alt="Preview" />
                </div>
            </div>
        </div>
    </div>
    <script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>
</html>
