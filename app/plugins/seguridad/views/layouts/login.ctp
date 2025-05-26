<?
header ("Cache-Control: no-cache, must-revalidate");
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>
		<?php echo Configure::read('APLICACION.nombre')?>
	</title>
	<?php
//		echo $html->charset();
		echo $html->meta('icon');
		echo $html->css("login");
		echo $javascript->link('prototype-1.7.0');
	?>
</head>
<body>
<div id ='login'>
	<?php echo $content_for_layout ?>
</div>		
</body>
</html>