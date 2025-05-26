<?php 
//header ("Cache-Control: no-cache, must-revalidate");
//ob_start();
//@ob_start ('ob_gzhandler');
header('Content-type: text/html; charset: UTF-8');
header('Cache-Control: must-revalidate');
$offset = -1;
$ExpStr = "Expires: " . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT';
header($ExpStr);

$css = Configure::read('APLICACION.default_css');

?>
<?php echo $html->docType(); ?> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>
		<?php echo Configure::read('APLICACION.nombre')?>
	</title>
	<?php
//		echo $html->charset();
		echo $html->meta('icon');
		echo $html->css($css);
		echo $javascript->link('prototype-1.7.0');
	?>
    <meta name="author" content="MARIO ADRIAN TORRES 20218387005">
</head>
<body style="padding: 20px;">

	<div id="login-container">
		<div class="login_header"><?php echo strtoupper(Configure::read('APLICACION.nombre_fantasia'))?></div>
		<div class="login_sheader">SIGEM <?php echo Configure::read('APLICACION.version')?></div>
		<div class="login_main"><?php echo $content_for_layout ?></div>
		<div class="login_footer"><?php echo $this->renderElement('maquetado/foot')?><?php //   echo $this->renderElement('maquetado/install')?></div>
	</div>

</body>
</html>