<?php 
$soloActivos = (isset($soloActivos) ? $soloActivos : 1);
$beneficios = $this->requestAction('/pfyj/persona_beneficios/beneficios_by_persona/'.$persona_id.'/'.$soloActivos);

?>
<?php if(count($beneficios) != 0):?>
<div class="areaDatoForm">
<h4>BENEFICIOS ACTIVOS</h4>

<table>

	<tr>
	
		<th>#</th>
		<th>ORGANISMO</th>
		<th>TIPO</th>
		<th>LEY</th>
		<th>NRO BENEFICIO</th>
		<th>SUB-BENEFICIO</th>
		<th>EMPRESA</th>
		<th>REPARTICION</th>
		<th>NRO CBU</th>
		<th>BANCO</th>
		<th>SUCURSAL</th>
		<th>CUENTA</th>
	</tr>

<?php
$i = 0;
foreach ($beneficios as $beneficio):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
<tr<?php echo $class;?>>
	<td align="center"><?php echo $beneficio['PersonaBeneficio']['id']?></td>
	<td><?php echo $this->requestAction('/config/global_datos/valor/'.$beneficio['PersonaBeneficio']['codigo_beneficio'])?></td>
	<td align="center"><?php echo $beneficio['PersonaBeneficio']['tipo']?></td>
	<td align="center"><?php echo $beneficio['PersonaBeneficio']['nro_ley']?></td>
	<td align="center"><?php echo $beneficio['PersonaBeneficio']['nro_beneficio']?></td>
	<td align="center"><?php echo $beneficio['PersonaBeneficio']['sub_beneficio']?></td>
	<td><?php echo (!empty($beneficio['PersonaBeneficio']['codigo_empresa']) && $beneficio['PersonaBeneficio']['codigo_beneficio'] == 'MUTUCORG2201' ? $this->requestAction('/config/global_datos/valor/'.$beneficio['PersonaBeneficio']['codigo_empresa']) : '') ?></td>
	<td><?php echo $beneficio['PersonaBeneficio']['codigo_reparticion']?></td>
	<td><?php echo $beneficio['PersonaBeneficio']['cbu']?></td>
	<td><?php echo (!empty($beneficio['PersonaBeneficio']['banco_id']) ? $this->requestAction('/config/bancos/nombre/'.$beneficio['PersonaBeneficio']['banco_id']) : '')?></td>
	<td><?php echo $beneficio['PersonaBeneficio']['nro_sucursal']?></td>
	<td><?php echo $beneficio['PersonaBeneficio']['nro_cta_bco']?></td>


</tr>

<?php endforeach;?>

</table>
</div>
<?php endif;?>
