<div class="areaDatoForm2">
	LIQUIDACION: <strong>#<?php echo $archivo['LiquidacionIntercambio']['liquidacion_id']?></strong> - <strong><?php echo $util->periodo($archivo['LiquidacionIntercambio']['periodo'],true)?> </strong> - <strong><?php echo $util->globalDato($archivo['LiquidacionIntercambio']['codigo_organismo'])?></strong>
	<br/>
	BANCO INTERCAMBIO: <strong><?php echo $util->banco($archivo['LiquidacionIntercambio']['banco_id'])?></strong>
	<br/>
	ARCHIVO: <a href="<?php echo $this->base?>/<?php echo $archivo['LiquidacionIntercambio']['archivo_file']?>" target="_blank"><strong><?php echo $archivo['LiquidacionIntercambio']['archivo_nombre']?></strong></a>
	<br/>
	FECHA DE CARGA: <?php echo $archivo['LiquidacionIntercambio']['created']?>
</div>