<h1>PROBADOR DE VENCIMIENTOS</h1>
<?php echo $frm->create(null,array('action' => 'vtos'))?>
	<table>
		<tr>
			<td>PERIODO</td><td><?php echo $frm->input('ProveedorVencimiento.periodo_liquidado')?></td>
		</tr>	
		<tr>
			<td>ORGANISMO</td><td><?php echo $frm->input('ProveedorVencimiento.codigo_organismo')?></td>
		</tr>
		<tr>
			<td>FECHA</td><td><?php echo $frm->input('ProveedorVencimiento.fecha')?></td>
		</tr>
		<tr>
			<td>PROVEEDOR ID</td><td><?php echo $frm->input('ProveedorVencimiento.proveedor_id')?></td>
		</tr>
		<tr>
			<td>BENEFICIO ID</td><td><?php echo $frm->input('ProveedorVencimiento.persona_beneficio_id')?></td>
		</tr>							
	</table>
<?php echo $frm->end("PROBAR")?>
<?php debug($vtos)?>
<?php debug($vtos1)?>