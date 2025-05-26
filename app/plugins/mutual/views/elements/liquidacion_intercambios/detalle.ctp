<?php 
$intercambio_id = (isset($intercambio_id) ? $intercambio_id : 0);
$archivo = $this->requestAction('/mutual/liquidaciones/get_intercambio/' . $intercambio_id);
?>

<div class="areaDatoForm">
	<h4>#<?php echo $intercambio_id?> - <?php echo $archivo['LiquidacionIntercambio']['banco_intercambio']?> [<?php echo $archivo['LiquidacionIntercambio']['archivo_nombre']?>]</h4>
	REGISTROS LEIDOS: <strong><?php echo $archivo['LiquidacionIntercambio']['total_registros'] ?></strong>
	<br/>
	REGISTROS COBRADOS: <strong><?php echo $archivo['LiquidacionIntercambio']['registros_cobrados'] ?></strong>
	&nbsp;|&nbsp;IMPORTE COBRADO: <strong><?php echo $util->nf($archivo['LiquidacionIntercambio']['importe_cobrado']) ?></strong>
	<br/>
	FRAGMENTADO: <?php echo $controles->onOff($archivo['LiquidacionIntercambio']['fragmentado'])?>
	&nbsp;|&nbsp;PROCESADO: <?php echo $controles->onOff($archivo['LiquidacionIntercambio']['procesado'])?>
	
	<?php if($archivo['LiquidacionIntercambio']['recibo_id'] > 0):?>
		<br/>
		RECIBO: <strong><?php echo $html->link($archivo['LiquidacionIntercambio']['recibo_link'],'/Clientes/recibos/imprimir_recibo_pdf/'.$archivo['LiquidacionIntercambio']['recibo_id'],array('target' => 'blank'))?></strong>
	<?php endif;?>
	<?php if($archivo['LiquidacionIntercambio']['proveedor_id'] != 0):?>
		<br/>
		APLICADO A: <strong style="color: red;"><?php echo $archivo['LiquidacionIntercambio']['proveedor_razon_social_resumida']?></strong>
	<?php endif;?>
</div>