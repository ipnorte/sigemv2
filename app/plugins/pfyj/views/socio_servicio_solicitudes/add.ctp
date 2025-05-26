<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>

<?php if($persona['Persona']['fallecida'] == 1):?>
	<div class="notices_error">PERSONA REGISTRADA COMO FALLECIDA EL <?php echo $util->armaFecha($persona['Persona']['fecha_fallecimiento'])?></div>
<?php endif;?>

<h3>NUEVA ORDEN DE SERVICIO</h3>
	
<script type="text/javascript">

	Event.observe(window, 'load', function() {
		<?php if($persona['Persona']['fallecida'] == 1):?>
			$('NuevaOrdenServicio').disable();
			$('btn_submit').disable();
			$("btn_cancel").enable();
			return;
		<?php endif;?>	
	
		var beneficioSel = $('SocioServicioSolicitudPersonaBeneficioId').getValue();
		var proveedorSel = getProveedorId($('SocioServicioSolicitudTipoMutualServicioId').getValue(),3);

		document.getElementById("SocioServicioSolicitudMutualServicioId").value = getProveedorId($('SocioServicioSolicitudTipoMutualServicioId').getValue(),0);
		
		var diaSel = $('SocioServicioSolicitudFechaAltaServicioDay').getValue();
		var mesSel = $('SocioServicioSolicitudFechaAltaServicioMonth').getValue();
		var aniSel = $('SocioServicioSolicitudFechaAltaServicioYear').getValue();
		var fechaSel = aniSel+'-'+mesSel+'-'+diaSel;

		armaVto(proveedorSel,beneficioSel,fechaSel);


		$('SocioServicioSolicitudTipoMutualServicioId').observe('change',function(){
			var beneficio= $('SocioServicioSolicitudPersonaBeneficioId').getValue();
			var proveedor = getProveedorId($('SocioServicioSolicitudTipoMutualServicioId').getValue(),3);
			var dia = $('SocioServicioSolicitudFechaAltaServicioDay').getValue();
			var mes = $('SocioServicioSolicitudFechaAltaServicioMonth').getValue();
			var anio = $('SocioServicioSolicitudFechaAltaServicioYear').getValue();
			var fecha = anio+'-'+mes+'-'+dia;
//			CuotaSocial(beneficio,getProveedorId($('ProductoMutual').getValue(),5));
			armaVto(proveedor,beneficio,fecha);
			document.getElementById("SocioServicioSolicitudMutualServicioId").value = getProveedorId($('SocioServicioSolicitudTipoMutualServicioId').getValue(),0);

		});

//		document.getElementById("SocioServicioSolicitudMutualServicioId").value = ServicioId;
		

		$('SocioServicioSolicitudPersonaBeneficioId').observe('change',function(){
			
			var beneficio= $('SocioServicioSolicitudPersonaBeneficioId').getValue();
			var proveedor = getProveedorId($('SocioServicioSolicitudTipoMutualServicioId').getValue(),3);
			var dia = $('SocioServicioSolicitudFechaAltaServicioDay').getValue();
			var mes = $('SocioServicioSolicitudFechaAltaServicioMonth').getValue();
			var anio = $('SocioServicioSolicitudFechaAltaServicioYear').getValue();
			var fecha = anio+'-'+mes+'-'+dia;
//			CuotaSocial(beneficio,getProveedorId($('ProductoMutual').getValue(),5));
			armaVto(proveedor,beneficio,fecha);

			
		});	


		$('SocioServicioSolicitudFechaAltaServicioDay').observe('change',function(){
			var beneficio= $('SocioServicioSolicitudPersonaBeneficioId').getValue();
			var proveedor = getProveedorId($('SocioServicioSolicitudTipoMutualServicioId').getValue(),3);
			var dia = $('SocioServicioSolicitudFechaAltaServicioDay').getValue();
			var mes = $('SocioServicioSolicitudFechaAltaServicioMonth').getValue();
			var anio = $('SocioServicioSolicitudFechaAltaServicioYear').getValue();
			var fecha = anio+'-'+mes+'-'+dia;
			armaVto(proveedor,beneficio,fecha);
		});

		$('SocioServicioSolicitudFechaAltaServicioMonth').observe('change',function(){
			var beneficio= $('SocioServicioSolicitudPersonaBeneficioId').getValue();
			var proveedor = getProveedorId($('SocioServicioSolicitudTipoMutualServicioId').getValue(),3);
			var dia = $('SocioServicioSolicitudFechaAltaServicioDay').getValue();
			var mes = $('SocioServicioSolicitudFechaAltaServicioMonth').getValue();
			var anio = $('SocioServicioSolicitudFechaAltaServicioYear').getValue();
			var fecha = anio+'-'+mes+'-'+dia;
			armaVto(proveedor,beneficio,fecha);
		});	

		$('SocioServicioSolicitudFechaAltaServicioYear').observe('change',function(){
			var beneficio= $('SocioServicioSolicitudPersonaBeneficioId').getValue();
			var proveedor = getProveedorId($('SocioServicioSolicitudTipoMutualServicioId').getValue(),3);
			var dia = $('SocioServicioSolicitudFechaAltaServicioDay').getValue();
			var mes = $('SocioServicioSolicitudFechaAltaServicioMonth').getValue();
			var anio = $('SocioServicioSolicitudFechaAltaServicioYear').getValue();
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
		return true;
	}

	function armaVto(proveedor,beneficio,fechaCarga){
		new Ajax.Updater('datos_inicio_vencimiento','<?php echo $this->base?>/proveedores/proveedor_vencimientos/arma_vencimiento/'+ proveedor + '/' + beneficio +'/' + fechaCarga, {asynchronous:true, evalScripts:true, onComplete:function(request, json) {$('spinner').hide();$('btn_submit').enable();},onLoading:function(request) {Element.show('spinner');$('btn_submit').disable();}, requestHeaders:['X-Update', 'datos_inicio_vencimiento']});
	}

	
</script>	
	
<?php echo $frm->create(null,array('action' => 'add/'.$persona['Persona']['id'],'id' => 'NuevaOrdenServicio','onsubmit' => "return validateForm();"))?>

<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>SERVICIO</td><td><?php echo $this->renderElement('mutual_servicios/combo_servicios',array('plugin' => 'mutual','model' => 'SocioServicioSolicitud','selected' => $this->data['SocioServicioSolicitud']['tipo_servicio_mutual_producto_id']))?></td>
		</tr>
		<tr>
			<td>FECHA ALTA DEL SERVICIO</td><td><?php echo $frm->input('SocioServicioSolicitud.fecha_alta_servicio',array('dateFormat' => 'DMY','label'=>'','minYear' => date('Y'), 'maxYear' => date("Y") + 1))?></td>
		</tr>
		<tr>
			<td>BENEFICIO</td><td><?php echo $this->renderElement('persona_beneficios/combo_beneficios',array('plugin' => 'pfyj','persona_id' => $persona['Persona']['id'],'soloActivos' => 1))?></td>
		</tr>
		<tr>
			<td>NRO.REFERENCIA</td><td><?php echo $frm->input('SocioServicioSolicitud.nro_referencia_proveedor',array('size' => 10,'maxlength'=> 10)); ?></td>
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
		<?php foreach($adicionales as $adicional):?>
			<tr id="<?php echo $adicional['SocioAdicional']['id']?>">
				<td><input type="checkbox" name="data[SocioServicioSolicitud][socio_adicional_id][<?php echo $adicional['SocioAdicional']['id']?>]" value="1" onclick="toggleCell('<?php echo $adicional['SocioAdicional']['id']?>', this)"/></td>
				<td><?php echo $adicional['SocioAdicional']['tdoc_ndoc']?></td>
				<td><?php echo $adicional['SocioAdicional']['apenom']?></td>
				<td align="center"><?php echo $adicional['SocioAdicional']['sexo']?></td>
				<td><?php echo $adicional['SocioAdicional']['vinculo_desc']?></td>
			</tr>
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
		<?php echo $frm->textarea('SocioServicioSolicitud.observaciones',array('cols' => 60, 'rows' => 10))?>
	</div>			

</div>
<?php echo $frm->hidden('SocioServicioSolicitud.mutual_servicio_id'); ?>

<?php echo $frm->hidden('SocioServicioSolicitud.persona_id',array('value' => $persona['Persona']['id'])); ?>
<?php echo $frm->hidden('SocioServicioSolicitud.socio_id',array('value' => (!empty($persona['Socio']) ? $persona['Socio']['id'] : 0))); ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/pfyj/socio_servicio_solicitudes/index/'.$persona['Persona']['id']))?>