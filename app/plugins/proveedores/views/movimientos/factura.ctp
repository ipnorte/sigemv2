<?php echo $this->renderElement('proveedor/proveedor_header',array('proveedor' => $proveedores))?>
<?php
$movimiento = $this->data['Movimiento'];
?>
<script language="Javascript" type="text/javascript">
	Event.observe(window, 'load', function() {
		<?php if($bloquearCabecera==1): 
			$letraDoc = $movimiento['letra_documento'];?>
			$('formCabFactura').disable();
			$('formDetFactura').enable();
			document.getElementById("MovimientoImporteTotal").disabled = true;
			$('MovimientoImporteGravado').focus();
		<?php else:
			$letraDoc = 'A';?>
			$('formCabFactura').enable();
		<?php endif; ?>
	});
</script>
	
<script language="Javascript" type="text/javascript">
	function ctrlForm(){
		var totalComprobante = $('MovimientoImporteTotal').getValue();
		
		if(totalComprobante == 0){
			alert('EL COMPROBANTE NO PUEDE SER IGUAL A 0 (CERO)');
			$('MovimientoImporteGravado').select();
			$('MovimientoImporteGravado').focus();
			return false;
		}
                $('btn_submit').disable();
                
		return true;
	}
	
	function totalComprobante(param){
		if(param=='1'){
			var importeIva = new Number($('MovimientoImporteGravado').getValue());
			var valorTasaIva = new Number($('MovimientoTasaIva').getValue());
			valorTasaIva = valorTasaIva / 100;
			importeIva = importeIva * valorTasaIva;
			importeIva = importeIva.toFixed(2);
			$("MovimientoImporteIva").value = importeIva;
		}
		var nNGr = new Number($('MovimientoImporteGravado').getValue());
		var nImI = new Number($('MovimientoImporteIva').getValue());
		var nNnG = new Number($('MovimientoImporteNoGravado').getValue());
		var nPer = new Number($('MovimientoPercepcion').getValue());
		var nRet = new Number($('MovimientoRetencion').getValue());
		var nIpI = new Number($('MovimientoImpuestoInterno').getValue());
		var nInB = new Number($('MovimientoIngresoBruto').getValue());
		var nOtI = new Number($('MovimientoOtroImpuesto').getValue());
		var nTot = nNGr + nImI + nNnG + nPer + nRet + nIpI + nInB + nOtI;

		document.getElementById("MovimientoImporteTotal").value = nTot.toFixed(2);
		document.getElementById("MovimientoImporteVenc1").value = nTot.toFixed(2);
		document.getElementById("MovimientoTotalComprobante").value = nTot.toFixed(2);

		document.getElementById("MovimientoImporteTotal").disabled = true;
		
	}
</script>
<?php echo $frm->create(null,array('name'=>'formCabFactura','id'=>'formCabFactura','onsubmit' => "", 'action' => "factura/" . $proveedores['Proveedor']['id'] ));?>
<H3>COMPROBANTES DEL PROVEEDOR</H3>
<div class="areaDatoForm">
<table class="tbl_form">
	<tr>
		<td>TIPO COMPROBANTE:</td>
		<td>
			<?php echo $this->renderElement('global_datos/combo_global',array(
                                    'plugin'=>'config',
                                    'label' => "",
                                    'model' => 'Movimiento.tipo_comprobante',
                                    'disabled' => false,
                                    'empty' => false,
                                    'selected' => $movimiento['tipo_comprobante'],
                                    'metodo' => 'get_tipo_comprobante'
			))?>				
		</td>
		<td>LETRA:</td>
		<td>
			<?php echo $frm->input('Movimiento.letra_comprobante',array('type' => 'select','options' => array('A' => ' <A> ', 'B' => ' <B> ','C' => ' <C> ', 'M' => ' <M>', 'O' => 'OTRO'),'selected' => $letraDoc));?>
		</td>
		<td>PUNTO VENTA:</td>
		<td><?php echo $frm->number('Movimiento.punto_venta_comprobante',array('label'=>'','size'=>5,'maxlength'=>5));?></td>
		<td>NUMERO:</td>
		<td><?php echo $frm->number('Movimiento.numero_comprobante',array('label'=>'','size'=>8,'maxlength'=>8));?></td>
        </tr>
        <tr>
            <td>COMENTARIO:</td>
            <td colspan="6"><?php echo $frm->input('Movimiento.comentario', array('label'=>'','size'=>60,'maxlength'=>50)) ?></td>
            <td><?php echo $frm->submit('INGRESAR')?></td>
		
	</tr>
