<?php 
//header ("Cache-Control: no-cache, must-revalidate");
//ob_start();
//ob_start ('ob_gzhandler');
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
		echo $html->charset();
		echo $html->meta('icon');
		echo $html->css($css);
//		echo $javascript->link('jquery-1.3.2.min');
		echo $html->css('modalbox');
// 		echo $javascript->link('prototype');
		echo $javascript->link('prototype-1.7.0');
		echo $javascript->link('scriptaculous.js?load=effects');
		echo $javascript->link('controls');
		echo $javascript->link('modalbox');
		echo $javascript->link('aplicacion/funciones');
		echo $javascript->link('aplicacion/validadores');
		echo $javascript->link('calendar/calendar_stripped');
		echo $javascript->link('calendar/calendar-setup_stripped');
		echo $javascript->link('calendar/lang/calendar-es');
		echo $html->css('calendar/skins/aqua/theme');
		

	?>
	<!-- <meta http-equiv="Content-type" content="text/html;charset=UTF-8" /> -->  
	
	<script type="text/javascript">
		Event.observe(window, 'load', function() {
			$('mensaje_error_js').hide();
		});
	</script>
	<meta name="author" content="MARIO ADRIAN TORRES 20218387005">
</head>
<body>
    
	<div id="container">
		<div id="navTop"><?php echo $this->renderElement('maquetado/navTop2')?></div>
<!--		<div id="header"><?//=$this->renderElement('maquetado/header')?></div>-->
		<div id="sHeader"><?php echo $this->renderElement('maquetado/sHeader')?></div>
		<?php if(isset($Seguridad)):?>
		<div id="menuHorizontalContainer">
		<?php echo $this->renderElement('maquetado/mainMenuHorizontal')?>
		</div>
		<?php endif;?>         
		<div id="appContainer">
       
		<div><?php echo$this->renderElement('maquetado/mensajes') ?></div>
		<?php echo $content_for_layout ?>
		<div id="mensaje_error_js" class="notices_error"></div>
		
		
		</div>
		<div style="clear: both;"></div>
		<div id='footer'><?php echo $this->renderElement('maquetado/foot')?><?php // echo $this->renderElement('maquetado/install')?></div>	

	</div>

</body>
</html>