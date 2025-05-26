	<tr>
		<th>MES</th>
		<th>PROVEEDOR</th>
		<th>CORTE(d)</th>
		<th>iAC(+m)</th>
		<th>iDC(+m)</th>
		<th>VTO.(d)</th>
		<th>V.SOCIO(+m)</th>
		<th>V.PROVEEDOR(+d)</th>
		<th></th>
	</tr>
<?php 
	$i = 0;
	foreach($vtos as $vto):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
?>
<tr<?php echo $class;?>>
	<td><?php echo $util->mesToStr($vto['ProveedorVencimiento']['mes'],true)?></td>
	<td><strong><?php echo $vto['Proveedor']['razon_social']?></strong></td>
	<td align="center"><?php echo $vto['ProveedorVencimiento']['d_corte']?></td>
	<td align="center"><?php echo $vto['ProveedorVencimiento']['m_ini_socio_ac_suma']?></td>
	<td align="center"><?php echo $vto['ProveedorVencimiento']['m_ini_socio_dc_suma']?></td>
	<td align="center"><?php echo $vto['ProveedorVencimiento']['d_vto_socio']?></td>
	<td align="center"><?php echo $vto['ProveedorVencimiento']['m_vto_socio_suma']?></td>
	<td align="center"><?php echo $vto['ProveedorVencimiento']['d_vto_proveedor_suma']?></td>
	<td class="actions"><?php echo $controles->getAcciones($vto['ProveedorVencimiento']['id'],false) ?></td>
</tr>
<?php endforeach;?>
<?php //   debug($vtos)?>