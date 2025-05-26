<?
//header ("Cache-Control: no-cache, must-revalidate");
//ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>
		<?php echo Configure::read('APLICACION.nombre')?>
	</title>
	<?php
		//echo $html->charset();
		echo $html->meta('icon');
		echo $html->css(Configure::read('APLICACION.css'));
		echo $javascript->link('prototype');
		echo $javascript->link('scriptaculous');
		echo $javascript->link('aplicacion/funciones');

	?>
	<!-- <meta http-equiv="Content-type" content="text/html;charset=UTF-8" /> -->  
</head>
<body>
	<div id="container">
		<div id="head"><?php echo $this->renderElement('/maquetado/head')?></div>
		<div id="main_menu"><?php echo $this->renderElement('/maquetado/main_menu')?></div>
		<div id="app_container">
		
			<table id="diseno_container">
				<tr>
					<td class="r1c1"></td>
					<td class="r1c2"><?php echo $this->renderElement('/maquetado/datos_user')?></td>
					<td class="r1c3"></td>
				</tr>
				<tr>
					<td class="r2c1"><?php echo $html->image('maquetado/AppContainer_r2_c1.gif') ?></td>
					<td class="r2c2"><?php echo $content_for_layout ?></td>
					<td class="r2c3"><?php echo $html->image('maquetado/AppContainer_r2_c3.gif') ?></td>
				</tr>
				
				<tr>
					<td class="r2c1"><?//=$html->image('maquetado/AppContainer_r2_c1.gif') ?></td>
					<td class="r2c2"><?php echo $this->renderElement('/maquetado/mensajes') ?></td>
					<td class="r2c3"><?//=$html->image('maquetado/AppContainer_r2_c3.gif') ?></td>
				</tr>				
				
				<tr>
					<td class="r3c1"></td>
					<td class="r3c2"></td>
					<td class="r3c3"></td>
				</tr>							
			</table>
		

		</div>
		<div style="clear: both;"></div>
		<div id='foot'><?php echo $this->renderElement('/maquetado/foot')?></div>	
	
	</div>

</body>
</html>