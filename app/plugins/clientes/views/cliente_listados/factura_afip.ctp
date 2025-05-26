<?php echo $this->renderElement('head',array('title' => 'LISTADOS','plugin' => 'config'))?>
<?php echo $this->renderElement('cliente_listados/menu_listado',array('plugin' => 'clientes'))?>
<h1>FACTURACION AFIP</h1>
<hr>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('form_iva_venta').disable();
	<?php endif;?>

});
</script>
<?php echo $frm->create(null,array('action' => 'factura_afip','id' => 'form_iva_venta'))?>
<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>FECHA DESDE:</td>
			<td><?php echo $frm->calendar('ListadoIvaVenta.fecha_desde', null, $fecha_desde, '2000',date("Y") + 1)?></td>
		</tr>
		
		<tr>
			<td>HASTA:</td>
			<td><?php echo $frm->calendar('ListadoIvaVenta.fecha_hasta', null, $fecha_hasta, '2000',date("Y") + 1)?></td>
		</tr>
		
		<tr>
			<td><?php echo $frm->submit("ACEPTAR")?></td>
		</tr>
	
	</table>
	
</div>
<?php echo $frm->end()?>

 	
<?php if(isset($showTabla) && $showTabla == 1):?>



<div class="areaDatoForm">
	
	
<table align="center" width="100%">

	<col width="40" />
	<col width="150" />
	<col width="400" />
	<col width="60" />
	<col width="60" />
	<col width="60" />
	<col width="60" />
	<col width="60" />
	<col width="60" />
	<col width="60" />
	<col width="60" />
	<col width="60" />
		
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
                <th style="font-size: small;"></th>
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
                        <?php $url = '/clientes/cliente_listados/ws_factura_afip/' . $renglon['ClienteFactura']['id']?>
                        <td><?php echo $controles->btnModalBox(array('title' => 'FACTURA AFIP','img'=> 'information.png','url' => $url,'h' => 350, 'w' => 550))?></td>
		</tr>
	<?php endforeach; ?>
</table>
	

	
</div>
<?php endif;?>
