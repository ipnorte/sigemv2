<?php echo $this->renderElement('proveedor_listados/menu_listado',array('plugin' => 'proveedores'))?>
<?php echo $this->renderElement('proveedor/datos_proveedor',array('proveedor_id'=>$proveedor['Proveedor']['id'],'plugin' => 'proveedores'))?>
<h3>LISTADO POR CONCEPTO DEL GASTO :: FECHA: <?php echo date('d/m/Y', strtotime($desdeFecha)) . ' AL ' . date('d/m/Y', strtotime($hastaFecha))?></h3>


<?php
App::import('Model', 'proveedores.ProveedorListado');

$oProveedorListado = new ProveedorListado();

foreach($aConceptoGasto as $ConceptoGasto):

	if($ConceptoGasto[0]['facturado'] > 0 || $ConceptoGasto[0]['credito'] > 0):
		$facturas = $oProveedorListado->factura_concepto_gasto_detalle($ConceptoGasto[0]['id'], $desdeFecha, $hastaFecha, $tipo);
?>

	<h3><?php echo $ConceptoGasto[0]['concepto_1'] ?> </h3>
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
						
			if($renglon['ProveedorFactura']['tipo'] == 'NC'):
				$nTotalComprobante -= $renglon['ProveedorFactura']['total_comprobante']; 
			else:
				$nTotalComprobante += $renglon['ProveedorFactura']['total_comprobante']; 
			endif;
		?>
			<tr>
				<td align="center"><?php echo date('d/m/Y',strtotime($renglon['ProveedorFactura']['fecha_comprobante']))?></td>
				<td align="center"><?php echo $renglon[0]['comprobante_libro']?></td>
				<td><?php echo $renglon['Proveedor']['razon_social']?></td>
				<td align="left"><?php echo $renglon['Proveedor']['cuit']?></td>
				<td align="right"><?php echo number_format($renglon['ProveedorFactura']['total_comprobante'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
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