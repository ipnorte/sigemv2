<?php
$contenido = "Content-Disposition: attachment; filename=Saldo-Fecha.xls";
header('Content-type: application/vnd.ms-excel');
header($contenido);
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");
?>

<h1>SALDOS A FECHA :: <?php echo date('d/m/Y', strtotime($desdeFecha)) . ' AL ' . date('d/m/Y', strtotime($hastaFecha))?></h1>
<hr>

<div class="areaDatoForm">

	
	<table align="center" width="100%">

		
		<tr border="0">
			<th style="font-size: small;">CUIT-CUIL</th>
			<th style="font-size: small;">RAZON SOCIAL</th>
			<th style="font-size: small;">CONDICION IVA</th>
			<th style="font-size: small;">SALDO <?php echo date('d/m/Y', strtotime($fecha_saldo_anterior))?></th>
			<th style="font-size: small;">PAGOS</th>
			<th style="font-size: small;">N.CREDITOS</th>
			<th style="font-size: small;">FACTURAS</th>
			<th style="font-size: small;">SALDO PERIODO</th>
			<th style="font-size: small;">SALDO <?php echo date('d/m/Y', strtotime($hastaFecha))?></th>
		</tr>

		<?php
		foreach ($saldos as $saldo):
		?>
			<tr>
				<td style="font-size: x-small;"><?php echo $saldo['Proveedor']['cuit']?></td>
				<td style="font-size: x-small;"><?php echo $saldo['Proveedor']['razon_social']?></td>
				<td style="font-size: x-small;"><?php echo $saldo['GlobalDato']['concepto_1']?></td>
				<td align="right" style="font-size: small;"><?php echo number_format($saldo['0']['saldo_anterior'],2)?></td>
				<td align="right" style="font-size: small;"><?php echo number_format($saldo['0']['pagos'],2)?></td>
				<td align="right" style="font-size: small;"><?php echo number_format($saldo['0']['credito'],2)?></td>
				<td align="right" style="font-size: small;"><?php echo number_format($saldo['0']['debito'],2)?></td>
				<td align="right" style="font-size: small;"><?php echo number_format($saldo['0']['saldo'],2)?></td>
				<td align="right" style="font-size: small;"><?php echo number_format($saldo['0']['saldo_actual'],2)?></td>
			</tr>
		<?php endforeach; ?>
	</table>
	
	
	
</div>
 	