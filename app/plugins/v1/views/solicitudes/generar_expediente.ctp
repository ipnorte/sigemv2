
<?php $organismo = substr(trim($solicitud['Beneficio']['codigo_beneficio']),8,2);?>
<?php $beneficios = (!empty($persona) ? $this->requestAction('/pfyj/persona_beneficios/beneficios_by_persona/'.$persona['Persona']['id'].'/1') : null);?>

<h1>GENERAR ORDEN DE DESCUENTO :: SOLICITUD Nro. <?php echo $nro_solicitud?></h1>
<hr>


<?php  if(!empty($persona)) echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'link' => true,'plugin' => 'pfyj'))?>


<?php if(!empty($persona['Socio']['id'])):?>
<?php //   echo $this->requestAction('/pfyj/socios/view/'.$persona['Socio']['id'])?>
<?php else:?>
<div class="notices">PERSONA NO REGISTRADA COMO SOCIO :: <strong>ALTA SOCIO NUEVO</strong></div>
<?php endif;?>

<?php echo $form->create(null,array('onsubmit' => "return validateFormAprobar();",'action' => 'generar_expediente/'. $nro_solicitud));?>
<div class="areaDatoForm2">
	Historial de la Solicitud Nro: <strong><?php echo $nro_solicitud?></strong>
	<?php echo $controles->btnModalBox(array('title' => 'HISTORIAL DE LA SOLICITUD Nro.'.$nro_solicitud,'url' => '/v1/solicitud_estados/historial/'.$nro_solicitud,'h' => 500, 'w' => 750,'img'=>'calendar.png'))?>
</div>

<h3 style="border-bottom: 1px solid;">BENEFICIO POR EL CUAL SE DESCUENTA</h3>

<script type="text/javascript">

function validateFormAprobar(){
	var sumbit = false;
	var msg = "";

	var beneficioStr = getTextoSelect("SolicitudPersonaBeneficioIdV2");

	var cancelaciones = "<?php echo (count($solicitud['Cancelaciones'])!=0 ? 1 : 0)?>";
	if(cancelaciones != 0){
		//SolicitudTotalOrdenesCancelacionEmitidas
		emitidas = document.getElementById("SolicitudTotalOrdenesCancelacionEmitidas");
		if(emitidas == null){

			return confirm("*** ATENCION ***\n\nLA SOLICITUD <?php echo $nro_solicitud?> TIENE ORDENES DE CANCELACION\nPERO NO EXISTE NINGUNA EMITIDA.\n\nAprobar esta solicitud?");

		}else{

			noChequeadasStr = "";
			chequeadasStr = "";
			i = 1;
			for (i; i <= emitidas.value; i++){
				oCHK = document.getElementById("SolicitudCancelacionOrdenId_" + i);
				oLBL = document.getElementById("SolicitudCancelacionLabel_" + i);
				if(!oCHK.checked) noChequeadasStr = noChequeadasStr + oLBL.value + "\n";
				else chequeadasStr = chequeadasStr + oLBL.value + "\n";
			}
			if(noChequeadasStr != ""){
				msg = "*** ATENCION ***\n\nNO HA SELECCIONADO LAS SIGUIENTES ORDENES DE CANCELACION EMITIDAS PENDIENTES:\n\n";
				msg = msg + noChequeadasStr;
				msg = msg + "\n";
				msg = msg + "Esto es correcto?";
				sumbit = confirm(msg);
			}else{
				sumbit = true;
			}

		}

	}else{

		msg = "*** ATENCION ***\nAPROBAR LA SOLICITUD #<?php echo $nro_solicitud?>?";
		msg = msg + "\n\nBENEFICIO: " + beneficioStr + "\n";
		return confirm(msg);

	}
	if(!sumbit)return sumbit;
	msg = "*** ATENCION ***\nAPROBAR LA SOLICITUD #<?php echo $nro_solicitud?>?";
	msg = msg + "\n\nBENEFICIO: " + beneficioStr + "\n";

	if(chequeadasStr != ""){
		msg = msg + "\n\nAPROBAR LAS SIGUIENTES ORDENES DE CANCELACION?\n" + chequeadasStr + "\n";
	}
	return confirm(msg);

}

