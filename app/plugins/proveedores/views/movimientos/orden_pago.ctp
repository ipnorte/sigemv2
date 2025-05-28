<?php 
$fechaPago = date('Ymd');
$importePago = 0.00;
$cntRenglones = 0;

if(isset($facturaPendiente)) $cntRenglones = count($facturaPendiente);

$rows = 0;
if($bloquear==1):
	$fechaPago = $this->data['Movimiento']['fecha_pago']['year'] . '-' . $this->data['Movimiento']['fecha_pago']['month'] . '-' . $this->data['Movimiento']['fecha_pago']['day'];
	$importePago = $this->data['Movimiento']['importe_cabecera'];
	$rows = count($facturaPendiente);
endif;
?>
<?php echo $this->renderElement('proveedor/proveedor_header',array('proveedor' => $proveedores))?>

<script language="Javascript" type="text/javascript">

	var rows = <?php echo $cntRenglones?>;
	var items = 0;
	var fechaPago = "<?php echo $fechaPago?>";
	var saldo_prov = <?php echo $saldo ?>;

	Event.observe(window, 'load', function() {
		
		var cabeceraPago = $('formCabeceraPago');
		
		<?php if($bloquear==1):?> 
			cabeceraPago.disabled = true;
			$('formCabeceraPago').disable();
			$('btn_submit').disable();
			selSum();
			ocultarOptionFPago();
		<?php endif; ?>
	
	});


	function ocultarOptionFPago(){

		$("cta_banco").hide();
		$("nro_opera").hide();
		$("fPago").hide();
		$("fVenc").hide();
		$("importe").hide();
		$("chq_cartera").hide();
		
		
	}	
	
	
	function ctrlPago(){
		var cbcImporte = $('MovimientoImporteCabecera').getValue();
		var nmbImporte = new Number(cbcImporte);
		 
		if(saldo_prov <= 0 && nmbImporte == 0){
			alert('DEBE INDICAR UN IMPORTE EN LA ORDEN DE PAGO');
			$('MovimientoImporteCabecera').focus();
			$('MovimientoImporteCabecera').select();
			return false;
		}

		if(saldo_prov <= 0 && nmbImporte > 0){
			if(!confirm("SE REALIZARA UN PAGO A CUENTA. \n \n ESTA SEGURO DE REALIZAR ESTA OPERACION")){return false;}
			else{return true;}
		}
		return true;
	}






</script>

<h3>Orden de Pago</h3>
<div class="areaDatoForm">
<?php echo $frm->create(null,array('name'=>'formCabeceraPago','id'=>'formCabeceraPago','onsubmit' => "return ctrlPago()", 'action' => "orden_pago/" . $proveedores['Proveedor']['id'] ));?>

		<table class="tbl_form">
			<tr>
				<td>Fecha del Pago:</td>
				<td><?php echo $frm->calendar('Movimiento.fecha_pago',null,$fechaPago,date('Y')-1,date('Y')+1)?></td>
				<td>Importe del Pago:</td>
				<td><?php echo $frm->money('Movimiento.importe_cabecera','',$importePago) ?></td>
			</tr>
			<tr>
				<td>Observaci&oacute;n</td>
				<td><?php echo $frm->input('Movimiento.observacion', array('label'=>'','size'=>60,'maxlength'=>50)) ?></td>
				<td><?php echo $frm->submit("SIGUIENTE")?></td>
				<td></td>
			</tr>
		</table>
	    <?php echo $frm->hidden("Movimiento.Cabecera", array('value' => 1)) ?>
<?php echo $frm->end(); ?>
</div>
<div style="clear: both;"></div>

<?php if($bloquear == 1): ?>
				 		
