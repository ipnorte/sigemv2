<?php if($menuPersonas == 1) echo $this->renderElement('personas/padron_header',array('persona' => $persona,'plugin'=>'pfyj'))?>
<?php if($persona['Persona']['fallecida'] == 1):?>
	<div class="notices_error">PERSONA REGISTRADA COMO FALLECIDA EL <?php echo $util->armaFecha($persona['Persona']['fecha_fallecimiento'])?></div>
<?php endif;?>

<h3>ALTA SOLICITUD DE AYUDA ECONOMICA (Res. No1418/03 INAES)</h3>
<?php echo $this->renderElement('mutual_producto_solicitudes/menu',array('persona' => $persona,'plugin'=>'mutual'))?>

<?php if(count($persona['PersonaBeneficio']) != 0):?>

<script type="text/javascript">
Event.observe(window, 'load', function(){


	<?php if($persona['Persona']['fallecida'] == 1):?>
		$('NuevaOrdenProductoMutual').disable();
		$('btn_submit').disable();
		return;
	<?php endif;?>
	
	$('btn_submit').disable();
    
    $('ProveedorPlanCuotaGrillaMontoId').enable();
     $('ProveedorPlanGrillaCuotaCuotaId').enable();
     $('MutualProductoSolicitudFormaPago').enable();
     $('MutualProductoSolicitudObservaciones').enable();
     $('MutualProductoSolicitudVendedorId').enable();    
	
	var beneficioSel = $('MutualProductoSolicitudPersonaBeneficioId').getValue();
	getComboPlanes();

	$('MutualProductoSolicitudPersonaBeneficioId').observe('change',function(){
		getComboPlanes();
	});
	

	$('ProveedorPlanId').observe('change',function(){
        $('ProveedorPlanCuotaGrillaMontoId').enable();
        $('ProveedorPlanGrillaCuotaCuotaId').enable();
        $('MutualProductoSolicitudFormaPago').enable();
        $('MutualProductoSolicitudObservaciones').enable();
        $('MutualProductoSolicitudVendedorId').enable();        
		getComboMontos();
	});	
	$('ProveedorPlanCuotaGrillaMontoId').observe('change',function(){
		getComboCuotas();
	});	

});

function getComboPlanes(){
	beneficio = $('MutualProductoSolicitudPersonaBeneficioId').getValue();
// 	$('spinner_msg').update("CARGANDO PLANES");
	new Ajax.Updater('ProveedorPlanId','<?php echo $this->base?>/proveedores/proveedor_planes/combo_planes_vigentes/'+ beneficio + '/P/0/0/0/0/1' , {asynchronous:true, evalScripts:true, onComplete:function(request, json) {$('spinner_plan').hide();$('btn_submit').enable();getComboMontos();},onLoading:function(request) {Element.show('spinner_plan');$('btn_submit').disable();}, requestHeaders:['X-Update', 'ProveedorPlanId']});
}

function getComboMontos(){
	planID = $('ProveedorPlanId').getValue();
	if(planID !== null){
		beneficio = $('MutualProductoSolicitudPersonaBeneficioId').getValue();
// 		$('spinner_msg').update("CARGANDO MONTOS");
		new Ajax.Updater('ProveedorPlanCuotaGrillaMontoId','<?php echo $this->base?>/proveedores/proveedor_planes/combo_planes_vigentes/'+ beneficio + '/M/' + planID, {asynchronous:true, evalScripts:true, onComplete:function(request, json) {$('spinner_montos').hide();$('btn_submit').enable();getComboCuotas();},onLoading:function(request) {Element.show('spinner_montos');$('btn_submit').disable();}, requestHeaders:['X-Update', 'ProveedorPlanCuotaGrillaMontoId']});
	}else{
		$('btn_submit').disable();
		alert("**** NO EXISTEN PLANES DISPONIBLES PARA EL ORGANISMO DEL BENEFICIO ****");
	}		
}