</table>
</div>
<?php echo $frm->end(); ?>

<?php if($bloquearCabecera==1 && $existeFactura == 0): ?>
<div class="areaDatoForm">
<?php echo $frm->create(null,array('name'=>'formDetFactura','id'=>'formDetFactura','onsubmit' => "return ctrlForm();", 'action' => "factura/" . $proveedores['Proveedor']['id'] ));?>
<table class="tbl_form">
	<tr>
	<td>
		<table class="tbl_form">
			<tr>
				<td>Neto Gravado:</td>
				<td><div class="input text"><label for="MovimientoImporteGravado"></label><input name="data[Movimiento][importe_gravado]" type="text" value="0" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" onblur="totalComprobante(0)" id="MovimientoImporteGravado" /></div></td>

				<td>Tasa IVA:</td>
				<td><div class="input text"><label for="MovimientoTasaIva"></label><input name="data[Movimiento][tasa_iva]" type="text" value="0" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" onblur="totalComprobante(1)" id="MovimientoTasaIva" /></div></td>
			</tr>
			<tr>
				<td>Importe IVA:</td>
				<td><div class="input text"><label for="MovimientoImporteIva"></label><input name="data[Movimiento][importe_iva]" type="text" value="0" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" onblur="totalComprobante(0)" id="MovimientoImporteIva" /></div></td>
			</tr>
                        <tr>
				<td>Neto no Gravado:</td>
				<td><div class="input text"><label for="MovimientoImporteNoGravado"></label><input name="data[Movimiento][importe_no_gravado]" type="text" value="0" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" onblur="totalComprobante(0)" id="MovimientoImporteNoGravado" /></div></td>
			</tr>
                        <tr>
				<td>Percepciones:</td>
				<td><div class="input text"><label for="MovimientoPercepcion"></label><input name="data[Movimiento][percepcion]" type="text" value="0" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" onblur="totalComprobante(0)" id="MovimientoPercepcion" /></div></td>
			</tr>
			<tr>
				<td>Retenciones:</td>
				<td><div class="input text"><label for="MovimientoRetencion"></label><input name="data[Movimiento][retencion]" type="text" value="0" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" onblur="totalComprobante(0)" id="MovimientoRetencion" /></div></td>
			</tr>
			<tr>
				<td>Impuestos Internos:</td>
				<td><div class="input text"><label for="MovimientoImpuestoInterno"></label><input name="data[Movimiento][impuesto_interno]" type="text" value="0" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" onblur="totalComprobante(0)" id="MovimientoImpuestoInterno" /></div></td>
			</tr>
			<tr>
				<td>Ingresos Brutos:</td>
				<td><div class="input text"><label for="MovimientoIngresoBruto"></label><input name="data[Movimiento][ingreso_bruto]" type="text" value="0" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" onblur="totalComprobante(0)" id="MovimientoIngresoBruto" /></div></td>
			</tr>
			<tr>
				<td>Otros Impuestos:</td>
				<td><div class="input text"><label for="MovimientoOtroImpuesto"></label><input name="data[Movimiento][otro_impuesto]" type="text" value="0" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" onblur="totalComprobante(0)" id="MovimientoOtroImpuesto" /></div></td>
			</tr>
			<tr>
				<td>Importe Total:</td>
				<td><?php echo $frm->number('Movimiento.importe_total',array('size'=>12,'maxlength'=>12, 'disabled' => 'disabled'));?></td>
			</tr>
		</table>
	</td>
	<td>	
		<table class="tbl_form">
			<tr>
				<td>Fecha Comprobante:</td>
				<td><?php echo $frm->calendar('Movimiento.fecha_comprobante',null,null,date('Y')-3,date('Y')+1)?></td>
				<td>Concepto de Gasto:</td>
				<td>
					<?php echo $this->renderElement('global_datos/combo_global',array(
																			'plugin'=>'config',
																			'label' => "",
																			'model' => 'Movimiento.concepto_gasto',
																			'disabled' => false,
																			'empty' => false,
																			'selected' => $proveedores['Proveedor']['concepto_gasto'],
																			'metodo' => 'get_concepto_gasto'
					))?>				
				</td>
			</tr>
			<tr>
				<td>Imputar al IVA Compra de:</td>
				<td><?php echo $frm->periodo('Movimiento.periodo',null,null,date('Y')-3,date('Y')+1) ?></td>
				<td>Tipo Asiento:</td>
				<td><?php echo $this->renderElement('tipo_asiento/combo_tipo_asiento',array(
										'plugin'=>'proveedores',
										'label' => "",
										'model' => 'Movimiento.proveedor_tipo_asiento_id',
										'disabled' => false,
										'empty' => false,
										'selected' => $proveedores['Proveedor']['proveedor_tipo_asiento_id']))?>
				</td>			
			</tr>
			<tr>
                            <td colspan="4">
				<table>
					<tr>
						<th>Vencimiento</th>
						<th>Importe</th>
					</tr>
					<tr>
						<td><?php echo $frm->calendar('Movimiento.vencimiento1',null,null,date('Y')-3,date('Y')+1)?></td>
						<td><?php echo $frm->money('Movimiento.importe_venc1','',0.00) ?></td>
					</tr>
					<tr>
						<td><?php echo $frm->calendar('Movimiento.vencimiento2',null,null,date('Y')-3,date('Y')+1)?></td>
						<td><?php echo $frm->money('Movimiento.importe_venc2','',0.00) ?></td>
					</tr>
					<tr>
						<td><?php echo $frm->calendar('Movimiento.vencimiento3',null,null,date('Y')-3,date('Y')+1)?></td>
						<td><?php echo $frm->money('Movimiento.importe_venc3','',0.00) ?></td>
					</tr>
					<tr>
						<td><?php echo $frm->calendar('Movimiento.vencimiento4',null,null,date('Y')-3,date('Y')+1)?></td>
						<td><?php echo $frm->money('Movimiento.importe_venc4','',0.00) ?></td>
					</tr>
					<tr>
						<td><?php echo $frm->calendar('Movimiento.vencimiento5',null,null,date('Y')-3,date('Y')+1)?></td>
						<td><?php echo $frm->money('Movimiento.importe_venc5','',0.00) ?></td>
					</tr>
					<tr>
						<td><?php echo $frm->calendar('Movimiento.vencimiento6',null,null,date('Y')-3,date('Y')+1)?></td>
						<td><?php echo $frm->money('Movimiento.importe_venc6','',0.00) ?></td>
					</tr>
					<tr>
						<td><?php echo $frm->calendar('Movimiento.vencimiento7',null,null,date('Y')-3,date('Y')+1)?></td>
						<td><?php echo $frm->money('Movimiento.importe_venc7','',0.00) ?></td>
					</tr>
					<tr>
						<td><?php echo $frm->calendar('Movimiento.vencimiento8',null,null,date('Y')-3,date('Y')+1)?></td>
						<td><?php echo $frm->money('Movimiento.importe_venc8','',0.00) ?></td>
					</tr>
					<tr>
						<td><?php echo $frm->calendar('Movimiento.vencimiento9',null,null,date('Y')-3,date('Y')+1)?></td>
						<td><?php echo $frm->money('Movimiento.importe_venc9','',0.00) ?></td>
					</tr>
					<tr>
						<td><?php echo $frm->calendar('Movimiento.vencimiento10',null,null,date('Y')-3,date('Y')+1)?></td>
						<td><?php echo $frm->money('Movimiento.importe_venc10','',0.00) ?></td>
					</tr>
				</table>
                            </td>
			</tr>
		</table>
	</td>
	</tr>
</table>
<?php echo $frm->hidden('Movimiento.proveedor_id', array('value' => $proveedores['Proveedor']['id'])) ?>
<?php echo $frm->hidden('Movimiento.estado', array('value' => 'A')) ?>
<?php echo $frm->hidden('Movimiento.tipo_comprobante', array('value' => $movimiento['tipo_comprobante'])) ?>
<?php echo $frm->hidden('Movimiento.letra_comprobante', array('value' => $movimiento['letra_comprobante'])) ?>
<?php echo $frm->hidden('Movimiento.punto_venta_comprobante', array('value' => $movimiento['punto_venta_comprobante'])) ?>
<?php echo $frm->hidden('Movimiento.numero_comprobante', array('value' => $movimiento['numero_comprobante'])) ?>
<?php echo $frm->hidden('Movimiento.comentario', array('value' => $movimiento['comentario'])) ?>
<?php echo $frm->hidden('Movimiento.total_comprobante') ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/Proveedores/movimientos/cta_cte/' . $proveedores['Proveedor']['id']))?>
</div>
<?php endif; ?>