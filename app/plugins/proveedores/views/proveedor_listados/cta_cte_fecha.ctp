<?php echo $this->renderElement('proveedor_listados/menu_listado',array('plugin' => 'proveedores'))?>
<?php echo $this->renderElement('proveedor/datos_proveedor',array('proveedor_id'=>$proveedor['Proveedor']['id'],'plugin' => 'proveedores'))?>
<h3>CUENTA CORRIENTE :: FECHA: <?php echo date('d/m/Y', strtotime($fecha_desde)) . ' AL ' . date('d/m/Y', strtotime($fecha_hasta))?></h3>

<div>
<table class="areaDatoForm">

	<tr border="0">
		<th>FECHA</th>
		<th>CONCEPTO</th>
		<th>REFERENCIA</th>
		<th>DEBE</th>
		<th>HABER</th>
		<th>SALDO</th>
	</tr>
	<tr>
		<td></td>
		<td>SALDO AL <?php echo date('d/m/Y', strtotime($fecha_saldo_anterior))?></td>		
		<td></td>
		<td></td>
		<td align="right"><?php echo number_format($saldo_anterior,2, ',','.')?></td>
		<td></td>
		<td></td>
		<td></td>	
	</tr>
	<?php
	$i = 0;
	foreach ($ctaCte as $renglon):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		$saldo_anterior += $renglon['debe'] - $renglon['haber'];
	?>
		<tr<?php echo $class;?> >
			<td align="center"><?php echo date('d/m/Y',strtotime($renglon['fecha']))?></td>
			<td><strong><?php echo $renglon['concepto']?></strong></td>
			<td><?php echo $renglon['comentario']?></td>
			<td align="right"><?php echo ($renglon['debe'] == 0  ? '' : number_format($renglon['debe'],2, ',','.'))?></td>
			<td align="right"><?php echo ($renglon['haber'] == 0 ? '' : number_format($renglon['haber'],2, ',','.'))?></td>
			<td align="right"><?php echo number_format($saldo_anterior,2, ',','.')?></td>
		</tr>
	<?php endforeach; ?>	
</table>
</div>