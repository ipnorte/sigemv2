<h3>BENEFICIOS ASIGNADOS</h3>
<table>

	<tr>
	
		<th>ORGANISMO</th>
		<th>NRO BENEFICIO</th>
		<th>LEY</th>
		<th>NRO CBU</th>
		<th>EMPRESA</th>
		<th>ACTIVO</th>
		<th>PRINCIPAL</th>
	
	</tr>

<?php
$i = 0;
foreach ($beneficios as $b):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
<tr<?php echo $class;?>>
	
	<td><?php echo $b['Beneficio']['beneficio_concepto']?></td>
	<td><?php echo $b['Beneficio']['nro_beneficio']?></td>
	<td><?php echo $b['Beneficio']['nro_ley']?></td>
	<td><?php echo $b['Beneficio']['cbu']?></td>
	<td><?php echo $b['Beneficio']['empresa']?></td>
	<td align="center"><?php echo $controles->OnOff($b['Beneficio']['activo'])?></td>
	<td align="center"><?php echo $controles->OnOff($b['Beneficio']['principal'])?></td>

</tr>

<?php endforeach;?>

</table>


<?php //   debug($beneficios)?>