function validateFormBeneficio(){

	var ret = true;
	var cbu = $('PersonaBeneficioCbu').getValue();
	var cbu_bco = cbu.substring(0,3);
	if(cbu != ''){
		var bancos = [<?php echo $bcos_hab?>];
		var ret = validarCBU('PersonaBeneficioCbu','CBU incorrecto',0,'mensaje_error_js',0);
		if(!ret){
			alert("El Nro de CBU es incorrecto!");
			return false;
		}
		var ctrlBanco = false;
		for (var index = 0; index < bancos.length; ++index) {
			var item = bancos[index];
			if(item == cbu_bco){
				ctrlBanco = true;
				break;
			}
		}

		if(ret && ctrlBanco) ret = true;
		else ret = false;

	}
	return ret;
}


Event.observe(window, 'load', function() {

	<?php if(empty($beneficios)):?>
		$('btn_submit').disable();
	<?php endif;?>

	<?php if(!empty($solicitud['Solicitud']['fecha_operacion_pago'])):?>
		document.getElementById("SolicitudFechaOperacionPagoDay").value = "<?php echo date("d",strtotime($solicitud['Solicitud']['fecha_operacion_pago']))?>";
		document.getElementById("SolicitudFechaOperacionPagoMonth").value = "<?php echo date("m",strtotime($solicitud['Solicitud']['fecha_operacion_pago']))?>";
		document.getElementById("SolicitudFechaOperacionPagoYear").value = "<?php echo date("Y",strtotime($solicitud['Solicitud']['fecha_operacion_pago']))?>";
	<?php endif;?>

	var proveedor = "<?php echo $solicitud['Solicitud']['proveedor_id_v2']?>";
	var beneficio = $('SolicitudPersonaBeneficioIdV2').getValue();

//	alert(beneficio);


	$('SolicitudPersonaBeneficioIdV2').observe('change',function(){
		beneficio= $('SolicitudPersonaBeneficioIdV2').getValue();
		var diaSel = $('SolicitudFechaOperacionPagoDay').getValue();
		var mesSel = $('SolicitudFechaOperacionPagoMonth').getValue();
		var aniSel = $('SolicitudFechaOperacionPagoYear').getValue();
		var fechaPago = aniSel+'-'+mesSel+'-'+diaSel;
		armaVto(proveedor,beneficio,fechaPago);
	});


	<?php if($organismo != '66'):?>
		beneficio= $('SolicitudPersonaBeneficioIdV2').getValue();
		var diaSel = $('SolicitudFechaOperacionPagoDay').getValue();
		var mesSel = $('SolicitudFechaOperacionPagoMonth').getValue();
		var aniSel = $('SolicitudFechaOperacionPagoYear').getValue();
		var fechaPago = aniSel+'-'+mesSel+'-'+diaSel;

		armaVto(proveedor,beneficio,fechaPago);

		$('SolicitudFechaOperacionPagoDay').observe('change',function(){
			beneficio= $('SolicitudPersonaBeneficioIdV2').getValue();
			var dia = $('SolicitudFechaOperacionPagoDay').getValue();
			var mes = $('SolicitudFechaOperacionPagoMonth').getValue();
			var anio = $('SolicitudFechaOperacionPagoYear').getValue();
			var fecha = anio+'-'+mes+'-'+dia;
			armaVto(proveedor,beneficio,fecha);
		});

		$('SolicitudFechaOperacionPagoMonth').observe('change',function(){
			beneficio= $('SolicitudPersonaBeneficioIdV2').getValue();
			var dia = $('SolicitudFechaOperacionPagoDay').getValue();
			var mes = $('SolicitudFechaOperacionPagoMonth').getValue();
			var anio = $('SolicitudFechaOperacionPagoYear').getValue();
			var fecha = anio+'-'+mes+'-'+dia;
			armaVto(proveedor,beneficio,fecha);
		});

		$('SolicitudFechaOperacionPagoYear').observe('change',function(){
			beneficio= $('SolicitudPersonaBeneficioIdV2').getValue();
			var dia = $('SolicitudFechaOperacionPagoDay').getValue();
			var mes = $('SolicitudFechaOperacionPagoMonth').getValue();
			var anio = $('SolicitudFechaOperacionPagoYear').getValue();
			var fecha = anio+'-'+mes+'-'+dia;
			armaVto(proveedor,beneficio,fecha);
		});

	<?php else:?>
		//es un anses
		beneficio= $('SolicitudPersonaBeneficioIdV2').getValue();
		var diaSel = $('SolicitudFechaCuponAnsesDay').getValue();
		var mesSel = $('SolicitudFechaCuponAnsesMonth').getValue();
		var aniSel = $('SolicitudFechaCuponAnsesYear').getValue();
		var fechaPago = aniSel+'-'+mesSel+'-'+diaSel;

		armaVto(proveedor,beneficio,fechaPago);

		$('SolicitudFechaCuponAnsesDay').observe('change',function(){
			beneficio= $('SolicitudPersonaBeneficioIdV2').getValue();
			var dia = $('SolicitudFechaCuponAnsesDay').getValue();
			var mes = $('SolicitudFechaCuponAnsesMonth').getValue();
			var anio = $('SolicitudFechaCuponAnsesYear').getValue();
			var fecha = anio+'-'+mes+'-'+dia;
			armaVto(proveedor,beneficio,fecha);
		});

		$('SolicitudFechaCuponAnsesMonth').observe('change',function(){
			beneficio= $('SolicitudPersonaBeneficioIdV2').getValue();
			var dia = $('SolicitudFechaCuponAnsesDay').getValue();
			var mes = $('SolicitudFechaCuponAnsesMonth').getValue();
			var anio = $('SolicitudFechaCuponAnsesYear').getValue();
			var fecha = anio+'-'+mes+'-'+dia;
			armaVto(proveedor,beneficio,fecha);
		});

		$('SolicitudFechaCuponAnsesYear').observe('change',function(){
			beneficio= $('SolicitudPersonaBeneficioIdV2').getValue();
			var dia = $('SolicitudFechaCuponAnsesDay').getValue();
			var mes = $('SolicitudFechaCuponAnsesMonth').getValue();
			var anio = $('SolicitudFechaCuponAnsesYear').getValue();
			var fecha = anio+'-'+mes+'-'+dia;
			armaVto(proveedor,beneficio,fecha);
		});

	<?php endif;?>

});

