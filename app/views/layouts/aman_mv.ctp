<?
//header ("Cache-Control: no-cache, must-revalidate");
//ob_start();
?>
<?php echo $html->docType(); ?> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>
		<?php echo Configure::read('APLICACION.nombre')?>
		&nbsp;@v<?php echo Configure::read('APLICACION.version')?>
	</title>
	<?php
//		echo $html->charset();
		echo $html->meta('icon');
		echo $html->css('aman_mv');
//		echo $javascript->link('jquery-1.3.2.min');
		echo $html->css('modalbox');
		echo $javascript->link('prototype');
		echo $javascript->link('scriptaculous.js?load=effects');
		echo $javascript->link('controls');
		echo $javascript->link('modalbox');
		echo $javascript->link('aplicacion/funciones');
		echo $javascript->link('aplicacion/validadores');
		
		
	?>
	<!-- <meta http-equiv="Content-type" content="text/html;charset=UTF-8" /> -->  
	
	<script type="text/javascript">
		Event.observe(window, 'load', function() {$('mensaje_error_js').hide();});
	</script>
	
</head>
<body>
	<div id="container">
		<div id="navTop"><?php echo $this->renderElement('maquetado/navTop')?></div>
		<div id="header"><?php echo $this->renderElement('maquetado/header')?></div>
		<div id="sHeader"><?php echo $this->renderElement('maquetado/sHeader')?></div>
		<div id="menuNav"><?php echo $this->renderElement('maquetado/mainMenu_vertical')?></div>
		<div id="appContainer">
		<?php echo $content_for_layout ?>
		<div id="mensaje_error_js" class="notices_error"></div>
		<div><?php echo $this->renderElement('maquetado/mensajes') ?></div>
		
		</div>
		<div style="clear: both;"></div>
		<div id='footer'><?php echo $this->renderElement('maquetado/foot')?></div>	

	</div>

</body>
</html>