<script type="text/javascript">

	function actDatos(){
		var importe, pago, acumulado;
		
		$('ajax_loader_2124618328').hide();
		
		$("MovimientoBancoCuentaId").value = "";
		$("MovimientoNumeroOperacion").value = "";	
		$("MovimientoTipoPago").value = "";
		
		importe = new Number($F('MovimientoImporteEfectivo'));
		acumulado = new Number($('MovimientoAcumula').getValue());
		pago = new Number($('MovimientoImportePago').getValue());
		
		acumulado = pago - acumulado;

		$('MovimientoImporteEfectivo').value = acumulado.toFixed(2);
		if(acumulado.toFixed(2) >= importe){
			actualizaImporte(-importe, 0);
		}		
		else{
			actualizaImporte(0, 0);		
		}
		
	}


	function actualizaImporte(valor, idCheque){
		var v1, v2, v3, acumulado;
			
		v1 = valor;
		v2 = $F("MovimientoImporteEfectivo");
		v1 = new Number(v1);
		v2 = new Number(v2);
		v3 = new Number(v1 + v2);
				 				
		acumulado = new Number($('MovimientoAcumula').getValue());
		acumulado -= v1;
		$("MovimientoAcumula").value = acumulado.toFixed(2);
		
		if(idCheque > 0){
			var checkbox = $('MovimientoCheck' + idCheque);
			checkbox.checked = !checkbox.checked;
			checkbox.enable();
		}
				 				

		if(v3 == 0.00) $('btn_submit').enable();
		else $('btn_submit').disable();
		
		
		v3 = v3.toFixed(2);
		$("MovimientoImporteEfectivo").value = v3;
		$("MovimientoTipoPago").value = "";
		ocultarOptionFPago();
	}

	
	function chqOnclick(chequeId, importe, uuid)
	{
		var checkbox = $('MovimientoCheck' + chequeId);
		var checked = 0;

		if(checkbox.checked == false) return true;
		if(checkbox.checked == true) checked = 1;
		
		cargarRenglones(chequeId, checked, uuid);
	
		return true;
	}	


	function cargarRenglones(chequeId, checked, uuid)
	{
		fecha_operacion = $('MovimientoFechaOperacion').getValue();
		importe_detalle = $('MovimientoImporteDetalle').getValue();
		importe_pago = $('MovimientoImportePago').getValue();
		clave = 'proveedor_id';
		valor = <?php echo $proveedores['Proveedor']['id']?>;
		
		new Ajax.Updater
		(
			'grilla_pagos',
			'<?php echo $this->base?>/proveedores/movimientos/cargar_cheques/'+chequeId+'/'+checked+'/'+fecha_operacion+'/'+importe_detalle+'/'+importe_pago+'/'+clave+'/'+valor+'/'+uuid, 
			{
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request) 
						{
							$('msjAjax_' + chequeId).show();
						},
				onComplete:function(request) 
						{
							actCheque(chequeId);
						}, 
				requestHeaders:['X-Update', 'grilla_pagos']
			}
		);

		return true;

	}


	function actCheque(chequeId){
		var checkbox = $('MovimientoCheck' + chequeId);
		var txtImpCheque = checkbox.getValue();
		var txtImpEfectivo = $("MovimientoImporteEfectivo").getValue();
		
		var impEfectivo = new Number(txtImpEfectivo);
		var impCheque = new Number(txtImpCheque);
		
		$('msjAjax_' + chequeId).hide();
		$('MovimientoTipoPago').value = '';


		if(impCheque > impEfectivo){
			checkbox.checked = false;
		}else{
			checkbox.disable();
			impCheque = impCheque * (-1);
			actualizaImporte(impCheque, 0);
		}

		ocultarOptionFPago();
	}
	

	
	function ctrlDetalle(){
		var cbcImporte = $('MovimientoImporteCabecera').getValue();
		var nmbImporte = new Number(cbcImporte);
		nmbImporte = nmbImporte.toFixed(2);
		
		var dtlImporte = $('MovimientoImporteDetalle').getValue();
		var nmbImpDetalle = new Number(dtlImporte);
		nmbImpDetalle = nmbImpDetalle.toFixed(2);
		
		var nmbAnticipo = nmbImporte - nmbImpDetalle;
		
		if((nmbAnticipo > 0 && nmbImpDetalle > 0) || (nmbAnticipo > 0 && nmbImpDetalle == 0 && saldo_prov > 0)){
			if(!confirm('SE GENERARA UN PAGO A CUENTA DE $ ' + nmbAnticipo + '\n \n ESTA SEGURO DE REALIZAR ESTA OPERACION')){return false;}
			else{return true;}
		}
		return true;
	}
	

	function chkOnclickOld(){
		selSum();

		acumulado = parseFloat($('MovimientoAcumula').getValue());

		pago = document.getElementById("MovimientoImportePago").value;
		pago = parseFloat(pago);
		resto = pago - acumulado;
		
		if(resto == 0 && items == 1) $('btn_submit').enable();
		else $('btn_submit').disable();
		document.getElementById("MovimientoImporteEfectivo").value = resto;
		document.getElementById("MovimientoTipoPago").value = "";
		ocultarOptionFPago();
		
	}	


	function chkOnclick(){
		var resto, acumulado, pago;
		
		selSum();

		acumulado = new Number($('MovimientoAcumula').getValue());
		pago = new Number($("MovimientoImportePago").getValue());
		resto = new Number(0);

		resto = pago - acumulado;
		
		if(resto == 0 && items == 1) $('btn_submit').enable();
		else $('btn_submit').disable();
		
		document.getElementById("MovimientoImporteEfectivo").value = resto.toFixed(2);
		document.getElementById("MovimientoTipoPago").value = "";
		ocultarOptionFPago();
		
	}	


	function selSum(){	
		var totalSeleccionado = 0;
		var importeCabecera = $('MovimientoImporteCabecera').getValue();
		var importePago = $('MovimientoImportePago').getValue();
		var error = "";	

		items = 0;
		
		importeCabecera = new Number(importeCabecera);
		importeCabecera = importeCabecera.toFixed(2);
		
		for (i=1;i<=rows;i++){
			var celdas = $('TRL_' + i).immediateDescendants();
			oChkCheck = document.getElementById('MovimientoSaldo_' + i);
			oTxtImpoPagar = document.getElementById('MovimientoImporteAPagar_' + i);
			if (oChkCheck.checked){
				items = 1;
				strValTxt = oTxtImpoPagar.value;
				numValTxt = new Number(strValTxt);
				numValTxt = numValTxt.toFixed(2);
				impoTxt = numValTxt * 100;
				impoTxt = impoTxt.toFixed(0);
	
				strChkTxt = oChkCheck.value;
				numChkTxt = new Number(strChkTxt);
				numChkTxt = numChkTxt / 100;
				numChkTxt = numChkTxt.toFixed(2);
	
				if (impoTxt == 0){
					alert("EL IMPORTE NO PUEDE SER 0 (CERO)");
					oChkCheck.checked = false;
					document.getElementById('MovimientoImporteAPagar_' + i).value = numChkTxt;
					break;
				}
	
				chkValue = parseInt(oChkCheck.value);
	
				if(chkValue < 0){
	
					if(impoTxt > 0){
						alert("EL IMPORTE NO PUEDE SER MAYOR A 0 (CERO)");
						oChkCheck.checked = false;
						document.getElementById('MovimientoImporteAPagar_' + i).value = numChkTxt;
						break;
					}
					if(impoTxt < chkValue){
						alert("EL IMPORTE INDICADO " + strValTxt + " NO PUEDE SER MENOR AL SALDO");
						oChkCheck.checked = false;
						document.getElementById('MovimientoImporteAPagar_' + i).value = numChkTxt;
						break;
					}
				}else{
				
					if(impoTxt > chkValue){
						alert("EL IMPORTE INDICADO " + strValTxt + " NO PUEDE SER SUPERIOR AL SALDO");
						oChkCheck.checked = false;
						document.getElementById('MovimientoImporteAPagar_' + i).value = numChkTxt;
						break;
					}
				}
				impoTxt = parseInt(impoTxt);
				totalSeleccionado = totalSeleccionado + impoTxt;
	
				celdas.each(function(td){td.addClassName("selected");});
			}else{
				celdas.each(function(td){td.removeClassName("selected");});
			}	
		}
	
//		document.getElementById('MovimientoImportePago').value = 0;

		totalSeleccionado = totalSeleccionado/100;
		
		if(totalSeleccionado >= importeCabecera){
			importePago = totalSeleccionado;
			$('MovimientoImportePago').value = totalSeleccionado;
		}
		
		$('MovimientoImporteEfectivo').value = importePago;
		
		$("MovimientoImporteDetalle").value = totalSeleccionado;
		totalSeleccionado = FormatCurrency(totalSeleccionado);
		$("MovimientoImporteDetalleMostrar").value = totalSeleccionado;
		$("MovimientoImporteDetalleMostrar").disabled = true;
	}


	function seleccionPago(){
		var seleccion = $('MovimientoTipoPago').getValue();

		$("MovimientoFpagoYear").value = $F('MovimientoFechaOperacion').substring(0,4);
		$("MovimientoFpagoMonth").value = $F('MovimientoFechaOperacion').substring(5,7);
		$("MovimientoFpagoDay").value = $F('MovimientoFechaOperacion').substring(8);
				
		$("MovimientoFvencYear").value = $F('MovimientoFechaOperacion').substring(0,4);
		$("MovimientoFvencMonth").value = $F('MovimientoFechaOperacion').substring(5,7);
		$("MovimientoFvencDay").value = $F('MovimientoFechaOperacion').substring(8);
		

		document.getElementById("MovimientoFpagoDay").disabled = false;
		document.getElementById("MovimientoFpagoMonth").disabled = false;
		document.getElementById("MovimientoFpagoYear").disabled = false;

		ocultarOptionFPago();


		if(seleccion == 'EF'){
			document.getElementById("MovimientoTipoPagoDesc").value = "EFECTIVO"
			$("cta_banco").hide();
			$("nro_opera").hide();
			$("fPago").hide();
			$("fVenc").hide();
			$("importe").show();
			$("chq_cartera").hide();
		}

		if(seleccion == 'CH'){
			
			document.getElementById("MovimientoTipoPagoDesc").value = "CHEQUE PROPIO"
			$("cta_banco").show();
			$("nro_opera").show();
			$("fPago").show();
			$("fVenc").show();
			$("importe").show();
			$("chq_cartera").hide();

			document.getElementById("MovimientoFpagoDay").disabled = true;
			document.getElementById("MovimientoFpagoMonth").disabled = true;
			document.getElementById("MovimientoFpagoYear").disabled = true;
			
		}		
		

		if(seleccion == 'TR'){
			document.getElementById("MovimientoTipoPagoDesc").value = "TRANSFERENCIA BANCARIA"
			$("cta_banco").show();
			$("nro_opera").show();
			$("fPago").hide();
			$("fVenc").show();
			$("importe").show();
			$("chq_cartera").hide();
		}


		if(seleccion == 'DB'){
			document.getElementById("MovimientoTipoPagoDesc").value = "DEPOSITO BANCARIO"
			$("cta_banco").hide();
			$("nro_opera").show();
			$("fPago").hide();
			$("fVenc").hide();
			$("importe").show();
			$("chq_cartera").hide();
		}		
		
		if(seleccion == 'CT'){
			document.getElementById("MovimientoTipoPagoDesc").value = "CHEQUES EN CARTERA"
			$("cta_banco").hide();
			$("nro_opera").hide();
			$("fPago").hide();
			$("fVenc").hide();
			$("importe").hide();
			$("chq_cartera").show();
		}		
		
	}

