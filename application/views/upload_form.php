<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Project Meme</title>
	<link rel="stylesheet" type="text/css" href="<?=base_url('assets/css/styles.css'); ?>">
</head>
<body>
    <div id="container">
        <h1>Upload</h1>
        <div id="content">
            <?php echo $error;?>
            <?php echo form_open_multipart('content/add');?>
                <input type="file" name="image" size="20" />
                <br />
                <input type="submit" value="Upload" />
            </form>
        </div>
	</div>
</body>
</html>
