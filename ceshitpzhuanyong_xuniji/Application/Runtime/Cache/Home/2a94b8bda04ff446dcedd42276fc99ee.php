<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>index</title>
</head>
<body>
	<?php echo test();?>
	<form action="" method="post" enctype="multipart/form-data"> 
		<input type="file" name='pic'>
		<input type="text" name='yzm' />
		<img src="<?php echo U('yzm');?>" onclick="this.src='<?php echo U('yzm');?>'" >
		<button>提交</button>
	</form>
	<hr>
</body>
</html>