function armaVto(proveedor,beneficio,fechaCarga){
	//alert(beneficio);
	new Ajax.Updater('datos_inicio_vencimiento','<?php echo $this->base?>/proveedores/proveedor_vencimientos/arma_vencimiento/'+ proveedor + '/' + beneficio +'/' + fechaCarga, {asynchronous:true, evalScripts:true, onComplete:function(request, json) {$('spinner').hide();$('btn_submit').enable();},onLoading:function(request) {Element.show('spinner');$('btn_submit').disable();}, requestHeaders:['X-Update', 'datos_inicio_vencimiento']});
}

</script>

	<div class="areaDatoForm">
		<?php
		$comboBeneficios = array();
		if(!empty($beneficios)):

			$selected = NULL;

			foreach($beneficios as $beneficio){
				$codBenPers = substr($beneficio['PersonaBeneficio']['codigo_beneficio'],8,2);
				//if($codBenPers == $organismo){
					// $comboBeneficios[$beneficio['PersonaBeneficio']['id']] = utf8_decode($beneficio['PersonaBeneficio']['string']);
				//}
				$comboBeneficios[$beneficio['PersonaBeneficio']['id']] = utf8_decode($beneficio['PersonaBeneficio']['string']);
				if($codBenPers == $organismo){$selected = $beneficio['PersonaBeneficio']['id'];}
			}
			echo "<div class='notices'><strong>ATENCION!:</strong> Verificar los datos del Beneficio!.</div>";
			echo $frm->input('persona_beneficio_id_v2',array('type'=>'select','options'=>$comboBeneficios,'empty'=>false, 'selected' => $selected,'label'=>'BENEFICIO'));
		else:
			echo "<div class='notices_error'><strong>ATENCION!:</strong> No posee beneficios para el Organismo por el cual se descuenta la presente Solicitud!</div>";
		endif;
		?>

		<?php echo $this->renderElement('persona_beneficios/beneficio_by_idr', array('idr' => $solicitud['Solicitud']['id_beneficio'], 'plugin' => 'pfyj'))?>

		<?php if(isset($solicitud['Solicitud']['persona_beneficio_id_v2'])):?>
			<div class="row">
				<?php //   echo $controles->btnModalBox(array('title' => 'MODIFICAR BENEFICIO','url' => '/v1/solicitudes/actualizar_beneficio/'.$solicitud['Solicitud']['id_beneficio'].'/'.$solicitud['Solicitud']['nro_solicitud'],'h' => 500, 'w' => 650,'img'=>'edit.png','texto' => 'Modificar Datos'))?>
			</div>
		<?php endif;?>

	</div>


