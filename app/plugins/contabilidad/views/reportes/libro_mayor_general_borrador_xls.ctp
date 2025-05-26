<?php
$contenido = "Content-Disposition: attachment; filename=Mayor-General.xls";
header('Content-type: application/vnd.ms-excel');
header($contenido);
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");
?>

<h1>LIBRO MAYOR GENERAL</h1>
<h1>FECHA DESDE :: <?php echo $util->armaFecha($fecha_desde)?></h1>
<h1>FECHA HASTA :: <?php echo $util->armaFecha($fecha_hasta)?></h1>

<table align="center" width="100%">

	<col width="100" />
	<col width="300" />
	<col width="100" />
	<col width="100" />
	<col width="800" />
	<col width="100" />
	<col width="100" />

	<tr border="0">
		<th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">CUENTA</th>
		<th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">DESCRIPCION</th>
		<th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">FECHA</th>
		<th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">NRO.AS.</th>
		<th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">REFERENCIA</th>
		<th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">DEBE</th>
		<th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">HABER</th>
		<th style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">MODULO</th>
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
				<td style="border-right: 1px solid black;font-size: small;"><?php echo $mayor['MutualAsientoRenglon']['cuenta']?></td>
				<td style="border-right: 1px solid black;font-size: small;"><?php echo $mayor['MutualAsientoRenglon']['descripcion']?></td>
				<td align="center" style="border-right: 1px solid black;font-size: small;"><?php echo date('d/m/Y',strtotime($mayor['MutualAsientoRenglon']['fecha']))?></td>
				<td style="border-right: 1px solid black;font-size: small;"><?php echo $mayor['MutualAsientoRenglon']['mutual_asiento_id']?></td>
				<td style="border-right: 1px solid black"><?php echo $mayor['MutualAsientoRenglon']['referencia']?></td>
				<td align="right" style="border-right: 1px solid black;font-size: small;"><?php echo ($mayor['MutualAsientoRenglon']['debe'] >  0 ? number_format($mayor['MutualAsientoRenglon']['debe'],2)  : '')?></td>
				<td align="right" style="border-right: 1px solid black;font-size: small;"><?php echo ($mayor['MutualAsientoRenglon']['haber'] > 0 ? number_format($mayor['MutualAsientoRenglon']['haber'],2) : '')?></td>
				<td style="border-right: 1px solid black"><?php echo $mayor['MutualAsientoRenglon']['modulo']?></td>
			</tr>	
	<?php endforeach; ?>
	<tr>
		<td colspan="5" align="right" style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;">T O T A L </td>
		<td align="right" style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;"><?php echo number_format($debe,2)?></td>
		<td align="right" style="border-top: 1px solid black;border-bottom: 1px solid black;border-right: 1px solid black;font-size: small;"><?php echo number_format($haber,2)?></td>
	</tr>
</table>