</script>
	
	<?php echo $frm->create(null,array('name'=>'formDetallePago','id'=>'formDetallePago','onsubmit' => "return ctrlDetalle()", 'action' => "orden_pago/" . $proveedores['Proveedor']['id'] ));
	if($cntRenglones > 0):?>
		<table class="areaDatoForm">
			<caption>COMPROBANTES PENDIENTES DE PAGO</caption>
			<tr>
				<th>Comprobante</th>
				<th align="center">Fecha Comp.</th>
				<th align="right">Imp.Comp.</th>
				<th>Cuota</th>
				<th>Vencimiento</th>
				<th align="right">Imp.Venc.</th>
				<th align="right">Pagado</th>
				<th align="right">Saldo</th>
				<th align="right">Importe a Pagar</th>
				<th></th>
			</tr>
			<?php
				$i = 0;
			  	foreach($facturaPendiente as $fPendiente):
			  		$i++;
			?>
		  		<tr id="TRL_<?php echo $i?>">
					<td><?php echo $fPendiente['tipo_comprobante_desc'] . ' (' . $fPendiente['comentario'] . ')' ?></td>
					<td align="center"><?php echo $fPendiente['fecha_comprobante'] == '  /  /  ' ? '' : $util->armaFecha($fPendiente['fecha_comprobante'])?></td>
					<td align="right"><?php echo number_format($fPendiente['total_comprobante'],2) ?></td>
					<td align="center"><?php echo $fPendiente['cuota'] ?></td>
					<td align="center"><?php echo $fPendiente['vencimiento'] == '  /  /  ' ? '' : $util->armaFecha($fPendiente["vencimiento"])?></td>
					<td align="right"><?php echo number_format($fPendiente["importe"],2) ?></td>
					<td align="right"><?php echo number_format($fPendiente["pago"],2) ?></td>
					<td align="right"><?php echo number_format($fPendiente["saldo"],2) ?></td>
					<?php
						if($fPendiente['signo'] == '-'):
					?>
						<td align="right"><input name="data[Movimiento][detalle][importe_a_pagar][<?php echo $i ?>]" type="text" value="<?php echo number_format($fPendiente["saldo"],2,".","") ?>" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true,true)" onblur="selSum()" id="MovimientoImporteAPagar_<?php echo $i ?>" /></td>
					<?php 
						else:
					?>
						<td align="right"><input name="data[Movimiento][detalle][importe_a_pagar][<?php echo $i ?>]" type="text" value="<?php echo number_format($fPendiente["saldo"],2,".","") ?>" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" onblur="selSum()" id="MovimientoImporteAPagar_<?php echo $i ?>" /></td>
					<?php
						endif;
					?>
					<td>
						<input type="checkbox" name="data[Movimiento][detalle][check][<?php echo $i ?>]" value="<?php echo number_format(round($fPendiente["saldo"],2) * 100,0,".","")?>" id="MovimientoSaldo_<?php echo $i?>" onclick="chkOnclick()"/>
						<?php echo $frm->hidden("Movimiento.detalle.tipo.$i", array('value' => $fPendiente['tipo'])) ?>
					    <?php echo $frm->hidden("Movimiento.detalle.id.$i", array('value' => $fPendiente['id'])) ?>
					</td>
		  		</tr>
	  		<?php endforeach;?>
		
			<tr class='totales'>
				<td colspan="5"></td>
				<td colspan="3" align='right'>TOTAL DEL PAGO</td>
				<td align="right"><?php echo $frm->number('Movimiento.importe_detalle_mostrar',array('size'=>12,'maxlength'=>12, 'disabled' => 'disabled'));?></td>
				<td></td>
			</tr>
		</table>
	<?php else: ?>
		<?php echo $frm->hidden("Movimiento.importe_detalle_mostrar", array('value' => 0.00)); ?>
	<?php endif; ?>	
	<?php echo $frm->hidden("Movimiento.importe_detalle", array('value' => 0.00)); ?>
		<div  class="areaDatoForm">	

			<table class="tbl_form">
				<tr>
					<td>Forma de Pago</td>
					<td><?php echo $frm->input('Movimiento.tipo_pago',array('type' => 'select','options' => array('' => 'Seleccionar...', 'EF' => 'EFECTIVO', 'CT' => 'CHEQUES EN CARTERA', 'CH' => 'CHEQUES PROPIOS', 'TR' => 'TRANSFERENCIA BANCARIA', 'DB' => 'DEPOSITO BANCARIO'), 'onchange' => 'seleccionPago()', 'selected' => ''));?></td>
				</tr>
				<tr id="cta_banco">
					<td>Cuenta Bancaria</td>
					<td><?php echo $this->renderElement('banco_cuentas/combo_cuentas',array(
											'plugin'=>'cajabanco',
											'label' => "",
											'model' => 'Movimiento.banco_cuenta_id',
											'disabled' => false,
											'empty' => false,
											'selected' => 0))?>
					</td>			
				</tr>
				<tr id="nro_opera">
					<td>Nro.Operaci&oacute;n/Cheque</td>
					<td><?php echo $frm->input('Movimiento.numero_operacion', array('label'=>'','size'=>20,'maxlength'=>15)) ?></td>
				</tr>
				<tr id="fPago">
					<td>Fecha Pago</td>
					<td><?php echo $frm->calendar('Movimiento.fpago',null,$fechaPago,date('Y')-1,date('Y')+1)?></td>
				</tr>
				<tr id="fVenc">
					<td>Fecha Vencimiento</td>
					<td><?php echo $frm->calendar('Movimiento.fvenc',null,$fechaPago,date('Y')-1,date('Y')+1)?></td>
				</tr>
				<tr id="importe">
					<td>Importe</td>
					<td><?php echo $frm->money('Movimiento.importe_efectivo','') ?>
			 			<?php //echo $controles->btnAjax('controles/add.png','/contabilidad/asientos/cargar_renglones','grilla_renglones','formAsiento')?>
				 		<a href="<?php echo $this->base?>/proveedores/movimientos/cargar_renglones" id="link1568620940" onclick=" event.returnValue = false; return false;">
				 		<img src="<?php echo $this->base?>/img/controles/add.png" border="0" alt="" />
				 		</a>

						<script type="text/javascript">
						
							Event.observe('link1568620940', 'click', function(event)
							{ 
								$('ajax_loader_2124618328').show();
								new Ajax.Updater('grilla_pagos', '<?php echo $this->base?>/proveedores/movimientos/cargar_renglones', 
								{ 
									asynchronous:true, 
									evalScripts:true, 
                                    type: 'POST',
									onComplete:function(request, json) 
									{
//												$('ajax_loader_2124618328').hide();
//									  			acumulado = parseFloat($('acumulado').getValue());
//												pago = document.getElementById("MovimientoImportePago").value;
//									  			pago = parseFloat(pago);
//												resto = pago - acumulado;
//									  			if(resto == 0) $('btn_submit').enable();
//								  				else $('btn_submit').disable();
//								  				document.getElementById("MovimientoBancoCuentaId").value = "";
//									  			document.getElementById("MovimientoNumeroOperacion").value = "";	
//									  			document.getElementById("MovimientoImporteEfectivo").value = resto;
//									  			document.getElementById("MovimientoTipoPago").value = "";
//									  			document.getElementById("MovimientoAcumula").value = acumulado;
//									  			ocultarOptionFPago();
										actDatos();
									},
									parameters:$('formDetallePago').serialize(), 
									requestHeaders:['X-Update', 'grilla_pagos']
								})
							}, 
							false);
						</script>						

			 			<span id="ajax_loader_2124618328" style="display: none;font-size: 11px;font-style:italic;color:red;margin-left:10px;"><img src="<?php echo $this->base?>/img/controles/ajax-loader.gif" border="0" alt="" /></span>
					</td>
				</tr>

				<tr>
					<td colspan="2">
					<table id="chq_cartera">
						<tr>
							<th>#</th>
							<th>BANCO</th>
							<th>FECHA</th>
							<th>VENCIMIENTO</th>
							<th>NRO.CHEQUE</th>
							<th>LIBRADOR</th>
							<th>IMPORTE</th>
							<th></th>
							<th></th>
							
						</tr>
						<?php foreach($chqCarteras as $chqCartera):?>
							<?php $i = $chqCartera['BancoChequeTercero']['id'];?>
							<tr id="CHQ_<?php echo $i?>">
								<td align="center"><?php echo $chqCartera['BancoChequeTercero']['id']?></td>
								<td><?php echo $chqCartera['BancoChequeTercero']['banco']?></td>
								<td><?php echo $util->armaFecha($chqCartera['BancoChequeTercero']['fecha_ingreso'])?></td>
								<td><?php echo $util->armaFecha($chqCartera['BancoChequeTercero']['fecha_vencimiento'])?></td>
								<td><?php echo $chqCartera['BancoChequeTercero']['numero_cheque'] ?></td>
								<td><?php echo $chqCartera['BancoChequeTercero']['librador'] ?></td>
								<td align="right"><strong><?php echo number_format($chqCartera['BancoChequeTercero']['importe'],2)?></strong></td>
								<td><input type="checkbox" name="data[Movimiento][id_check][<?php echo $chqCartera['BancoChequeTercero']['id'] ?>]"  id="MovimientoCheck<?php echo $i?>" value='<?php echo $chqCartera['BancoChequeTercero']['importe']?>' onclick="chqOnclick('<?php echo $i?>', '<?php echo $chqCartera['BancoChequeTercero']['importe'] * (-1)?>', '<?php echo $uuid?>')"/></td>
								<td><?php echo $controles->ajaxLoader('msjAjax_' . $i,'')?></td>
							</tr>
						<?php endforeach;?>	
					</table>
					</td>
				</tr>


				<tr>
					<td colspan="2" id="grilla_pagos"></td>
				</tr>
			</table>
		</div>
		
		<div style="clear: both;"></div>
			<?php echo $frm->hidden("Movimiento.proveedor_id", array('value' => $proveedores['Proveedor']['id'])) ?>
			<?php echo $frm->hidden("Movimiento.destinatario", array('value' => $proveedores['Proveedor']['razon_social_resumida'])) ?>
		    <?php echo $frm->hidden("Movimiento.detalle_pago", array('value' => 1)) ?>
			<?php echo $frm->hidden('Movimiento.fecha_operacion', array('value' => $fechaPago)) ?>
			<?php echo $frm->hidden('Movimiento.tipo_pago_desc') ?>
			<?php echo $frm->hidden('Movimiento.acumula', array('value' => 0)) ?>
			<?php echo $frm->hidden('Movimiento.importe_cabecera', array('value' => $importePago)) ?>
			<?php echo $frm->hidden('Movimiento.importe_pago', array('value' => $importePago)) ?>
			<?php echo $frm->hidden('Movimiento.observacion', array('value' => $this->data['Movimiento']['observacion'])) ?>
			<?php echo $frm->hidden("Movimiento.formadetalle", array('value' => 1)) ?>
			<?php echo $frm->hidden("Movimiento.uuid", array('value' => $uuid)) ?>
			<?php echo $frm->btnGuardarCancelar(array('URL' => '/Proveedores/movimientos/cta_cte/' . $proveedores['Proveedor']['id']))?>
		<div style="clear: both;"></div>
<?php endif; ?>	