<?php
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=balance_sumas_saldos.xls");
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");

App::import('Model', 'contabilidad.PlanCuenta');

$oPlanCuenta = new PlanCuenta();


?>

<h1>BALANCE DE SUMAS Y SALDOS</h1>
<h1>FECHA DESDE :: <?php echo $util->armaFecha($fecha_desde)?></h1>
<h1>FECHA HASTA :: <?php echo $util->armaFecha($aMutualProcesoAsiento['MutualProcesoAsiento']['fecha_hasta'])?></h1>

<table border="1">

		
	<tr>
		<th>CUENTA</th>
		<th>DESCRIPCION</th>
		<th colspan = "2">SUMAS</th>
		<th colspan = "2">SALDOS</th>
	</tr>

	<tr>
		<th></th>
		<th></th>
		<th>DEBE</th>
		<th>HABER</th>
		<th>DEBE</th>
		<th>HABER</th>
	</tr>
	<?php
	$debe = 0;
	$haber = 0;

	$total_saldo_debe = 0;
	$total_saldo_haber = 0;
	
	foreach ($libroSumasSaldos as $mayor):
		$debe += $mayor[0]['debe'];
		$haber += $mayor[0]['haber'];
		
		$saldo_debe = 0;
		$saldo_haber = 0;
		
		if($mayor[0]['debe'] > $mayor[0]['haber']) $saldo_debe = $mayor[0]['debe'] - $mayor[0]['haber'];
		else $saldo_haber = $mayor[0]['haber'] - $mayor[0]['debe']; 
		
		$total_saldo_debe  += $saldo_debe;
		$total_saldo_haber += $saldo_haber;
		
	?>
		<tr>
			<td><?php echo $oPlanCuenta->formato_cuenta($mayor['PlanCuenta']['cuenta'], $ejercicio)?></td>
			<td><?php echo $mayor['PlanCuenta']['descripcion']?></td>
			<td align="right"><?php echo $mayor[0]['debe']?></td>
			<td align="right"><?php echo $mayor[0]['haber']?></td>
			<td align="right"><?php echo ($saldo_debe  > 0 ? $saldo_debe  : '')?></td>
			<td align="right"><?php echo ($saldo_haber > 0 ? $saldo_haber : '')?></td>
		</tr>
	<?php endforeach; ?>
		<tr>
			<td align="right" colspan="2" >TOTAL GENERAL:</td>
			<td align="right" ><?php echo $debe?></td>
			<td align="right" ><?php echo $haber?></td>
			<td align="right" ><?php echo $total_saldo_debe?></td>
			<td align="right" ><?php echo $total_saldo_haber?></td>
		</tr>
</table>

