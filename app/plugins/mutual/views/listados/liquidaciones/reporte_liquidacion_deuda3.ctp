<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: '.$util->globalDato($liquidacion['Liquidacion']['codigo_organismo']).' :: RESUMEN PERIODO ' . $util->periodo($liquidacion['Liquidacion']['periodo'])))?>
<div class="areaDatoForm2">
	<h3><?php echo $util->periodo($liquidacion['Liquidacion']['periodo'],true) . ' | ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo'])?></h3>
	FECHA PROCESO: <strong><?php echo $liquidacion['Liquidacion']['created']?></strong>
	<br/>
	ESTADO: <strong><?php echo ($liquidacion['Liquidacion']['cerrada'] == 1 ? '<span style="color:red;">CERRADA</span>' : '<span style="color:green;">ABIERTA</span>')?></strong>
	&nbsp;|&nbsp; TOTAL LIQUIDADO ($): <strong><?php echo $util->nf($liquidacion['Liquidacion']['total_periodo'] + $liquidacion['Liquidacion']['total_vencido'])?></strong>
</div>
<div class="notices_error"><strong>ATENCION!:</strong> Mientras se encuentra ejecut&aacute;ndose el proceso <strong>NO CERRAR ESTA VENTANA!</strong></div>
<?php 
echo $this->renderElement('show',array(
										'plugin' => 'shells',
										'process' => 'reporte_control_liquidacion3',
										'accion' => '.mutual.listados.reporte_liquidacion_deuda3.'.$liquidacion['Liquidacion']['id'].'.1',
										'target' => '',
										'btn_label' => 'VER REPORTE',
										'titulo' => 'REPORTE CONTROL DE LIQUIDACION | ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) ." - ". $util->periodo($liquidacion['Liquidacion']['periodo']),
										'p1' => $liquidacion['Liquidacion']['id'],
));

?>	