function getComboCuotas(){
	planID = $('ProveedorPlanId').getValue();
	beneficio = $('MutualProductoSolicitudPersonaBeneficioId').getValue();
	fecha = '<?php echo date('Y-m-d')?>';
// 	$('spinner_msg').update("CARGANDO CUOTAS");
	cuotaID = $('ProveedorPlanCuotaGrillaMontoId').getValue();
    if(cuotaID !== null){
        new Ajax.Updater('ProveedorPlanGrillaCuotaCuotaId','<?php echo $this->base?>/proveedores/proveedor_planes/combo_planes_vigentes/'+ beneficio + '/C/' + planID + '/' + cuotaID, {asynchronous:true, evalScripts:true, onComplete:function(request, json) {$('spinner_cuotas').hide();$('btn_submit').enable();},onLoading:function(request) {Element.show('spinner_cuotas');$('btn_submit').disable();}, requestHeaders:['X-Update', 'ProveedorPlanGrillaCuotaCuotaId']});
	}else{
        $('ProveedorPlanCuotaGrillaMontoId').disable();
        $('ProveedorPlanGrillaCuotaCuotaId').disable();
        $('ProveedorPlanGrillaCuotaCuotaId').update("");
        $('MutualProductoSolicitudFormaPago').disable();
        $('MutualProductoSolicitudObservaciones').disable();
        $('MutualProductoSolicitudVendedorId').disable();
		$('btn_submit').disable();
		alert("**** NO EXISTEN MONTOS DISPONIBLES PARA PLAN ****");
    }
}

function confirmar(){

	if($('ProveedorPlanId').getValue() === null || $('ProveedorPlanCuotaGrillaMontoId').getValue() === null || $('ProveedorPlanGrillaCuotaCuotaId').getValue() === null){
		return false;
	}	
	
	var msg = "**** NUEVA SOLICITUD DE CREDITO ****\n\n";
	msg = msg + "BENEFICIO: " + getTextoSelect('MutualProductoSolicitudPersonaBeneficioId') + "\n\n";
	msg = msg + "PLAN: " + getTextoSelect('ProveedorPlanId') + "\n";
	msg = msg + "SOLICITADO: " + getTextoSelect('ProveedorPlanCuotaGrillaMontoId') + " en " + getTextoSelect('ProveedorPlanGrillaCuotaCuotaId') + "\n";

	if($('MutualProductoSolicitudVendedorId').getValue() !== ""){
		msg = msg + "\n";
		msg = msg + "VENDEDOR: " + getTextoSelect('MutualProductoSolicitudVendedorId') + "\n\n";
	}
	
	msg = msg + "\n" + "GENERAR SOLICITUD?";
	return confirm(msg);
}

</script>

