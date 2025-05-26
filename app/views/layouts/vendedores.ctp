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
		&nbsp;@v<?php echo Configure::read('APLICACION.version')?>
	</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<?php
		echo $html->charset();
		echo $html->meta('icon');
//		echo $html->css($css);
//		echo $javascript->link('jquery-1.3.2.min');
//		echo $html->css('modalbox');
// 		echo $javascript->link('prototype');
//		echo $javascript->link('prototype-1.7.0');
//		echo $javascript->link('scriptaculous.js?load=effects');
//		echo $javascript->link('controls');
//		echo $javascript->link('modalbox');
//		echo $javascript->link('aplicacion/funciones');
//		echo $javascript->link('aplicacion/validadores');
//		echo $javascript->link('calendar/calendar_stripped');
//		echo $javascript->link('calendar/calendar-setup_stripped');
//		echo $javascript->link('calendar/lang/calendar-es');
//		echo $html->css('calendar/skins/aqua/theme');
		

	?>
	<!-- <meta http-equiv="Content-type" content="text/html;charset=UTF-8" /> -->  
	
<!--	<script type="text/javascript">
		Event.observe(window, 'load', function() {
			$('mensaje_error_js').hide();
		});
	</script>-->
	<meta name="author" content="MARIO ADRIAN TORRES 20218387005">
        
                  
            
        <link rel="stylesheet" href="<?php echo $this->base ?>/js/bootstrap-4.3.1/themes/flaty/bootstrap.min.css" crossorigin="anonymous">
        <script src="<?php echo $this->base ?>/js/fontawesome-5.11.2/js/fontawesome.min.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="<?php echo $this->base ?>/js/fontawesome-5.11.2/css/all.css">

        <script src="<?php echo $this->base ?>/js/jquery-3.4.1.min.js"></script>
        <script src="<?php echo $this->base ?>/js/jquery-ui-1.12.1/jquery-ui.min.js"></script>

        <link href="<?php echo $this->base ?>/js/datepicker/bootstrap-datepicker.standalone.min.css" rel="stylesheet" type="text/css"/>
        <script src="<?php echo $this->base ?>/js/datepicker/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="<?php echo $this->base ?>/js/datepicker/bootstrap-datepicker.es.min.js" type="text/javascript"></script>

        <script src="<?php echo $this->base ?>/js/bootstrap-4.3.1/js/bootstrap.min.js"></script>  
        
</head>
<body>
    <div class="container-fluid" style="padding: 2px;">
            <?php echo $this->renderElement('solicitudes/nuevo_menu',array('plugin' => 'ventas'))?>
            
        <div class="container-fluid min-vh-100 pt-2">
            <?php echo $this->renderElement('solicitudes/mensajes',array('plugin' => 'ventas'))?>
            <?php echo $content_for_layout ?>
        </div>
        <div class="container-fluid" style="margin-top: 5px;">
            <div class="row" style="padding: 5px;color: #f5f5f5;background-color: #595959;border: 0;">
                <div class="col-sm-12"><small><?php echo $this->renderElement('maquetado/foot')?></small></div>
            </div>
        </div>	 
    </div>
</body>
</html>
