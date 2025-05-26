<?php echo $this->renderElement('head',array('title' => 'LISTADOS','plugin' => 'config'))?>
<?php echo $this->renderElement('proveedor_listados/menu_listado',array('plugin' => 'proveedores'))?>
<h1>LIBRO IVA COMPRAS</h1>
<hr>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('form_iva_compra').disable();
	<?php endif;?>

});
</script>
<?php
?>
<?php echo $frm->create(null,array('action' => 'iva_compra','id' => 'form_iva_compra'))?>
<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>FECHA DESDE:</td>
			<td><?php echo $frm->calendar('ListadoIvaCompra.fecha_desde', null, $fecha_desde, '2000',date("Y") + 1)?></td>
			<td>HASTA:</td>
			<td><?php echo $frm->calendar('ListadoIvaCompra.fecha_hasta', null, $fecha_hasta, '2000',date("Y") + 1)?></td>
		</tr>
		
		<tr>
			<td>TIPO PROVEEDOR:</td>
			<td><?php echo $frm->input('ListadoIvaCompra.tipo',array('type'=>'select','options'=>array(3 => 'TODOS', 0 => 'PROVEEDOR', 1 => 'COMERCIO'), 'selected' => $tipo));?></td>
			<td><?php echo $frm->submit("ACEPTAR")?></td>
		</tr>
	
	</table>
	
</div>
<?php echo $frm->end()?>

 	
<?php if(isset($showTabla) && $showTabla == 1):


?>

<div class="areaDatoForm">
	
	<?php 
		echo $controles->botonGenerico('/proveedores/proveedor_listados/iva_compra_xls/' . $fecha_desde . '/' . $fecha_hasta . '/' . $tipo,'controles/ms_excel.png', null, array('id' => 'xls'));
		echo $controles->botonGenerico('/proveedores/proveedor_listados/iva_compra_pdf/' . $fecha_desde . '/' . $fecha_hasta . '/' . $tipo,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
	?>
	
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
		if($renglon['ProveedorFactura']['tipo'] == 'NC'):
			$nInoGra -= $renglon['ProveedorFactura']['importe_no_gravado'];
			$nIGrava -= $renglon['ProveedorFactura']['importe_gravado'];
			$nImpIva -= $renglon['ProveedorFactura']['importe_iva'];
			$nIPerce -= $renglon['ProveedorFactura']['percepcion'];
			$nIReten -= $renglon['ProveedorFactura']['retencion'];
			$nIImInt -= $renglon['ProveedorFactura']['impuesto_interno'];
			$nIInBru -= $renglon['ProveedorFactura']['ingreso_bruto'];
			$nIOImpu -= $renglon['ProveedorFactura']['otro_impuesto'];
			$nITotCo -= $renglon['ProveedorFactura']['total_comprobante'];
		else:
			$nInoGra += $renglon['ProveedorFactura']['importe_no_gravado'];
			$nIGrava += $renglon['ProveedorFactura']['importe_gravado'];
			$nImpIva += $renglon['ProveedorFactura']['importe_iva'];
			$nIPerce += $renglon['ProveedorFactura']['percepcion'];
			$nIReten += $renglon['ProveedorFactura']['retencion'];
			$nIImInt += $renglon['ProveedorFactura']['impuesto_interno'];
			$nIInBru += $renglon['ProveedorFactura']['ingreso_bruto'];
			$nIOImpu += $renglon['ProveedorFactura']['otro_impuesto'];
			$nITotCo += $renglon['ProveedorFactura']['total_comprobante'];
		endif;
	?>
		<tr>
			<td style="font-size: x-small;"><?php echo date('d/m/Y',strtotime($renglon['ProveedorFactura']['fecha_comprobante']))?></td>
			<td style="font-size: x-small;"><?php echo $renglon['ProveedorFactura']['comprobante_libro']?></td>
			<td style="font-size: x-small;"><?php echo $renglon['ProveedorFactura']['razon_social']?></td>
			<td style="font-size: x-small;"><?php echo $renglon['ProveedorFactura']['cuit']?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ProveedorFactura']['importe_no_gravado'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ProveedorFactura']['importe_gravado'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ProveedorFactura']['importe_iva'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ProveedorFactura']['percepcion'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ProveedorFactura']['retencion'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ProveedorFactura']['impuesto_interno'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ProveedorFactura']['ingreso_bruto'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ProveedorFactura']['otro_impuesto'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($renglon['ProveedorFactura']['total_comprobante'] * ($renglon['ProveedorFactura']['tipo'] == 'NC' ? -1 : 1),2)?></td>
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
	
	<?php 
	echo $controles->botonGenerico('/proveedores/proveedor_listados/iva_compra_xls/' . $fecha_desde . '/' . $fecha_hasta . '/' . $tipo,'controles/ms_excel.png', null, array('id' => 'xls'));
	echo $controles->botonGenerico('/proveedores/proveedor_listados/iva_compra_pdf/' . $fecha_desde . '/' . $fecha_hasta . '/' . $tipo,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));

//	echo $frm->create(null,array('action' => 'iva_compra/','id' => 'iva_compra'));
//	echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'APROBAR','URL' => '/proveedores/listados/iva_compra/'));
	?>
	
	
</div>
<?php endif;?>
 	