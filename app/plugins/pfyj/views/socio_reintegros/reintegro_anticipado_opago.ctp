<?php 
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>

<h3>REINTEGRO ANTICIPADO</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<h4>GENERAR ORDEN</h4>

<script type="text/javascript">

	Event.observe(window, 'load', function() {
		
		$('btn_submit').disable();
		ocultarOptionFPago();
	
	});


	function ocultarOptionFPago(){

		$("cta_banco").hide();
		$("nro_opera").hide();
		$("fPago").hide();
		$("fVenc").hide();
		$("importe").hide();
		
		
	}	

	
	function totalComprobante(){
//		var acumulado = parseFloat($('acumulado').getValue());
//		alert(acumulado);
		
		document.getElementById('MovimientoImportePago').value = $('MovimientoImporteDetalle').getValue();
		document.getElementById('MovimientoImporteEfectivo').value = $('MovimientoImporteDetalle').getValue();
		document.getElementById("MovimientoImporteDetalle").value = $('MovimientoImporteDetalle').getValue();
		
		actualizaImporte(0);
	}		


	function seleccionPago(){
		var seleccion = $('MovimientoTipoPago').getValue();

		document.getElementById("MovimientoFpagoDay").value = document.getElementById("MovimientoFechaPagoDay").value;
		document.getElementById("MovimientoFpagoMonth").value = document.getElementById("MovimientoFechaPagoMonth").value;
		document.getElementById("MovimientoFpagoYear").value = document.getElementById("MovimientoFechaPagoYear").value;

		document.getElementById("MovimientoFvencDay").value = document.getElementById("MovimientoFechaPagoDay").value;
		document.getElementById("MovimientoFvencMonth").value = document.getElementById("MovimientoFechaPagoMonth").value;
		document.getElementById("MovimientoFvencYear").value = document.getElementById("MovimientoFechaPagoYear").value;
		
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
		}


		if(seleccion == 'DB'){
			document.getElementById("MovimientoTipoPagoDesc").value = "DEBITO BANCARIO"
			$("cta_banco").hide();
			$("nro_opera").show();
			$("fPago").hide();
			$("fVenc").hide();
			$("importe").show();
		}		
		
	}

</script>

<div class="areaDatoForm">
	
	<?php echo $frm->create(null,array('name'=>'formDetallePago','id'=>'formDetallePago','action' => 'reintegro_anticipado_opago/'.$socio['Socio']['id']))?>

	<table class="tbl_form">
	
		<tr>
			<td>LIQUIDACION</td>
			<td><?php echo $frm->input('SocioReintegro.liquidacion_id',array('type' => 'select','options' => $liquidaciones,'label' => null))?></td>
		</tr>
		<tr>
			<td>Fecha del Pago:</td>
			<td><?php echo $frm->calendar('Movimiento.fecha_pago',null,$fechaPago,date('Y')-1,date('Y')+1)?></td>
		</tr>
		<tr>
			<td>Importe Reintegro:</td>
			<td><div class="input text"><label for="MovimientoImporteDetalle"></label><input name="data[Movimiento][importe_detalle]" type="text" value="" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true,false)" onblur="totalComprobante()" id="MovimientoImporteDetalle" /></div></td>
		</tr>
		<tr>
			<td>Observaci&oacute;n</td>
			<td><?php echo $frm->input('Movimiento.observacion', array('label'=>'','value' => 'REINTEGRO ANTICIPADO A SOCIO','size'=>60,'maxlength'=>50)) ?></td>
		</tr>
		<tr>
			<td>Forma de Pago</td>
			<td><?php echo $frm->input('Movimiento.tipo_pago',array('type' => 'select','options' => array('' => 'Seleccionar...', 'EF' => 'EFECTIVO', 'CH' => 'CHEQUES PROPIOS', 'TR' => 'TRANSFERENCIA BANCARIA', 'DB' => 'DEPOSITO BANCARIO'), 'onchange' => 'seleccionPago()', 'selected' => ''));?></td>
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
								$('ajax_loader_2124618328').hide();
					  			acumulado = parseFloat($('acumulado').getValue());
								pago = document.getElementById("MovimientoImportePago").value;
					  			pago = parseFloat(pago);
								resto = pago - acumulado;
					  			if(resto == 0) $('btn_submit').enable();
				  				else $('btn_submit').disable();
				  				document.getElementById("MovimientoBancoCuentaId").value = "";
					  			document.getElementById("MovimientoNumeroOperacion").value = "";	
					  			document.getElementById("MovimientoImporteEfectivo").value = resto;
					  			document.getElementById("MovimientoTipoPago").value = "";
					  			ocultarOptionFPago();
							},
							parameters:$('formDetallePago').serialize(), 
							requestHeaders:['X-Update', 'grilla_pagos']
						})
					}, false);
	 				function actualizaImporte(valor){
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
			<td colspan="2" id="grilla_pagos"></td>
		</tr>
	</table>
	<?php echo $frm->hidden('SocioReintegro.id',array('value' => 0))?>
	<?php echo $frm->hidden('SocioReintegroPago.id',array('value' => 0))?>
	<?php echo $frm->hidden('SocioReintegro.socio_id',array('value' => $socio['Socio']['id']))?>
	<?php echo $frm->hidden('SocioReintegroPago.socio_id',array('value' => $socio['Socio']['id']))?>
	<?php echo $frm->hidden('SocioReintegro.anticipado',array('value' => 1))?>
	<?php echo $frm->hidden('Movimiento.socio_id',array('value' => $socio['Socio']['id']))?>
	<?php echo $frm->hidden("Movimiento.destinatario", array('value' => rtrim($socio['Persona']['apellido']) . ', ' . ltrim(rtrim($socio['Persona']['nombre'])))) ?>
	<?php echo $frm->hidden('Movimiento.tipo_pago_desc') ?>
	<?php echo $frm->hidden('Movimiento.importe_pago') ?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GENERAR ORDEN DE REINTEGRO ANTICIPADO','URL' => ( empty($fwrd) ? "/pfyj/socio_reintegros/by_socio/".$socio['Socio']['id'] : $fwrd) ))?>

</div>

