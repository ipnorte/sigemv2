<h1>DETALLE DE MAYOR DE CUENTA</h1>
<table align="center" width="100%">

<!--	<col width="250" />-->
<!--	<col width="100" />-->
<!--	<col width="100" />-->
		
	<tr border="0">
		<th style="font-size: small;">FECHA</th>
		<th style="border-left: 1px solid white;font-size: small;"> NRO.AS.</th>
		<th style="border-left: 1px solid white;font-size: small;">REFERENCIA</th>
		<th style="border-left: 1px solid white;font-size: small;">DEBE</th>
		<th style="border-left: 1px solid white;font-size: small;">HABER</th>
		<th style="border-left: 1px solid white;font-size: small;">SALDO</th>
	</tr>

	<?php
	$debe = 0;
	$haber = 0;
	foreach ($cuentaMayor as $mayor):
		$debe += $mayor['MutualAsientoRenglon']['debe'];
		$haber += $mayor['MutualAsientoRenglon']['haber'];
		$saldo = $debe - $haber;
	?>
		
			<tr>
				<td align="center" style="font-size: small;"><?php echo date('d/m/Y',strtotime($mayor['MutualAsientoRenglon']['fecha']))?></td>
				<td style="border-left: 1px solid black;font-size: small;"><?php echo $mayor['MutualAsientoRenglon']['mutual_asiento_id']?></td>
				<td style="border-left: 1px solid black;font-size: small;"><?php echo $mayor['MutualAsientoRenglon']['referencia']?></td>
				<td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo ($mayor['MutualAsientoRenglon']['debe'] >  0 ? number_format($mayor['MutualAsientoRenglon']['debe'],2)  : '')?></td>
				<td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo ($mayor['MutualAsientoRenglon']['haber'] > 0 ? number_format($mayor['MutualAsientoRenglon']['haber'],2) : '')?></td>
				<td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo number_format($saldo,2)?></td>
			</tr>	
	<?php endforeach; ?>
	<tr>
		<td colspan="3" align="right" style="border-top: 1px solid black;font-size: small;">T O T A L</td>
		<td align="right" style="border-top: 1px solid black;border-left: 1px solid black;font-size: small;"><?php echo number_format($debe,2)?></td>
		<td align="right" style="border-top: 1px solid black;border-left: 1px solid black;font-size: small;"><?php echo number_format($haber,2)?></td>
		<td align="right" style="border-top: 1px solid black;border-left: 1px solid black;font-size: small;"><?php echo number_format($saldo,2)?></td>
	</tr>
</table>