<?php if(count($solicitud['Cancelaciones'])!=0):?>

<h3 style="border-bottom: 1px solid;">CANCELACIONES</h3>

	<div class="areaDatoForm">

		<?php echo $this->renderElement('solicitud_cancelaciones/grilla_cancelaciones', array('cancelaciones' => $solicitud['Cancelaciones'],'persona_id' => $persona['Persona']['id'], 'plugin' => 'v1'))?>


		<?php echo $controles->botonGenerico('/v1/solicitudes/generar_expediente/'.$solicitud['Solicitud']['nro_solicitud'],'controles/reload3.png','ACTUALIZAR')?>
		<h4>ORDENES DE CANCELACION</h4>

		<?php echo $this->renderElement('cancelacion_orden/grilla_check', array('cancelaciones' => $cancelaciones_emitidas,'modelo' => 'Solicitud', 'plugin' => 'mutual'))?>

	</div>

<?php endif;?>

<h3 style="border-bottom: 1px solid;">LIQUIDACION Y PAGO DEL PRESTAMO</h3>





		<?php echo $this->renderElement('solicitudes/grilla_liquidacion', array('solicitud' => $solicitud, 'plugin' => 'v1'))?>

		<?php if(!empty($tarjeta)):?>

			<div class="areaDatoForm">
				<h4>Datos Registrados de la Tarjeta</h4>
				<hr>
			<table class="tbl_form">
				<tr><td colspan="2"></td></tr>
					<tr><td>TITULAR</td><td><strong><?php echo $tarjeta->card_holder_name?></strong></td></tr>
					<tr><td>NUMERO</td><td><strong><?php echo $tarjeta->card_number?></strong></td></tr>
					<tr><td>VENCIMIENTO</td><td><strong><?php echo $tarjeta->card_expiration_month?>/<?php echo $tarjeta->card_expiration_year?></strong></td></tr>
					<tr><td>CODIGO</td><td><strong><?php echo $tarjeta->security_code?></strong></strong></td></tr>
					</tr>
				</table>
			</div>		

		<?php endif;?>		

		<h4>DATOS RELACIONADOS AL PAGO</h4>
		<div class="areaDatoForm">
			<table class="tbl_form">
				<tr>
					<td>NRO. CREDITO PROVEEDOR</td>
					<td>
						<?php echo $frm->input('Solicitud.nro_credito_proveedor',array('size'=>50,'maxlenght'=>50, 'value' => (!empty($solicitud['Solicitud']['nro_credito_proveedor']) ? $solicitud['Solicitud']['nro_credito_proveedor'] : "") ,'disabled' => (!empty($solicitud['Solicitud']['nro_credito_proveedor']) ? "disabled" : ""))); ?>
						<?php if(!empty($solicitud['Solicitud']['nro_credito_proveedor'])):?>
							<?php echo $frm->hidden('Solicitud.nro_credito_proveedor_fix',array('value'=>$solicitud['Solicitud']['nro_credito_proveedor']))?>
						<?php endif;?>
					</td>
				</tr>
				<?php if($organismo == '66'):?>
					<tr>
						<td>FECHA CUPON ANSES</td>
						<td><?php echo $frm->input('Solicitud.fecha_cupon_anses',array('dateFormat' => 'DMY','minYear'=>date("Y") - 1, 'maxYear' => date("Y") + 1))?></td>
					</tr>
				<?php endif;?>
				<tr>
					<td>FECHA PAGO</td>
					<td>
						<?php //   if(!empty($solicitud['Solicitud']['fecha_operacion_pago'])):?>
							<?php //   echo $frm->input('Solicitud.fecha_operacion_pago',array('dateFormat' => 'DMY','minYear'=>date("Y") - 1, 'maxYear' => date("Y") + 1,'disabled' => 'disabled'))?>
							<?php //   echo $frm->hidden('Solicitud.fecha_operacion_pago_fix',array('value'=>$solicitud['Solicitud']['fecha_operacion_pago']))?>
						<?php //   else:?>
							<?php echo $frm->input('Solicitud.fecha_operacion_pago',array('dateFormat' => 'DMY','minYear'=>date("Y") - 2, 'maxYear' => date("Y") + 1))?>
						<?php //   endif;?>
					</td>
				</tr>
				<?php if($solicitud['Solicitud']['codigo_fpago'] != '0001'):?>
					<?php if($solicitud['Solicitud']['codigo_fpago'] == '0002'):?>
						<tr id="datoBanco">
							<td>BANCO</td>
							<td colspan="2">
							<?php echo $this->renderElement('banco/combo_global',array('plugin' => 'config','tipo' => 4, 'model' => 'Solicitud.banco_id', 'selected' => (isset($solicitud['Solicitud']['codigo_banco']) ? $solicitud['Solicitud']['codigo_banco'] : ""), 'disabled' =>  (!empty($solicitud['Solicitud']['codigo_banco']) ? 1 : 0)))?>
							</td>
						</tr>
					<?php endif;?>
					<tr id="datoBancoNroOpenBan">
						<td>NRO.COMPROBANTE</td>
						<td colspan="2"><?php echo $frm->input('Solicitud.nro_operacion',array('size'=>50,'maxlenght'=>50, 'value' => (!empty($solicitud['Solicitud']['nro_operacion_pago']) ? $solicitud['Solicitud']['nro_operacion_pago'] : "") ,'disabled' => (!empty($solicitud['Solicitud']['nro_operacion_pago']) ? "disabled" : ""))); ?></td>
					</tr>
				<?php endif;?>
			</table>

			<div id="spinner" style="display: none; float: left;"><?php echo $html->image('controles/ajax-loader.gif'); ?></div>
			<div style="clear: both;"></div>

			<div class="areaDatoForm2" id="datos_inicio_vencimiento">
			</div>

			<div style="clear: both;"></div>

			<table class="tbl_form">
				<tr>
					<td>OBSERVACIONES</td>
				</tr>

				<tr>
					<td><?php echo $frm->textarea('observaciones',array('cols' => 60, 'rows' => 10))?></td>
				</tr>
			</table>

			<div style="clear: both;"></div>

		</div>




