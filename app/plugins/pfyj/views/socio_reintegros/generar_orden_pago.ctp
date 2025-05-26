<?php if($menuPersonas == 1): echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'))?>
<?php else: echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'))?>
<?php endif;?>
<h3>ABONAR REINTEGRO AL SOCIO</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<div class="actions"><?php echo $controles->btnRew('Regresar','by_socio/'.$socio['Socio']['id'])?></div>
<?php if(!empty($reintegros)):
	$fechaPago = date('Ymd');
	?>


<script language="Javascript" type="text/javascript">
	var rows = <?php echo count($reintegros)?>;
	var fechaPago = <?php echo $fechaPago?>;
	Event.observe(window, 'load', function() {
		
		$('btn_submit').disable();
		selSum();
		ocultarOptionFPago();
	
	});


	function ocultarOptionFPago(){

		$("cta_banco").hide();
		$("nro_opera").hide();
		$("fPago").hide();
		$("fVenc").hide();
		$("importe").hide();
		$("chq_cartera").hide();
		
		
	}	

	
	function chkOnclickOld(){
		selSum();
		if(document.getElementById("acumulado") != null) acumulado = parseFloat($('acumulado').getValue());
		else acumulado = 0;
		pago = document.getElementById("MovimientoImportePago").value;
		pago = parseFloat(pago);
		resto = pago - acumulado;
		if(resto == 0) $('btn_submit').enable();
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
		
		$("MovimientoImporteEfectivo").value = resto.toFixed(2);
		$("MovimientoTipoPago").value = "";
		ocultarOptionFPago();
		
	}	


	function selSum(){
	
		var totalSeleccionado = new Number(0);

		for (i=1;i<=rows;i++){
			
			var celdas = $('TRL_' + i).immediateDescendants();
			
			oChkCheck = document.getElementById('SocioReintegroId_' + i);

			toggleCell('TRL_' + i,oChkCheck);
		
			if (oChkCheck.checked){
//				numValTxt = new Number(oChkCheck.value);
//				impoTxt = numValTxt.toFixed(0);

				numValTxtCtrl = new Number(oChkCheck.value);
				impoTxtCtrl = numValTxtCtrl.toFixed(0) / 100;
				
				numValTxt = new Number(document.getElementById('SocioReintegroPago_' + i).value);
				impoTxt = numValTxt.toFixed(2);
				if(impoTxt > impoTxtCtrl){
					alert("EL IMPORTE DEL PAGO " + impoTxt + " NO PUEDE SER SUPERIOR AL TOTAL DEL REINTEGRO ("+ impoTxtCtrl +")!");
					$('SocioReintegroId1_' + i).focus();
					oChkCheck.checked = false;
					toggleCell('TRL_' + i,oChkCheck);
					break;					
				}else{
//					totalSeleccionado = totalSeleccionado + parseInt(impoTxt);
					totalSeleccionado = totalSeleccionado + parseFloat(impoTxt);
				}
			}	
		}

//		totSel = totalSeleccionado/100;
		totSel = totalSeleccionado;
		totSel = new Number(totSel);
		totSel = totSel.toFixed(2);
		document.getElementById('MovimientoImporteEfectivo').value = totSel;
		document.getElementById("MovimientoImporteDetalle").value = totSel;
		document.getElementById('MovimientoImportePago').value = totSel;
		
//		totSel = FormatCurrency(totSel);
		document.getElementById("MovimientoImporteDetalleMostrar").value = totSel;
		document.getElementById("MovimientoImporteDetalleMostrar").disabled = true;

	}


	function seleccionPago(){
		var seleccion = $('MovimientoTipoPago').getValue();

		$("MovimientoFpagoDay").value = $F("MovimientoFechaPagoDay");
		$("MovimientoFpagoMonth").value = $F("MovimientoFechaPagoMonth");
		$("MovimientoFpagoYear").value = $F("MovimientoFechaPagoYear").value;

		$("MovimientoFvencDay").value = $F("MovimientoFechaPagoDay");
		$("MovimientoFvencMonth").value = $F("MovimientoFechaPagoMonth");
		$("MovimientoFvencYear").value = $F("MovimientoFechaPagoYear");
		
		ocultarOptionFPago();

		if(seleccion == 'EF'){
			document.getElementById("MovimientoTipoPagoDesc").value = "EFECTIVO"
			$("cta_banco").hide();
			$("nro_opera").hide();
			$("fPago").hide();
			$("fVenc").hide();
			$("importe").show();
		}

		if(seleccion == 'CH'){
			
			document.getElementById("MovimientoTipoPagoDesc").value = "CHEQUE PROPIO"
			$("cta_banco").show();
			$("nro_opera").show();
			$("fPago").show();
			$("fVenc").show();
			$("importe").show();

			$("MovimientoFpagoDay").disabled = true;
			$("MovimientoFpagoMonth").disabled = true;
			$("MovimientoFpagoYear").disabled = true;
			
		}		
		

		if(seleccion == 'TR'){
			document.getElementById("MovimientoTipoPagoDesc").value = "TRANSFERENCIA BANCARIA"
			$("cta_banco").show();
			$("nro_opera").show();
			$("fPago").hide();
			$("fVenc").show();
			$("importe").show();
		}


		if(seleccion == 'DB'){
			document.getElementById("MovimientoTipoPagoDesc").value = "DEBITO BANCARIO"
			$("cta_banco").hide();
			$("nro_opera").show();
			$("fPago").hide();
			$("fVenc").hide();
			$("importe").show();
		}		
		
		
		if(seleccion == 'CT'){
			$("MovimientoTipoPagoDesc").value = "CHEQUES EN CARTERA"
			$("cta_banco").hide();
			$("nro_opera").hide();
			$("fPago").hide();
			$("fVenc").hide();
			$("importe").hide();
			$("chq_cartera").show();
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
		$("MovimientoImporteEfectivo").value = v3
		$("MovimientoTipoPago").value = "";
		ocultarOptionFPago();
	}


	function actDatos(){
		var importe, pago, acumulado;
		
		$('ajax_loader_2124618328').hide();
		
		$("MovimientoBancoCuentaId").value = "";
		$("MovimientoNumeroOperacion").value = "";	
		$("MovimientoTipoPago").value = "";
		
		importe = new Number($F('MovimientoImporteEfectivo'));
		acumulado = new Number($('MovimientoAcumula').getValue());
		pago = new Number($('MovimientoImportePago').getValue());
		
		acumulado = pago - acumulado
		
		$('MovimientoImporteEfectivo').value = acumulado.toFixed(2);
		if(acumulado.toFixed(2) >= importe){
			actualizaImporte(-importe, 0);
		}		
		else{
			actualizaImporte(0, 0);		
		}
		
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
		fecha_operacion = $F('MovimientoFechaPagoYear') + '-' + $F('MovimientoFechaPagoMonth') + '-' + $F('MovimientoFechaPagoDay');
		importe_detalle = $('MovimientoImporteDetalle').getValue();
		importe_pago = $('MovimientoImportePago').getValue();
		clave = 'socio_id';
		valor = <?php echo $socio['Socio']['id']?>;
		
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
			impCheque = impCheque * (-1)
			actualizaImporte(impCheque, 0);
		}

		ocultarOptionFPago();
	}


