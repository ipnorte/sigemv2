<?php
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=tipo-asiento.xls");
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");

App::import('Model', 'clientes.ClienteListado');

$oClienteListado = new ClienteListado();

?>

<h1>LISTADO POR TIPO ASIENTO</h1>


<?php
foreach($aTipoAsiento as $tipoAsiento):

	if($tipoAsiento[0]['facturado'] > 0 || $tipoAsiento[0]['credito'] > 0):
		$facturas = $oClienteListado->factura_tipo_asiento_detalle($tipoAsiento['ClienteTipoAsiento']['id'], $desdeFecha, $hastaFecha, $tipo);
?>

	<h3><?php echo $tipoAsiento['ClienteTipoAsiento']['concepto'] ?> </h3>
	<table>
		<tr>
			<th>FECHA</th>
			<th>COMPROBANTE</th>
			<th>RAZON SOCIAL</th>
			<th>C.U.I.T.</th>
			<th>TOTAL</th>
		</tr>

		<?php
		$nTotalComprobante = 0;
		foreach ($facturas as $renglon):
						
			if($renglon['ClienteFactura']['tipo'] == 'NC'):
				$nTotalComprobante -= $renglon['ClienteFactura']['total_comprobante']; 
			else:
				$nTotalComprobante += $renglon['ClienteFactura']['total_comprobante']; 
			endif;
		?>
			<tr>
				<td align="center"><?php echo date('d/m/Y',strtotime($renglon['ClienteFactura']['fecha_comprobante']))?></td>
				<td align="center"><?php echo $renglon['ClienteFactura']['comprobante_libro']?></td>
				<td><?php echo $renglon['Cliente']['razon_social']?></td>
				<td align="left"><?php echo $renglon['Cliente']['cuit']?></td>
				<td align="right"><?php echo number_format($renglon['ClienteFactura']['total_comprobante'] * ($renglon['ClienteFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			</tr>
		
			
		<?php	
		endforeach;?>

		<tr>
			<td colspan="4" align="right">TOTAL:</td>
			<td align="right"><?php echo number_format($nTotalComprobante,2)?></td>
		</tr>
		

	</table>
	<?php endif;?>	


<?php
endforeach;
?>