<?php echo $this->renderElement('head',array('title' => 'LISTADOS','plugin' => 'config'))?>
<?php echo $this->renderElement('cliente_listados/menu_listado',array('plugin' => 'clientes'))?>
<h3>LISTADO POR TIPO DE ASIENTO :: FECHA: <?php echo date('d/m/Y', strtotime($desdeFecha)) . ' AL ' . date('d/m/Y', strtotime($hastaFecha))?></h3>


<?php
App::import('Model', 'clientes.ClienteListado');

$oClienteListado = new ClienteListado();


foreach($aTipoAsiento as $tipoAsiento):

	if($tipoAsiento[0]['facturado'] > 0 || $tipoAsiento[0]['credito'] > 0):
		$facturas = $oClienteListado->factura_tipo_asiento_detalle($tipoAsiento['ClienteTipoAsiento']['id'], $desdeFecha, $hastaFecha);
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