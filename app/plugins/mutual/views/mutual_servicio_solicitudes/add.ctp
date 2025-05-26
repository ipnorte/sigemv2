<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona,'plugin' => 'pfyj'))?>

<?php if($persona['Persona']['fallecida'] == 1):?>
	<div class="notices_error">PERSONA REGISTRADA COMO FALLECIDA EL <?php echo $util->armaFecha($persona['Persona']['fecha_fallecimiento'])?></div>
<?php endif;?>

<h3>NUEVA ORDEN DE SERVICIO</h3>
	
<script type="text/javascript">

	var adicionales = <?php echo (!empty($adicionales) ? count($adicionales) : 0 )?>;
	
	Event.observe(window, 'load', function() {
		<?php if($persona['Persona']['fallecida'] == 1):?>
			$('NuevaOrdenServicio').disable();
			$('btn_submit').disable();
			$("btn_cancel").enable();
			return;
		<?php endif;?>;	
	
		var beneficioSel = $('MutualServicioSolicitudPersonaBeneficioId').getValue();
		var proveedorSel = getProveedorId($('MutualServicioSolicitudTipoMutualServicioId').getValue(),3);

		document.getElementById("MutualServicioSolicitudMutualServicioId").value = getProveedorId($('MutualServicioSolicitudTipoMutualServicioId').getValue(),0);


		document.getElementById("MutualServicioSolicitudFechaAltaServicioDay").value = "<?php echo date("d",strtotime($fechaCobertura))?>";
		document.getElementById("MutualServicioSolicitudFechaAltaServicioMonth").value = "<?php echo date("m",strtotime($fechaCobertura))?>";
		document.getElementById("MutualServicioSolicitudFechaAltaServicioYear").value = "<?php echo date("Y",strtotime($fechaCobertura))?>";

		$('MutualServicioSolicitudFechaEmisionDay').disable();
		$('MutualServicioSolicitudFechaEmisionMonth').disable();
		$('MutualServicioSolicitudFechaEmisionYear').disable();

		$('MutualServicioSolicitudFechaAltaServicioDay').disable();
		$('MutualServicioSolicitudFechaAltaServicioMonth').disable();
		$('MutualServicioSolicitudFechaAltaServicioYear').disable();
		
		var diaSel = $('MutualServicioSolicitudFechaAltaServicioDay').getValue();
		var mesSel = $('MutualServicioSolicitudFechaAltaServicioMonth').getValue();
		var aniSel = $('MutualServicioSolicitudFechaAltaServicioYear').getValue();
		var fechaSel = aniSel+'-'+mesSel+'-'+diaSel;

		armaVto(proveedorSel,beneficioSel,fechaSel);


		$('MutualServicioSolicitudTipoMutualServicioId').observe('change',function(){
			var beneficio= $('MutualServicioSolicitudPersonaBeneficioId').getValue();
			var proveedor = getProveedorId($('MutualServicioSolicitudTipoMutualServicioId').getValue(),3);
			var dia = $('MutualServicioSolicitudFechaAltaServicioDay').getValue();
			var mes = $('MutualServicioSolicitudFechaAltaServicioMonth').getValue();
			var anio = $('MutualServicioSolicitudFechaAltaServicioYear').getValue();
			var fecha = anio+'-'+mes+'-'+dia;
//			CuotaSocial(beneficio,getProveedorId($('ProductoMutual').getValue(),5));
			armaVto(proveedor,beneficio,fecha);
			document.getElementById("MutualServicioSolicitudMutualServicioId").value = getProveedorId($('MutualServicioSolicitudTipoMutualServicioId').getValue(),0);

		});

//		document.getElementById("SocioServicioSolicitudMutualServicioId").value = ServicioId;
		

		$('MutualServicioSolicitudPersonaBeneficioId').observe('change',function(){
			
			var beneficio= $('MutualServicioSolicitudPersonaBeneficioId').getValue();
			var proveedor = getProveedorId($('MutualServicioSolicitudTipoMutualServicioId').getValue(),3);
			var dia = $('MutualServicioSolicitudFechaAltaServicioDay').getValue();
			var mes = $('MutualServicioSolicitudFechaAltaServicioMonth').getValue();
			var anio = $('MutualServicioSolicitudFechaAltaServicioYear').getValue();
			var fecha = anio+'-'+mes+'-'+dia;
//			CuotaSocial(beneficio,getProveedorId($('ProductoMutual').getValue(),5));
			armaVto(proveedor,beneficio,fecha);

			
		});	


		$('MutualServicioSolicitudFechaAltaServicioDay').observe('change',function(){
			var beneficio= $('MutualServicioSolicitudPersonaBeneficioId').getValue();
			var proveedor = getProveedorId($('MutualServicioSolicitudTipoMutualServicioId').getValue(),3);
			var dia = $('MutualServicioSolicitudFechaAltaServicioDay').getValue();
			var mes = $('MutualServicioSolicitudFechaAltaServicioMonth').getValue();
			var anio = $('MutualServicioSolicitudFechaAltaServicioYear').getValue();
			var fecha = anio+'-'+mes+'-'+dia;
			armaVto(proveedor,beneficio,fecha);
		});

		$('MutualServicioSolicitudFechaAltaServicioMonth').observe('change',function(){
			var beneficio= $('MutualServicioSolicitudPersonaBeneficioId').getValue();
			var proveedor = getProveedorId($('MutualServicioSolicitudTipoMutualServicioId').getValue(),3);
			var dia = $('MutualServicioSolicitudFechaAltaServicioDay').getValue();
			var mes = $('MutualServicioSolicitudFechaAltaServicioMonth').getValue();
			var anio = $('MutualServicioSolicitudFechaAltaServicioYear').getValue();
			var fecha = anio+'-'+mes+'-'+dia;
			armaVto(proveedor,beneficio,fecha);
		});	

		$('MutualServicioSolicitudFechaAltaServicioYear').observe('change',function(){
			var beneficio= $('MutualServicioSolicitudPersonaBeneficioId').getValue();
			var proveedor = getProveedorId($('MutualServicioSolicitudTipoMutualServicioId').getValue(),3);
			var dia = $('MutualServicioSolicitudFechaAltaServicioDay').getValue();
			var mes = $('MutualServicioSolicitudFechaAltaServicioMonth').getValue();
			var anio = $('MutualServicioSolicitudFechaAltaServicioYear').getValue();
			var fecha = anio+'-'+mes+'-'+dia;
			armaVto(proveedor,beneficio,fecha);
		});
		
		
	});

	function getProveedorId(str,idx){
		var val = str.split('|');
		return val[idx];
	}

	function CuotaSocial(beneficio,importeDiferencial){
		<?php if(!isset($persona['Socio']['id']) || empty($persona['Socio']['id'])):?>
		if(importeDiferencial > 0){
			$('importe_cuota_social').update('CUOTA SOCIAL DIFERENCIAL | IMPORTE: ' + importeDiferencial);
		}else{
			new Ajax.Updater('importe_cuota_social','<?php echo $this->base?>/pfyj/persona_beneficios/importe_cuota_social/'+ beneficio, {asynchronous:true, evalScripts:true, onComplete:function(request, json) {$('spinner2').hide();$('btn_submit').enable();},onLoading:function(request) {Element.show('spinner2');$('btn_submit').disable();}, requestHeaders:['X-Update', 'importe_cuota_social']});
		}
		<?php endif;?>
	}		

	function validateForm(){

		var msgConfirm = "ATENCION!\n\n";
		msgConfirm = msgConfirm + "GENERAR LA ORDEN DE SERVICIO: \n\n";
		msgConfirm = msgConfirm + getTextoSelect("MutualServicioSolicitudTipoMutualServicioId") + "\n";
		msgConfirm = msgConfirm + "\n";
		msgConfirm = msgConfirm + "INICIO DE COBERTURA EL: " + getStrFecha("MutualServicioSolicitudFechaAltaServicio") + "\n";
		msgConfirm = msgConfirm + "BENEFICIO: " + getTextoSelect("MutualServicioSolicitudPersonaBeneficioId") + "\n";

		msgConfirm = msgConfirm + "\n";
		var strAdic = "";
		for (i=0; i < adicionales; i++){
			oChkCheck = document.getElementById('chk_' + i);
			if(oChkCheck.checked){
				strAdic = strAdic + "* " + document.getElementById('adicional_' + i).value + "\n";
				error = false;
			}
		}		

		if(strAdic !== ""){
			msgConfirm = msgConfirm + "INCORPORAR LOS SIGUIENTES ADICIONALES: \n\n" + strAdic;
		}
		
		return confirm(msgConfirm);
	}

	function armaVto(proveedor,beneficio,fechaCarga){
		new Ajax.Updater('datos_inicio_vencimiento','<?php echo $this->base?>/proveedores/proveedor_vencimientos/arma_vencimiento/'+ proveedor + '/' + beneficio +'/' + fechaCarga, {asynchronous:true, evalScripts:true, onComplete:function(request, json) {$('spinner').hide();$('btn_submit').enable();},onLoading:function(request) {Element.show('spinner');$('btn_submit').disable();}, requestHeaders:['X-Update', 'datos_inicio_vencimiento']});
	}

	
