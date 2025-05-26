<div class="areaDatoForm2">
	<h3>#<?php echo $liquidacion['Liquidacion']['id']?> - <?php echo $util->periodo($liquidacion['Liquidacion']['periodo'],true) . ' | ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo'])?></h3>
	CREADA: <strong><?php echo $liquidacion['Liquidacion']['created']?></strong> - ULTIMA MODIFICACION: <strong><?php echo $liquidacion['Liquidacion']['modified']?></strong> |
	TIPO LIQUIDACION: <strong><span style="color: white; background-color: green; padding: 2px 5px 2px 5px;"><?php echo $util->tipoLiquidacion($liquidacion['Liquidacion']['tipo_liquida'])?></span></strong>
	<br/>
	ESTADO: <strong><?php echo ($liquidacion['Liquidacion']['cerrada'] == 1 ? '<span style="color:red;">CERRADA</span>' : '<span style="color:green;">ABIERTA</span>')?></strong>
	<strong><?php echo ($liquidacion['Liquidacion']['imputada'] == 1 ? ' | (IMPUTADA EN CTA.CTE.)' : ' | PENDIENTE DE IMPUTAR')?></strong>
	&nbsp;|&nbsp; TOTAL LIQUIDADO ($): <strong><?php echo $util->nf($liquidacion['Liquidacion']['total_periodo'] + $liquidacion['Liquidacion']['total_vencido'])?></strong>
</div>