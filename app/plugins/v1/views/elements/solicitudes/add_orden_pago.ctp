<h1>SOLICITUD DE CREDITO Nro. <?php echo $nro_solicitud?> :: (<?php echo $solicitud['Solicitud']['estado_descripcion']?>)</h1>
<hr>
<?php echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'link' => false,'plugin' => 'pfyj'))?>

<h3 style="border-bottom: 1px solid;">BENEFICIO POR EL CUAL SE DESCUENTA</h3>
<?php echo $solicitud['Beneficio']['string']?>

<h3 style="border-bottom: 1px solid;">LIQUIDACION Y PAGO DEL PRESTAMO</h3>
<?php echo $this->renderElement('solicitudes/grilla_liquidacion', array('solicitud' => $solicitud, 'plugin' => 'v1'))?>

<?php if(count($solicitud['SolicitudCancelacionOrden'])!=0):?>

	<h3 style="border-bottom: 1px solid;">ORDENES DE CANCELACION</h3>
		<?php echo $this->renderElement('solicitud_cancelaciones/ordenes_cancelacion_info_pago', array('cancelaciones' => $solicitud['SolicitudCancelacionOrden'],'persona_id' => $persona['Persona']['id'], 'plugin' => 'v1'))?>
	
<?php elseif(count($solicitud['Cancelaciones'])!=0):?>
	<h3 style="border-bottom: 1px solid;">CANCELACIONES</h3>
		<?php echo $this->renderElement('solicitud_cancelaciones/cancelaciones_info_pago', array('cancelaciones' => $solicitud['Cancelaciones'],'persona_id' => $persona['Persona']['id'], 'plugin' => 'v1'))?>

<?php endif;?>


<?php 
	$parametros = $solicitud['Solicitud']['nro_solicitud'];
	if(isset($persona_id)) $parametros .= '/' . $persona_id;
?>

<h3 style="border-bottom: 1px solid;">ORDEN DE PAGO</h3>
	<?php echo $frm->create(null,array('name'=>'formDetallePago','id'=>'formDetallePago', 'action' => 'addOrdenPago/' . $parametros))?>
	<?php 
		$fecha_pago = date('dmY');
		if(!empty($solicitud['Solicitud']['fecha_operacion_pago'])):
			$fecha_pago = $solicitud['Solicitud']['fecha_operacion_pago'];
		elseif(!empty($solicitud['Solicitud']['fecha_estado'])):
			$fecha_pago = $solicitud['Solicitud']['fecha_estado'];
		endif;
		
		
		$importePago = round($solicitud['Solicitud']['en_mano'],2);
		if($solicitud['Solicitud']['comision_instruccion_pago_calculo'] == 2) $importePago -= $solicitud['Solicitud']['monto_instruccion_pago'];
