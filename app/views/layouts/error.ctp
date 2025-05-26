<?php
/* SVN FILE: $Id$ */
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.view.templates.errors
 * @since         CakePHP(tm) v 0.10.0.1076
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>

<?
//header ("Cache-Control: no-cache, must-revalidate");
//ob_start();
@ob_start ('ob_gzhandler');
header('Content-type: text/html; charset: UTF-8');
header('Cache-Control: must-revalidate');
$offset = -1;
$ExpStr = "Expires: " . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT';
header($ExpStr);
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
		echo $html->css("aman2");
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
			$('btnShowMenu').observe('click',function(){
				$('btnShowMenu').hide();
				document.getElementById('appContainer').style.width="82%";
				$('MenuNavContainer').show();
			});
			$('btnHideMenu').observe('click',function(){
				$('MenuNavContainer').hide();
				document.getElementById('appContainer').style.width="96%";
				$('btnShowMenu').show();
			});			
		});
	</script>
	
</head>
<body>
	<div id="container">
		<div id="navTop"><?php echo $this->renderElement('maquetado/navTop2')?></div>
		<div id="sHeader"><?php echo $this->renderElement('maquetado/sHeader')?></div>
		<div id="MenuNavContainer" style="width: 15%;float: left;display: none;font-size: 75%;color:#003366;font-weight: bold;">
			<div id="btnHideMenu" style="float: left;cursor: pointer;">
			<?php echo $html->image('controles/application_go.png',array('border'=>0,'style' => 'padding:2px;'))?>
			Men&uacute; Principal
			</div>
			<div style="clear: both;"></div>
			<?php echo $this->renderElement('maquetado/mainMenu')?>
		</div>
		<div id="btnShowMenu" style="width: 20px;float: left;cursor: pointer;"><?php echo $html->image('controles/application_go.png',array('border'=>0,'style' => 'padding:2px;'))?></div>
		<div id="appContainer" style="width: 96%;">
		<div><?php echo $this->renderElement('maquetado/mensajes') ?></div>

		<div style="background-color:red; color:white;padding:10px;font-size:12px;font-family:verdana;">
			<h3 style="color:white;"><?php echo $name; ?></h3>
				<strong><?php __('Error'); ?>: </strong>
				<?php echo $message?>
		</div>
		<div id="mensaje_error_js" class="notices_error"></div>
		
		
		</div>
		<div style="clear: both;"></div>
		<div id='footer'><?php echo $this->renderElement('maquetado/foot')?><?php echo $this->renderElement('maquetado/install')?></div>	

	</div>

</body>
</html>

