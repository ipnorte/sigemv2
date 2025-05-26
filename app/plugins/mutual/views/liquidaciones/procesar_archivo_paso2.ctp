<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: PROCESAR ARCHIVO DE DESCUENTO'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php  if(!empty($archivo)):?>

<h3>PASO 2: Cruce de Informaci&oacute;n con Datos del Envio</h3>

<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion_intercambio',array('archivo' => $archivo,'plugin'=>'mutual'))?>

<div class="row">
	<?php echo $controles->btnRew('Regresar','/mutual/liquidaciones/importar/'.$archivo['LiquidacionIntercambio']['liquidacion_id'].'/1')?>
</div>

<div class="notices"><strong>ATENCION!:</strong> Mientras se encuentra ejecut&aacute;ndose el proceso NO CERRAR ESTA VENTANA!</div>
<?php 
echo $this->renderElement('show',array(
										'plugin' => 'shells',
										'process' => 'procesa_archivo',
										'accion' => '.mutual.liquidaciones.resumen_cruce_informacion.'.$archivo['LiquidacionIntercambio']['liquidacion_id'],
										'target' => '',
										'btn_label' => 'Paso 3 - Ver Resultados',
										'titulo' => 'Cruce de Informacion con los Datos del Envio',
										'p1' => $archivo['LiquidacionIntercambio']['id'],
										'p2' => 2	
));

?>
<?php endif;?>