</script>	
	
<?php echo $frm->create(null,array('action' => 'add/'.$persona['Persona']['id'],'id' => 'NuevaOrdenServicio','onsubmit' => "return validateForm();"))?>

<div class="areaDatoForm">

	<table class="tbl_form">
		<tr>
			<td>FECHA EMISION</td><td><?php echo $frm->input('MutualServicioSolicitud.fecha_emision',array('dateFormat' => 'DMY','value' => $fechaEmision,'minYear' => date('Y'), 'maxYear' => date("Y") + 1))?></td>
		</tr>	
		<tr>
			<td>SERVICIO</td><td><?php echo $this->renderElement('mutual_servicios/combo_servicios',array('plugin' => 'mutual','model' => 'MutualServicioSolicitud','selected' => $this->data['SocioServicioSolicitud']['tipo_servicio_mutual_producto_id']))?></td>
		</tr>
		<tr>
			<td>INICIO DE COBERTURA</td><td><?php echo $frm->input('MutualServicioSolicitud.fecha_alta_servicio',array('dateFormat' => 'DMY','value' => $fechaCobertura,'minYear' => date('Y'), 'maxYear' => date("Y") + 1))?></td>
		</tr>
		<tr>
			<td>BENEFICIO</td><td><?php echo $this->renderElement('persona_beneficios/combo_beneficios',array('plugin' => 'pfyj','persona_id' => $persona['Persona']['id'],'soloActivos' => 1))?></td>
		</tr>
		<tr>
			<td>NRO.REFERENCIA</td><td><?php echo $frm->input('MutualServicioSolicitud.nro_referencia_proveedor',array('size' => 10,'maxlength'=> 10)); ?></td>
		</tr>
	</table>
	<?php if(!empty($adicionales)):?>
		<table>
			<tr>
				<th colspan="5">INCORPORAR ADICIONALES AL SERVICIO</th>
			</tr>
			<tr>
				<th></th>
				<th>DOCUMENTO</th>
				<th>NOMBRE</th>
				<th>SEXO</th>
				<th>VINCULO</th>
			</tr>
		<?php $i = 0;?>		
		<?php foreach($adicionales as $adicional):?>
			<tr id="<?php echo $adicional['SocioAdicional']['id']?>">
				<td>
					<input type="checkbox" id="chk_<?php echo $i?>" name="data[MutualServicioSolicitud][socio_adicional_id][<?php echo $adicional['SocioAdicional']['id']?>]" value="1" onclick="toggleCell('<?php echo $adicional['SocioAdicional']['id']?>', this)"/>
					<input type="hidden" id="adicional_<?php echo $i?>" value="<?php echo $adicional['SocioAdicional']['tdoc_ndoc_apenom']?>"/>
				</td>
				<td><?php echo $adicional['SocioAdicional']['tdoc_ndoc']?></td>
				<td><?php echo $adicional['SocioAdicional']['apenom']?></td>
				<td align="center"><?php echo $adicional['SocioAdicional']['sexo']?></td>
				<td><?php echo $adicional['SocioAdicional']['vinculo_desc']?></td>
			</tr>
			<?php $i++;?>
		<?php endforeach;?>
		</table>	
	<?php endif;?>
	<div style="clear: both;"></div>

	<div id="spinner" style="display: none; float: left;color:red;font-size:xx-small;"><?php echo $html->image('controles/ajax-loader.gif'); ?>...Calculando Vencimientos</div>
	<div style="clear: both;"></div>
	
	<div class="areaDatoForm2" id="datos_inicio_vencimiento">
	</div>
	<?php if(!isset($persona['Socio']['id']) || empty($persona['Socio']['id'])):?>
		<div style="background-color: #D8DBD4;padding: 5px;">
			<strong>PERSONA NO REGISTRADA COMO SOCIO!</strong>
			<br/>
			Con la Aprobaci&oacute;n de la presente Orden se generar&aacute; una ORDEN DE DESCUENTO (CMUTU - Cargos Mutual - PERMANENTE)
			<br/>
			en concepto de <strong><span id="importe_cuota_social"></span></strong>
			<br/>
			Para imprimir la SOLICITUD DE AFILIACION, deber&aacute; ingresar en la solapa <strong>Socio</strong> una vez generada la presente Orden.
			<div id="spinner2" style="display: none; float: right;color:red;font-size:xx-small;"><?php echo $html->image('controles/ajax-loader.gif'); ?>...Buscando Importe Cuota Social</div>
		</div>	
	<?php endif;?>
	<div class="row">OBSERVACIONES</div>
	<div class="row">
		<?php echo $frm->textarea('MutualServicioSolicitud.observaciones',array('cols' => 60, 'rows' => 10))?>
	</div>			

</div>
<?php echo $frm->hidden('MutualServicioSolicitud.mutual_servicio_id'); ?>
<?php echo $frm->hidden('MutualServicioSolicitud.fecha_emision',array('value' => $fechaEmision)); ?>
<?php echo $frm->hidden('MutualServicioSolicitud.fecha_alta_servicio',array('value' => $fechaCobertura)); ?>
<?php echo $frm->hidden('MutualServicioSolicitud.persona_id',array('value' => $persona['Persona']['id'])); ?>
<?php echo $frm->hidden('MutualServicioSolicitud.socio_id',array('value' => (!empty($persona['Socio']) ? $persona['Socio']['id'] : 0))); ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/mutual/mutual_servicio_solicitudes/index/'.$persona['Persona']['id']))?>