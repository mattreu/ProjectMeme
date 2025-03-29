<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Project Meme</title>
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/styles.css'); ?>">
</head>
<body>
    <div id="container">
        <h1>Memes</h1>
        <div id="content">
			<div class="conf"><a href="<?=base_url('index.php/content/add'); ?>">Add new</a></div>
			<?php if(empty($images)): ?>
				<p>No content yet...</p>
			<?php else: ?>
				<?php foreach($images as $image): ?>
					<div class="image_container">
						<img src="<?=base_url('assets/images/'.$image['location']);?>">
						<p>by <?=$image['username']?> on <?=$image['created_at']?></p>
					</div>
				<?php endforeach ?>
			<?php endif ?>
		</div>
	</div>
</body>
</html>