//		$importePago = round($solicitud['Solicitud']['monto_a_percibir'],2);
	?>
		<script language="Javascript" type="text/javascript">
			var fechaPago = <?php echo $fecha_pago ?>;
			var importeTotal = <?php echo  round($importePago,2) ?>;
			var rows = <?php echo $rows?>;
			var acumulado = 0;
			
			Event.observe(window, 'load', function() {
				document.getElementById("MovimientoImporteEfectivo").value = importeTotal;
				$('btn_submit').disable();
				ocultarOptionFPago();
				totalOPago();
			
			});
		
		
			function ocultarOptionFPago(){
		
				$("cta_banco").hide();
				$("nro_opera").hide();
				$("fPago").hide();
				$("fVenc").hide();
				$("importe").hide();
				$("chq_cartera").hide();
				
				
			}	
		

			function seleccionPago(){
				var seleccion = $('MovimientoTipoPago').getValue();

				$("MovimientoFpagoYear").value = $('MovimientoFechaPagoYear').getValue();
				$("MovimientoFpagoMonth").value = $('MovimientoFechaPagoMonth').getValue();
				$("MovimientoFpagoDay").value = $('MovimientoFechaPagoDay').getValue();
				
				$("MovimientoFvencYear").value = $('MovimientoFechaPagoYear').getValue();
				$("MovimientoFvencMonth").value = $('MovimientoFechaPagoMonth').getValue();
				$("MovimientoFvencDay").value = $('MovimientoFechaPagoDay').getValue();
		
				ocultarOptionFPago();
		
				if(seleccion == 'EF'){
					$("MovimientoTipoPagoDesc").value = "EFECTIVO"
					$("cta_banco").hide();
					$("nro_opera").hide();
					$("fPago").hide();
					$("fVenc").hide();
					$("importe").show();
				}
		
				if(seleccion == 'CH'){
					
					$("MovimientoTipoPagoDesc").value = "CHEQUE PROPIO"
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
					$("MovimientoTipoPagoDesc").value = "TRANSFERENCIA BANCARIA"
					$("cta_banco").show();
					$("nro_opera").show();
					$("fPago").hide();
					$("fVenc").show();
					$("importe").show();
				}
		
		
				if(seleccion == 'DB'){
					$("MovimientoTipoPagoDesc").value = "DEBITO BANCARIO"
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
			

			function totalOPago(){
				var impoCancela = new Number($('MovimientoImporteCancela').getValue());
				var valor = importeTotal - impoCancela;
				var importe = new Number(valor);
				var acumulado = new Number($F('MovimientoAcumula'));
				
				valor = valor - acumulado
				$('MovimientoImportePago').value = importe.toFixed(2);
				$('MovimientoImporteEfectivo').value =  valor.toFixed(2);


				actualizaImporte(0,0);
			}		


			function chkOnclick(){
				selSum();

				totalOPago();

				acumulado = parseFloat($('acumulado').getValue());
				cobro = $("MovimientoImportePago").getValue();
				cobro = parseFloat(cobro);
				resto = cobro - acumulado;
				
				if(resto == 0) $('btn_submit').enable();
				else $('btn_submit').disable();
				
				$("MovimientoImporteEfectivo").value = resto;
				$("MovimientoTipoPago").value = "";
				ocultarOptionFPago();
				
			}	

			function selSum(){	
				var totalSeleccionado = 0;
				var error = "";	
				var chequeado = 0;

				ocultarOptionFPago();
				
				for (i=1;i<=rows;i++){
					var celdas = $('TRL_' + i).immediateDescendants();
					oChkCheck = document.getElementById('CancelacionOrdenSaldo_' + i);
					if (oChkCheck.checked){
						chequeado = 1;
						
						strChkTxt = oChkCheck.value;
						numChkTxt = new Number(strChkTxt);
						numChkTxt = numChkTxt / 100;
						numChkTxt = numChkTxt.toFixed(2);

						chkValue = parseInt(oChkCheck.value);
						totalSeleccionado = totalSeleccionado + chkValue;
			
						celdas.each(function(td){td.addClassName("selected");});
					}else{
						celdas.each(function(td){td.removeClassName("selected");});
					}	
				}
				totalSeleccionado = totalSeleccionado/100;
				
				$('MovimientoImportePago').value = totalSeleccionado;
				$('MovimientoImporteEfectivo').value = totalSeleccionado;
				
				importePago = $('MovimientoImportePago').getValue();
				totalSeleccionado = FormatCurrency(totalSeleccionado);
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
				var fecha_operacion = $F('MovimientoFechaPagoYear') + '-' + $F('MovimientoFechaPagoMonth') + '-' + $F('MovimientoFechaPagoDay');
				var importe_detalle = 0;
				var importe_pago = $('MovimientoImportePago').getValue();
				var clave = 'id_persona';
				var valor = $('MovimientoIdPersona').getValue();

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
				var importe, pago;
				
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
		</script>
	

		<div  class="areaDatoForm">	
	<?php if(count($cancelaciones) > 0): ?>
		<h4>DETALLE DE ORDENES DE CANCELACION EMITIDAS</h4>
		<table>
			<tr>
				<th>#</th>
				<th>TIPO</th>
				<th>ORDEN</th>
				<th>TIPO / NUMERO</th>
				<th>PROVEEDOR / PRODUCTO</th>
				<th>A LA ORDEN DE</th>
				<th>DEUDA PROVEEDOR</th>
				<th>SALDO ORDEN DTO</th>
				<th>IMPORTE SELECCIONADO</th>
				<th>DEBITO/CREDITO</th>
				<th>VENCIMIENTO</th>
				<th></th>
				
			</tr>
			<?php $i=0; // $nRespon = $cancelaciones[0]['CancelacionOrden']['origen_proveedor_id'];?>
			<?php foreach($cancelaciones as $cancelacion):
				if($cancelacion['CancelacionOrden']['orden_proveedor_id'] == $solicitud['Producto']['Proveedor']['idr']):
				$i++;
			?>
				<tr id="TRL_<?php echo $i?>">
					<td><strong><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cancelacion['CancelacionOrden']['id'],'h' => 450, 'w' => 750))?></strong></td>
					<td align="center"><?php echo $cancelacion['CancelacionOrden']['tipo_cancelacion_desc']?></td>
					<td align="center"><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cancelacion['CancelacionOrden']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cancelacion['CancelacionOrden']['orden_descuento_id'].'/'.$cancelacion['CancelacionOrden']['socio_id'],'h' => 450, 'w' => 750))?></td>
					<td><?php echo $cancelacion['CancelacionOrden']['tipo_nro_odto']?></td>
					<td><?php echo $cancelacion['CancelacionOrden']['proveedor_producto_odto']?></td>
					<td><?php echo $cancelacion['CancelacionOrden']['a_la_orden_de']?></td>
					<td align="right"><strong><?php echo number_format($cancelacion['CancelacionOrden']['importe_proveedor'],2)?></strong></td>
					<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['saldo_orden_dto'],2)?></td>
					<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['importe_seleccionado'],2)?></td>
					<td align="right">
						<?php
							if(!empty($cancelacion['CancelacionOrden']['tipo_cuota_diferencia'])){
								echo $this->requestAction('/config/global_datos/valor/' . $cancelacion['CancelacionOrden']['tipo_cuota_diferencia']);
								echo "&nbsp;= \$";
								echo number_format($cancelacion['CancelacionOrden']['importe_diferencia'],2);
							}
						?>
					</td>
					<td align="center"><strong><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto'])?></strong></td>
