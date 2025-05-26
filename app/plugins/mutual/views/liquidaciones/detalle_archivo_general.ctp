<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: IMPORTAR DATOS'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>

<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>

<h3>ANALISIS DE ARCHIVOS RECIBIDOS VINCULADOS A ESTA LIQUIDACION</h3>

<?php 

$title = "";
if(!empty($archivo)):
	$title = "ARCHIVO: " . $archivo['LiquidacionIntercambio']['banco_intercambio'] . " | " . $archivo['LiquidacionIntercambio']['archivo_nombre'];
else:
	$title = "ARCHIVO: *** TODOS ***";
endif;

echo $this->renderElement('show',array(
										'plugin' => 'shells',
										'process' => 'analisis_general_archivo',
										'accion' => '.mutual.liquidaciones.detalle_archivo_general.'.$liquidacion['Liquidacion']['id'],
										'target' => '',
										'btn_label' => 'RESUMEN IMPORTACION',
										'titulo' => 'ANALIZA LOS ARCHIVOS DE RENDICION',
										'subtitulo' => $title,
										'p1' => $liquidacion['Liquidacion']['id'],
										'p2' => (isset($archivo['LiquidacionIntercambio']['id']) ? $archivo['LiquidacionIntercambio']['id'] : 0),
));

?>

