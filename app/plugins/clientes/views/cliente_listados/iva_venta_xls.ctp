<?php
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=iva-venta.xls");
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");
?>

<h1>LIBRO IVA VENTAS</h1>
	
<table align="center" width="100%">

<!--	<col width="100" />-->
<!--	<col width="250" />-->
<!--	<col width="250" />-->
<!--	<col width="100" />-->
<!--	<col width="100" />-->
<!--	<col width="100" />-->
		
	<tr border="0">
		<th style="font-size: small;">FECHA</th>
		<th style="font-size: small;">COMPROBANTE</th>
		<th style="font-size: small;">CONCEPTO</th>
		<th style="font-size: small;">CUIT</th>
		<th style="font-size: small;">IMP.NO GRAVADO</th>
		<th style="font-size: small;">IMP. GRAVADO</th>
		<th style="font-size: small;">IMP. IVA</th>
		<th style="font-size: small;">PERCEPCION</th>
		<th style="font-size: small;">RETENCION</th>
		<th style="font-size: small;">IMP. INTERNO</th>
		<th style="font-size: small;">ING. BRUTO</th>
		<th style="font-size: small;">OTROS IMP.</th>
		<th style="font-size: small;">TOTAL</th>
	</tr>

	<?php
	$nInoGra = 0; $nIGrava = 0; $nImpIva = 0; $nIPerce = 0; $nIReten = 0; $nIImInt = 0; $nIInBru = 0; $nIOImpu = 0; $nITotCo = 0;
	foreach ($facturas as $renglon):
		if($renglon['ClienteFactura']['tipo'] == 'NC'):
			$nInoGra -= $renglon['ClienteFactura']['importe_no_gravado'];
			$nIGrava -= $renglon['ClienteFactura']['importe_gravado'];
			$nImpIva -= $renglon['ClienteFactura']['importe_iva'];
			$nIPerce -= $renglon['ClienteFactura']['percepcion'];
			$nIReten -= $renglon['ClienteFactura']['retencion'];
			$nIImInt -= $renglon['ClienteFactura']['impuesto_interno'];
			$nIInBru -= $renglon['ClienteFactura']['ingreso_bruto'];
			$nIOImpu -= $renglon['ClienteFactura']['otro_impuesto'];
			$nITotCo -= $renglon['ClienteFactura']['total_comprobante'];
		else:
			$nInoGra += $renglon['ClienteFactura']['importe_no_gravado'];
			$nIGrava += $renglon['ClienteFactura']['importe_gravado'];
			$nImpIva += $renglon['ClienteFactura']['importe_iva'];
			$nIPerce += $renglon['ClienteFactura']['percepcion'];
			$nIReten += $renglon['ClienteFactura']['retencion'];
			$nIImInt += $renglon['ClienteFactura']['impuesto_interno'];
			$nIInBru += $renglon['ClienteFactura']['ingreso_bruto'];
			$nIOImpu += $renglon['ClienteFactura']['otro_impuesto'];
			$nITotCo += $renglon['ClienteFactura']['total_comprobante'];
		endif;
	?>
		<tr>
			<td style="font-size: x-small;"><?php echo date('d/m/Y',strtotime($renglon['ClienteFactura']['fecha_comprobante']))?></td>
			<td style="font-size: x-small;"><?php echo $renglon['ClienteFactura']['comprobante_libro']?></td>
			<td style="font-size: x-small;"><?php echo $renglon['ClienteFactura']['razon_social']?></td>
			<td style="font-size: x-small;"><?php echo $renglon['ClienteFactura']['cuit']?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ClienteFactura']['importe_no_gravado'] * ($renglon['ClienteFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ClienteFactura']['importe_gravado'] * ($renglon['ClienteFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ClienteFactura']['importe_iva'] * ($renglon['ClienteFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ClienteFactura']['percepcion'] * ($renglon['ClienteFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ClienteFactura']['retencion'] * ($renglon['ClienteFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ClienteFactura']['impuesto_interno'] * ($renglon['ClienteFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ClienteFactura']['ingreso_bruto'] * ($renglon['ClienteFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ClienteFactura']['otro_impuesto'] * ($renglon['ClienteFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ClienteFactura']['total_comprobante'] * ($renglon['ClienteFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
		</tr>
	<?php endforeach; ?>
		<tr>
			<td colspan="4" align="right" style="border-top: 1px solid black;font-size: small;">TOTAL GENERAL</td>
			<td align="right" style="border-top: 1px solid black;font-size: small;"><?php echo number_format($nInoGra,2)?></td>
			<td align="right" style="border-top: 1px solid black;font-size: small;"><?php echo number_format($nIGrava,2)?></td>
			<td align="right" style="border-top: 1px solid black;font-size: small;"><?php echo number_format($nImpIva,2)?></td>
			<td align="right" style="border-top: 1px solid black;font-size: small;"><?php echo number_format($nIPerce,2)?></td>
			<td align="right" style="border-top: 1px solid black;font-size: small;"><?php echo number_format($nIReten,2)?></td>
			<td align="right" style="border-top: 1px solid black;font-size: small;"><?php echo number_format($nIImInt,2)?></td>
			<td align="right" style="border-top: 1px solid black;font-size: small;"><?php echo number_format($nIInBru,2)?></td>
			<td align="right" style="border-top: 1px solid black;font-size: small;"><?php echo number_format($nIOImpu,2)?></td>
			<td align="right" style="border-top: 1px solid black;font-size: small;"><?php echo number_format($nITotCo,2)?></td>
		</tr>
</table>
 	