<!-- 					<td><input type="checkbox" name="data[CancelacionOrden][id_check][<?php echo $cancelacion['CancelacionOrden']['id'] ?>]" value="<?php echo number_format(round($cancelacion['CancelacionOrden']['importe_proveedor'],2) * -100,0,".","")?>" id="CancelacionOrdenSaldo_<?php echo $i ?>" onclick="toggleCell('TRL_<?php echo $i?>',this); chkOnclick()"/></td> -->
				</tr>
				<?php endif;?>
			<?php endforeach;?>	
		</table>
	<?php endif; ?>

		
			<table class="tbl_form">
				<tr>
					<td>FECHA DE PAGO:</td>
					<td><?php echo $frm->calendar('Movimiento.fecha_pago',null, $fecha_pago,date('Y')-1,date('Y')+1)?></td>
				</tr>
				<tr>
					<td>CANCELACION EXTERNA:</td>
					<td><div class="input text"><label for="MovimientoImporteCancela"></label><input name="data[Movimiento][importe_cancela]" type="text" value=<?php echo $solicitud['Solicitud']['total_cancelado'] ?> size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true,false)" onblur="totalOPago()" id="MovimientoImporteCancela" /></div></td>
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
					<td><?php echo $frm->calendar('Movimiento.fpago',null,$solicitud['Solicitud']['fecha_operacion_pago'],date('Y')-1,date('Y')+1)?></td>
				</tr>
				<tr id="fVenc">
					<td>Fecha Vencimiento</td>
					<td><?php echo $frm->calendar('Movimiento.fvenc',null,$solicitud['Solicitud']['fecha_operacion_pago'],date('Y')-1,date('Y')+1)?></td>
				</tr>
				<tr id="importe">
					<td>Importe</td>
					<td><?php echo $frm->money('Movimiento.importe_efectivo','') ?>
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
//						  				acumulado = parseFloat($('acumulado').getValue());
//										pago = document.getElementById("MovimientoImportePago").value;
//							  			pago = parseFloat(pago);
//										resto = pago - acumulado;
//							  			if(resto == 0) $('btn_submit').enable();
//						  				else $('btn_submit').disable();
//						  				document.getElementById("MovimientoBancoCuentaId").value = "";
//							  			document.getElementById("MovimientoNumeroOperacion").value = "";	
//							  			document.getElementById("MovimientoImporteEfectivo").value = resto;
//							  			document.getElementById("MovimientoTipoPago").value = "";
//						  				ocultarOptionFPago();

										actDatos();
						  				
									},
									parameters:$('formDetallePago').serialize(), 
									requestHeaders:['X-Update', 'grilla_pagos']
								})
							}, false);
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
			<?php echo $frm->hidden("Movimiento.id_persona", array('value' => $solicitud['Solicitud']['id_persona'])) ?>
			<?php echo $frm->hidden("Movimiento.destinatario", array('value' => $solicitud['PersonaV1']['apellido'] . ' ' . $solicitud['PersonaV1']['nombre'])) ?>
			<?php // echo $frm->hidden('Movimiento.fecha_operacion', array('value' => $solicitud['Solicitud']['fecha_operacion_pago'])) ?>
			<?php echo $frm->hidden('Movimiento.tipo_pago_desc') ?>
			<?php echo $frm->hidden('Movimiento.acumula', array('value' => 0)) ?>
			<?php echo $frm->hidden('Movimiento.importe_pago', array('value' => 0)) ?>
			<?php echo $frm->hidden("Movimiento.uuid", array('value' => $uuid)) ?>
			<?php // echo $frm->btnGuardarCancelar(array('URL' => '/Proveedores/movimientos/cta_cte/' . $proveedores['Proveedor']['id']))?>
		<div style="clear: both;"></div>
		
	<?php echo $frm->hidden('Solicitud.id',array('value' => $solicitud['Solicitud']['nro_solicitud']))?>
	<?php echo $frm->hidden('Solicitud.aprobar',array('value' => 1))?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GENERAR O.PAGO','URL' => $regresar ))?>
