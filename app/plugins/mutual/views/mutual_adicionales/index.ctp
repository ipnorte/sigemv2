<?php echo $this->renderElement('head',array('title' => 'CONFIGURACION DE CONCEPTOS ADICIONALES','plugin' => 'config'))?>
<div class="actions"><?php echo $controles->botonGenerico('/mutual/mutual_adicionales/add','controles/calculator_add.png','Nuevo Concepto')?></div>
<table>
	<tr>
		<th></th>
		<th>#</th>
		<th>CONCEPTO ADICIONAL</th>
		<th>ORGANISMO</th>
		<th>CALCULAR S/PROVEEDOR</th>
		<th>IMPUTAR A PROVEEDOR</th>
		<th>TIPO</th>
		<th>VALOR</th>
		<th>DEVENGA</th>
		<th>CALCULAR SOBRE</th>
		<th>PERIODO DESDE</th>
		<th>PERIODO HASTA</th>
		
		<th>ACTIVO</th>	
	</tr>
	<?php foreach($adicionales as $adicional):?>
		<tr class="activo_<?php echo $adicional['MutualAdicional']['activo']?>">
			<td nowrap="nowrap"><?php echo $controles->getAcciones($adicional['MutualAdicional']['id'],false,true,true) ?></td>
			
			<td><?php echo $adicional['MutualAdicional']['id']?></td>
			<td><strong><?php echo $util->globalDato($adicional['MutualAdicional']['tipo_cuota'])?></strong></td>
			<td><?php echo $adicional['MutualAdicional']['organismo']?></td>
			<td><?php echo $adicional['MutualAdicional']['proveedor_deuda_aplica']?></td>
			<td><?php echo $adicional['MutualAdicional']['proveedor_deuda_imputa']?></td>
			<td align="center"><?php echo $adicional['MutualAdicional']['tipo_desc']?></td>
			<td align="right"><?php echo $util->nf($adicional['MutualAdicional']['valor'])?></td>
			<td align="center"><?php echo $controles->onOff($adicional['MutualAdicional']['devengado_previo'])?></td>
			<td><?php echo $adicional['MutualAdicional']['deuda_calcula_desc']?></td>
			<td align="center"><?php echo $util->periodo($adicional['MutualAdicional']['periodo_desde'])?></td>
			<td align="center"><?php echo $util->periodo($adicional['MutualAdicional']['periodo_hasta'])?></td>
			
			<td align="center"><?php echo $controles->onOff($adicional['MutualAdicional']['activo'])?></td>
		</tr>
	<?php endforeach;?>
</table>

<?php //   debug($adicionales)?>