<div class="areaDatoForm">

	<?php echo $frm->create(null,array('action' => 'nuevo_credito/'.$persona['Persona']['id'],'id' => 'NuevaOrdenProductoMutual','onsubmit' => "return confirmar()"))?>

		<table class="tbl_form">
			<tr>
				<td>BENEFICIO</td>
				<td><?php echo $this->renderElement('persona_beneficios/combo_beneficios',array('plugin' => 'pfyj','persona_id' => $persona['Persona']['id'],'soloActivos' => 1,'style' => 'font-size: 14px;font-weight: bold;'))?></td>
			</tr> 
			<tr>
				<td>PLAN</td>
				<td>
					<table class="tbl_form">
						<tr>
							<td><select style="font-size: 13px;font-weight: bold;" name="data[ProveedorPlan][id]" id="ProveedorPlanId"></select></td>
							<td><div id="spinner_plan" style="display: none; float: left;color:red;font-size:xx-small;"><?php echo $html->image('controles/ajax-loader.gif');?></div></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>MONTOS Y CUOTAS</td>
				<td>
					<table class="tbl_form">
						<tr>
							<td><select style="font-size: 13px;font-weight: bold;" name="data[ProveedorPlanGrillaCuota][monto_id]" id="ProveedorPlanCuotaGrillaMontoId"></select></td>
							<td><div id="spinner_montos" style="display: none; float: left;color:red;font-size:xx-small;"><?php echo $html->image('controles/ajax-loader.gif');?></div></td>
							<td><select style="font-size: 13px;font-weight: bold;" name="data[ProveedorPlanGrillaCuota][cuota_id]" id="ProveedorPlanGrillaCuotaCuotaId"></select></td>
							<td><div id="spinner_cuotas" style="display: none; float: left;color:red;font-size:xx-small;"><?php echo $html->image('controles/ajax-loader.gif');?></div></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>FORMA DE PAGO</td>
				<td>
				<?php echo $this->renderElement('global_datos/combo_global',array(
																				'plugin'=>'config',
																				'metodo' => "get_fpago_solicitud",
																				'model' => 'MutualProductoSolicitud.forma_pago',
																				'empty' => false,
																				'selected' => $this->data['MutualProductoSolicitud']['forma_pago']
				))?>				
				</td>
			</tr>
			<?php if(!empty($cancelaciones)):?>
			<tr>
				<td colspan="2">
				<div class="areaDatoForm3">
					<h3>ORDENES DE CANCELACION EMITIDAS</h3>
					<table>
						<tr>
							<th></th>
							<th>#</th>
							<th>VENCIMIENTO</th>
							<th>A LA ORDEN DE</th>
							<th>CONCEPTO</th>
							<th>IMPORTE</th>
						</tr>
						<?php foreach ($cancelaciones as $cancelacion):?>
							<tr>
								<td><input type="checkbox" name="data[MutualProductoSolicitud][CancelacionOrden][<?php echo $cancelacion['CancelacionOrden']['id']?>]" value="<?php echo $cancelacion['CancelacionOrden']['id']?>"/></td>
								<td><?php echo $cancelacion['CancelacionOrden']['id']?></td>
								<td><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto'])?></td>
								<td><?php echo $cancelacion['CancelacionOrden']['a_la_orden_de']?></td>
								<td><?php echo $cancelacion['CancelacionOrden']['concepto']?></td>
								<td align="right"><?php echo $util->nf($cancelacion['CancelacionOrden']['importe_proveedor'])?></td>
							</tr>
						<?php endforeach;?>
					</table>
				
				</div>
				</td>
			</tr>
			<?php endif;?>
			<tr>
				<td>OBSERVACIONES</td>
				<td><?php echo $frm->textarea('MutualProductoSolicitud.observaciones',array('cols' => 60, 'rows' => 10))?></td>
			</tr>
			<tr>
				<td>VENDEDOR</td>
				<td><?php echo $frm->input('MutualProductoSolicitud.vendedor_id',array('type' => 'select','options' => $vendedores, 'empty' => true))?></td>
			</tr>

		</table>
		<div style="clear: both;"></div>
				
		<?php //   echo $controles->ajaxLoader('spinner_msg','PROCESANDO...')?>


	<?php echo $frm->hidden('persona_id',array('value' => $persona['Persona']['id']))?>
	<?php echo $frm->hidden('socio_id',array('value' => (isset($persona['Socio']['id']) ? $persona['Socio']['id'] : 0)))?>
	<?php echo $frm->hidden('permanente',array('value' => 0))?>
	<?php echo $frm->hidden('sin_cargo',array('value' => 0))?>
	<?php echo $frm->hidden('con_importe_fijo',array('value' => 0))?>
	<?php echo $frm->hidden('prestamo',array('value' => 1))?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GENERAR SOLICITUD','URL' => ( empty($fwrd) ? "/mutual/mutual_producto_solicitudes/by_persona/".$persona['Persona']['id'] : $fwrd) ))?>


</div>
<?php else:?>

		<div class='notices_error'>NO POSEE BENEFICIOS CARGADOS!</div>
		<div class="actions"><?php echo $controles->botonGenerico('/pfyj/persona_beneficios/add/'.$persona['Persona']['id'],'controles/add.png','Cargar Beneficio')?></div>

<?php endif;?>	