</script>

<?php echo $frm->create(null,array('name'=>'formDetallePago','onsubmit' => "$('btn_submit').disable();",'id'=>'formDetallePago','action' => 'generar_orden_pago/'.$socio['Socio']['id']))?>
	<h4>GENERAR ORDEN DE PAGO</h4>
	<table>
		<tr>
			<th>#</th>
			<th>TIPO</th>
			<th>LIQUIDACION</th>
			<th>DEBITADO</th>
			<th>IMPUTADO</th>
			<th>REINTEGRO</th>
			<th>APLICADO</th>
			<th>PAGADO</th>
			<th>SALDO</th>
			<th>PAGO</th>
			<th></th>
		</tr>
		<?php $i = $ACU_SALDO = $ACU_PAGOS = 0;?>
		<?php foreach($reintegros as $reintegro):?>
			<?php $i++;?>
			<?php $ACU_SALDO += $reintegro['SocioReintegro']['saldo']?>
			<?php $ACU_PAGOS += $reintegro['SocioReintegro']['pagos']?>
			<tr id="TRL_<?php echo $i?>">
				<td><?php echo $reintegro['SocioReintegro']['id']?></td>
				<td><strong><?php echo $reintegro['SocioReintegro']['tipo']?></strong></td>
				<td><?php echo $reintegro['SocioReintegro']['liquidacion_str']?></td>
				<td align="right"><?php echo $util->nf($reintegro['SocioReintegro']['importe_debitado'])?></td>
				<td align="right"><?php echo $util->nf($reintegro['SocioReintegro']['importe_imputado'])?></td>
				<td align="right"><?php echo $util->nf($reintegro['SocioReintegro']['importe_reintegro'])?></td>
				<td align="right"><?php echo $util->nf($reintegro['SocioReintegro']['importe_aplicado'])?></td>
				<td align="right"><?php echo $util->nf($reintegro['SocioReintegro']['pagos'])?></td>
				<td align="right"><strong><?php echo $util->nf($reintegro['SocioReintegro']['saldo'])?></strong></td>
				<td align="right"><input type="text" name="data[SocioReintegro][pago][<?php echo $reintegro['SocioReintegro']['id']?>]" value="<?php echo number_format($reintegro['SocioReintegro']['saldo'],2,".","")?>" id="SocioReintegroPago_<?php echo $i?>" class="input_number" onkeypress = "return soloNumeros(event,true)" size="12" maxlength="12"/></td>
				<td align="center">
				<input type="checkbox" name="data[SocioReintegro][id][<?php echo $reintegro['SocioReintegro']['id']?>]" value="<?php echo number_format($reintegro['SocioReintegro']['saldo'] * 100,0,".","")?>" id="SocioReintegroId_<?php echo $i?>" onclick="chkOnclick()"/>
				</td>
			</tr>
		<?php endforeach;?>
		<tr class='totales'>
			<th colspan="7" align='right'>TOTAL A ABONAR AL SOCIO</th>
			<th align="right"><?php //   echo $util->nf($ACU_PAGOS);?></th>
			<th align="right"><?php echo $util->nf($ACU_SALDO);?></th>
			<th align="right"><?php echo $frm->number('Movimiento.importe_detalle_mostrar',array('size'=>12,'maxlength'=>12, 'disabled' => 'disabled'));?></th>
			<th></th>
		</tr>
	</table>
	
	
	<?php echo $frm->hidden("Movimiento.importe_detalle", array('value' => 0.00)); ?>
		<div  class="areaDatoForm">	
		
			<table class="tbl_form">
				<tr>
					<td>Fecha del Pago:</td>
					<td><?php echo $frm->calendar('Movimiento.fecha_pago',null,$fechaPago,date('Y')-1,date('Y')+1)?></td>
				</tr>
				<tr>
					<td>Observaci&oacute;n</td>
					<td><?php echo $frm->input('Movimiento.observacion', array('label'=>'','value' => 'REINTEGRO A SOCIO','size'=>60,'maxlength'=>50)) ?></td>
				</tr>
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
									asynchronous:true, evalScripts:true, onComplete:function(request, json) 
									{
//										$('ajax_loader_2124618328').hide();
//							  			acumulado = parseFloat($('acumulado').getValue());
//										pago = document.getElementById("MovimientoImportePago").value;
//							  			pago = parseFloat(pago);
//										resto = pago - acumulado;
//							  			if(resto == 0) $('btn_submit').enable();
//						  				else $('btn_submit').disable();
//							  			resto = new Number(resto);
//							  			resto = resto.toFixed(2);
//						  				document.getElementById("MovimientoBancoCuentaId").value = "";
//							  			document.getElementById("MovimientoNumeroOperacion").value = "";	
//							  			document.getElementById("MovimientoImporteEfectivo").value = resto;
//							  			document.getElementById("MovimientoTipoPago").value = "";
//							  			ocultarOptionFPago();
							  			actDatos();
									},
									parameters:$('formDetallePago').serialize(), 
									requestHeaders:['X-Update', 'grilla_pagos']
								})
							}, false);
			 				function actualizaImporteAnt(valor){
				 				v1 = valor;
				 				v2 = document.getElementById("MovimientoImporteEfectivo").value;
				 				v1 = new Number(v1);
				 				v2 = new Number(v2);
				 				document.getElementById("MovimientoImporteEfectivo").value = v1 + v2;
					  			document.getElementById("MovimientoTipoPago").value = "";
					  			ocultarOptionFPago();
				 			}
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
			<?php echo $frm->hidden("Movimiento.socio_id", array('value' => $socio['Socio']['id'])) ?>
			<?php echo $frm->hidden("Movimiento.destinatario", array('value' => rtrim($socio['Persona']['apellido']) . ', ' . ltrim(rtrim($socio['Persona']['nombre'])))) ?>
			<?php echo $frm->hidden('Movimiento.tipo_pago_desc') ?>
			<?php echo $frm->hidden('Movimiento.importe_pago') ?>
			<?php echo $frm->hidden('Movimiento.acumula', array('value' => 0)) ?>
			<?php echo $frm->hidden("Movimiento.uuid", array('value' => $uuid)) ?>
			<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GENERAR ORDEN DE PAGO','URL' => '/pfyj/socio_reintegros/by_socio/' . $socio['Socio']['id']))?>
		<div style="clear: both;"></div>
	




<?php else:?>
	<h4>SIN REINTEGROS PENDIENTES DE PROCESAR</h4>
<?php endif;?>

<?php // debug($reintegros)?>