<?php echo $this->renderElement('orden_descuento/by_numero', array('tipo' => 'EXPTE','numero'=> $solicitud['Solicitud']['nro_solicitud'], 'plugin' => 'mutual'))?>

<!--AGREGO INPUTS HIDDEN DATOS DE TARJETA -->
<?php echo $frm->hidden('Solicitud.tarjeta_numero',array('value'=>$solicitud['Beneficio']['tarjeta_numero']))?>
<?php echo $frm->hidden('Solicitud.tarjeta_titular',array('value'=>$solicitud['Beneficio']['tarjeta_titular']))?>
<?php echo $frm->hidden('Solicitud.tarjeta_debito',array('value'=>$solicitud['Beneficio']['tarjeta_debito']))?>

<?php echo $frm->hidden('Solicitud.nro_solicitud',array('value'=>$solicitud['Solicitud']['nro_solicitud']))?>
<?php echo $frm->hidden('Solicitud.codigo_producto',array('value'=>$solicitud['Solicitud']['codigo_producto']))?>
<?php echo $frm->hidden('Solicitud.persona_id',array('value'=>$persona['Persona']['id']))?>
<?php echo $frm->hidden('Solicitud.persona_id_v1',array('value'=>$solicitud['Solicitud']['id_persona']))?>
<?php echo $frm->hidden('Solicitud.socio_id',array('value'=> (isset($persona['Socio']['id']) ? $persona['Socio']['id'] : 0)))?>
<?php echo $frm->hidden('Solicitud.con_cancelacion',array('value'=> (isset($solicitud['Cancelaciones']) && count($solicitud['Cancelaciones'])!= 0 ? 1 : 0)))?>
<?php echo $frm->hidden('Solicitud.proveedor_id_v2',array('value' => $solicitud['Solicitud']['proveedor_id_v2']))?>
<?php echo $frm->hidden('Solicitud.id_beneficio',array('value'=>$solicitud['Solicitud']['id_beneficio']))?>
<?php echo $frm->hidden('Solicitud.proveedor_id_v2_situacion_cuota',array('value' => $solicitud['Solicitud']['proveedor_id_v2_situacion_cuota']))?>
<?php echo $frm->hidden('Solicitud.proveedor_id_v2_estado_cuota',array('value' => $solicitud['Solicitud']['proveedor_id_v2_estado_cuota']))?>
<?php echo $frm->hidden('Solicitud.carga_directa',array('value' => $solicitud['Solicitud']['carga_directa']))?>
<?php echo $frm->hidden('Solicitud.codigo_productor',array('value' => $solicitud['Solicitud']['codigo_productor']))?>
<?php echo $frm->hidden('Solicitud.en_mano',array('value' => $solicitud['Solicitud']['en_mano']))?>
<?php echo $frm->hidden('Solicitud.solicitado',array('value' => $solicitud['Solicitud']['solicitado']))?>
<?php echo $frm->hidden('Solicitud.reasignar_proveedor_id',array('value' => $solicitud['Solicitud']['reasignar_proveedor_id']))?>
<?php echo $frm->hidden('Solicitud.codigo_producto_sigem',array('value' => $solicitud['Solicitud']['codigo_producto_sigem']))?>

<?php //   if(isset($solicitud['Solicitud']['persona_beneficio_id_v2'])):?>
	<?php //   echo $frm->hidden('Solicitud.persona_beneficio_id_v2',array('value' => $solicitud['Solicitud']['persona_beneficio_id_v2']))?>
<?php //   endif;?>

<?php if($solicitud['Solicitud']['reasignar_proveedor_id'] != 0):?>

	<div class='notices_error' style="width: 100%">
		<strong>ATENCION!</strong><br/>
		La presente solicitud fu&eacute; marcada para ser reasignada el <?php echo date("d-m-Y", strtotime($solicitud['Solicitud']['reasigna_proveedor_fecha']))?> por
		el usuario <strong><?php echo $solicitud['Solicitud']['reasigna_proveedor_user']?></strong>.
		La Orden de Descuento que se emitir&aacute; ser&aacute; asignada a <strong><?php echo $solicitud['Solicitud']['reasignar_proveedor_razon_social']?></strong>
	</div>
	<div style="clear: both;"></div>

<?php endif;?>

<?php echo $frm->btnGuardarCancelar(array('URL' => '/v1/solicitudes/a_verificar','TXT_GUARDAR' => "APROBAR SOLICITUD"))?>
<?php //  debug($tarjeta)?>
