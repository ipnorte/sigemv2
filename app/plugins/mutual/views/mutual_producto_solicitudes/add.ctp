<?php if($menuPersonas == 1) echo $this->renderElement('personas/padron_header',array('persona' => $persona,'plugin'=>'pfyj'))?>
<?php if($persona['Persona']['fallecida'] == 1):?>
	<div class="notices_error">PERSONA REGISTRADA COMO FALLECIDA EL <?php echo $util->armaFecha($persona['Persona']['fecha_fallecimiento'])?></div>
<?php endif;?>

<script type="text/javascript">

Event.observe(window, 'load', function() {

	<?php if($persona['Persona']['fallecida'] == 1):?>
		$('NuevaOrdenProductoMutual').disable();
		$('btn_submit').disable();
		return;
	<?php endif;?>

	$('MutualProductoSolicitudFechaDay').disable();
	$('MutualProductoSolicitudFechaMonth').disable();
	$('MutualProductoSolicitudFechaYear').disable();

	




	var beneficioSel = $('MutualProductoSolicitudPersonaBeneficioId').getValue();
	var proveedorSel = getProveedorId($('ProductoMutual').getValue(),3);

	var diaSel = $('MutualProductoSolicitudFechaPagoDay').getValue();
	var mesSel = $('MutualProductoSolicitudFechaPagoMonth').getValue();
	var aniSel = $('MutualProductoSolicitudFechaPagoYear').getValue();
	var fechaSel = aniSel+'-'+mesSel+'-'+diaSel;


	var cuotaSocDif = getProveedorId($('ProductoMutual').getValue(),5);


	//verifico el valor que viene por defecto para saber si es mensual o no
	control_mensual(getProveedorId($('ProductoMutual').getValue(),6),getProveedorId($('ProductoMutual').getValue(),4));
	
	var sinCargo = getProveedorId($('ProductoMutual').getValue(),7);

	if(sinCargo == 1){
		$('MutualProductoSolicitudImporteTotal').disable();
		$('MutualProductoSolicitudCuotas').disable();
	}else{
		$('MutualProductoSolicitudImporteTotal').enable();
		$('MutualProductoSolicitudCuotas').enable();
	}		
	
	CuotaSocial(beneficioSel,cuotaSocDif);
	armaVto(proveedorSel,beneficioSel,fechaSel);



	$('ProductoMutual').observe('change',function(){
		
		var str = $('ProductoMutual').getValue();
		var val = str.split('|');
		var importeFijo = val[4];

		var mensual = val[6];
		var sinCargo = getProveedorId(str,7);
		control_mensual(mensual,importeFijo);
		
		if(sinCargo == 1){
			$('MutualProductoSolicitudImporteTotal').disable();
			$('MutualProductoSolicitudCuotas').disable();
		}else{
			$('MutualProductoSolicitudImporteTotal').enable();
			$('MutualProductoSolicitudCuotas').enable();			
		}		

		

		var dia = $('MutualProductoSolicitudFechaPagoDay').getValue();
		var mes = $('MutualProductoSolicitudFechaPagoMonth').getValue();
		var anio = $('MutualProductoSolicitudFechaPagoYear').getValue();
		var fecha = anio+'-'+mes+'-'+dia;

		var beneficio = $('MutualProductoSolicitudPersonaBeneficioId').getValue();
		CuotaSocial(beneficio,val[5]);
		armaVto(val[3],beneficio,fecha);
								
		//alert(importeFijo);
	});


	$('MutualProductoSolicitudPersonaBeneficioId').observe('change',function(){
		
		var beneficio= $('MutualProductoSolicitudPersonaBeneficioId').getValue();
		var proveedor = getProveedorId($('ProductoMutual').getValue(),3);
		var dia = $('MutualProductoSolicitudFechaPagoDay').getValue();
		var mes = $('MutualProductoSolicitudFechaPagoMonth').getValue();
		var anio = $('MutualProductoSolicitudFechaPagoYear').getValue();
		var fecha = anio+'-'+mes+'-'+dia;
		CuotaSocial(beneficio,getProveedorId($('ProductoMutual').getValue(),5));
		armaVto(proveedor,beneficio,fecha);

		
	});	


	$('MutualProductoSolicitudFechaPagoDay').observe('change',function(){
		var beneficio= $('MutualProductoSolicitudPersonaBeneficioId').getValue();
		var proveedor = getProveedorId($('ProductoMutual').getValue(),3);
		var dia = $('MutualProductoSolicitudFechaPagoDay').getValue();
		var mes = $('MutualProductoSolicitudFechaPagoMonth').getValue();
		var anio = $('MutualProductoSolicitudFechaPagoYear').getValue();
		var fecha = anio+'-'+mes+'-'+dia;
		armaVto(proveedor,beneficio,fecha);
	});

	$('MutualProductoSolicitudFechaPagoMonth').observe('change',function(){
		var beneficio= $('MutualProductoSolicitudPersonaBeneficioId').getValue();
		var proveedor = getProveedorId($('ProductoMutual').getValue(),3);
		var dia = $('MutualProductoSolicitudFechaPagoDay').getValue();
		var mes = $('MutualProductoSolicitudFechaPagoMonth').getValue();
		var anio = $('MutualProductoSolicitudFechaPagoYear').getValue();
		var fecha = anio+'-'+mes+'-'+dia;
		armaVto(proveedor,beneficio,fecha);
	});	

	$('MutualProductoSolicitudFechaPagoYear').observe('change',function(){
		var beneficio= $('MutualProductoSolicitudPersonaBeneficioId').getValue();
		var proveedor = getProveedorId($('ProductoMutual').getValue(),3);
		var dia = $('MutualProductoSolicitudFechaPagoDay').getValue();
		var mes = $('MutualProductoSolicitudFechaPagoMonth').getValue();
		var anio = $('MutualProductoSolicitudFechaPagoYear').getValue();
		var fecha = anio+'-'+mes+'-'+dia;
		armaVto(proveedor,beneficio,fecha);
	});				

		
});

function getProveedorId(str,idx){
	var val = str.split('|');
	return val[idx];
}

function armaVto(proveedor,beneficio,fechaCarga){
	new Ajax.Updater('datos_inicio_vencimiento','<?php echo $this->base?>/proveedores/proveedor_vencimientos/arma_vencimiento/'+ proveedor + '/' + beneficio +'/' + fechaCarga, {asynchronous:true, evalScripts:true, onComplete:function(request, json) {$('spinner').hide();$('btn_submit').enable();},onLoading:function(request) {Element.show('spinner');$('btn_submit').disable();}, requestHeaders:['X-Update', 'datos_inicio_vencimiento']});
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


function control_mensual(mensual,importeFijo){

	if(mensual != 0){
	
		document.getElementById('MutualProductoSolicitudImporteTotal').value = importeFijo;
		if(importeFijo > 0)document.getElementById('MutualProductoSolicitudConImporteFijo').value = 1;
		else document.getElementById('MutualProductoSolicitudConImporteFijo').value = 0;
		document.getElementById('MutualProductoSolicitudCuotas').value = 0;
		document.getElementById('MutualProductoSolicitudPermanente').value = 1;
		if(importeFijo > 0)$('MutualProductoSolicitudImporteTotal').disable();
		else $('MutualProductoSolicitudImporteTotal').enable();
		$('MutualProductoSolicitudCuotas').hide();
		$('textoImporteTotal').update("IMPORTE MENSUAL");
		$('textoCuotas').update('');
		
	}else{
		
		document.getElementById('MutualProductoSolicitudImporteTotal').value = '';
		document.getElementById('MutualProductoSolicitudCuotas').value = '';
		$('MutualProductoSolicitudImporteTotal').enable();
		$('MutualProductoSolicitudCuotas').show();
		$('textoImporteTotal').update("IMPORTE TOTAL");
		$('textoCuotas').update('CUOTAS');
		document.getElementById('MutualProductoSolicitudPermanente').value = 0;
		document.getElementById('MutualProductoSolicitudConImporteFijo').value = 0;
		
	}				
}

function validateForm(){
	$('btn_submit').disable();
	var conImporteFijo = $('MutualProductoSolicitudConImporteFijo').getValue();
//	var mensual = $('MutualProductoSolicitudPermanente').getValue();
	var mensual = getProveedorId($('ProductoMutual').getValue(),6);

	var sinCargo = getProveedorId($('ProductoMutual').getValue(),7);
	
	var prestamo = getProveedorId($('ProductoMutual').getValue(),8);

	document.getElementById('MutualProductoSolicitudPermanente').value = mensual;
	document.getElementById('MutualProductoSolicitudPrestamo').value = prestamo;
	
	if(sinCargo == 1){
		if(confirm("Generar una Solicitud sin CARGO?")){
			document.getElementById('MutualProductoSolicitudSinCargo').value = 1;
			document.getElementById('MutualProductoSolicitudPermanente').value = 0;
			document.getElementById('MutualProductoSolicitudCuotas').value = 0;
                        
			return true;
		}else{
                    $('btn_submit').enable();
			return false;
		}		
	}	
	
	if(conImporteFijo == 1 && mensual == 1)return true;
	
	if(conImporteFijo == 0 && mensual == 1){
		
		if($('MutualProductoSolicitudImporteTotal').getValue() == 0){
			alert('Debe indicar el Importe Mensual!');
			$('MutualProductoSolicitudImporteTotal').focus();
                        $('btn_submit').enable();
			return false;
		}	
	}

	if(conImporteFijo == 0 && mensual == 0){
		
		if($('MutualProductoSolicitudImporteTotal').getValue() == 0){
			alert('Debe indicar el Importe Total!');
			$('MutualProductoSolicitudImporteTotal').focus();
                        $('btn_submit').enable();
			return false;
		}
		if($('MutualProductoSolicitudCuotas').getValue() == ''){
			alert('Debe indicar la Cantidad de Cuotas!');
			$('MutualProductoSolicitudCuotas').focus();
                        $('btn_submit').enable();
			return false;
		}
		
	}	
	
	return true;
}

</script>

<h3>NUEVA ORDEN DE COMPRA</h3>

<?php echo $this->renderElement('mutual_producto_solicitudes/menu',array('persona' => $persona,'plugin'=>'mutual'))?>

<?php //   debug($persona)?>
<?php if(count($persona['PersonaBeneficio']) != 0):?>

	<?php echo $frm->create(null,array('action' => 'add/'.$persona['Persona']['id'],'id' => 'NuevaOrdenProductoMutual','onsubmit' => "return validateForm();"))?>
	
	<div class="areaDatoForm">


		<table class="tbl_form">
		
			<tr>
				<td><?php echo $frm->input('fecha',array('dateFormat' => 'DMY','label'=>'FECHA DE CARGA','minYear'=>'1980', 'maxYear' => date("Y")))?></td>
				<td><?php echo $frm->input('fecha_pago',array('dateFormat' => 'DMY','label'=>'FECHA DE PAGO','minYear'=>'1980', 'maxYear' => date("Y") + 1))?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo $this->requestAction('/mutual/mutual_productos/combo')?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo $frm->input('nro_referencia_proveedor',array('label'=>'NRO REFERENCIA PROVEEDOR')) ?></td>
			</tr>
		
		</table>

		<div style="clear: both;"></div>
	
	</div>
	
	<div class="areaDatoForm">	
		<h3>DATOS PARA DESCUENTO</h3>
		<table class="tbl_form">
			<tr>
				<td>
					<div class="input text">
						<label for="MutualProductoSolicitudImporteTotal"><span id="textoImporteTotal">IMPORTE TOTAL</span></label>
						<input name="data[MutualProductoSolicitud][importe_total]" type="text" value="" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true,false)" id="MutualProductoSolicitudImporteTotal" />
					</div>				
				</td>
				<td>
					<div class="input text">
						<label for="MutualProductoSolicitudCuotas"><span id="textoCuotas">CUOTAS</span></label>
						<input name="data[MutualProductoSolicitud][cuotas]" type="text" size="5" maxlenght="5" class="input_number" onkeypress="return soloNumeros(event)" maxlength="11" value="" id="MutualProductoSolicitudCuotas" />
					</div>					
				</td>
				<td></td>
			</tr>
		</table>
		<table class="tbl_form">
			<tr>
				<td colspan="2"><?php echo $this->requestAction('/pfyj/persona_beneficios/combo/MutualProductoSolicitud/'.$persona['Persona']['id'])?></td>
			</tr>
		</table>
		<div class="row">
			<?php //   echo $frm->periodo('MutualProductoSolicitud.periodo_ini','INICIA EN',null,date('Y')-1,date('Y') + 1)?>
		</div>
		
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
			<?php echo $frm->textarea('MutualProductoSolicitud.observaciones',array('cols' => 60, 'rows' => 10))?>
		</div>
		
		<div style="clear: both;"></div>
			
	</div>	
	
	<div style="clear: both;"></div>
        
        <?php echo $this->renderElement('mutual_producto_solicitudes/info_operaciones_pendientes_by_persona', array(
           'plugin' => 'mutual',
            'persona_id' => $persona['Persona']['id'],
        ));?>
    
	
	<?php echo $frm->hidden('persona_id',array('value' => $persona['Persona']['id']))?>
	<?php echo $frm->hidden('socio_id',array('value' => (isset($persona['Socio']['id']) ? $persona['Socio']['id'] : 0)))?>
	<?php echo $frm->hidden('permanente')?>
	<?php echo $frm->hidden('sin_cargo',array('value' => 0))?>
	<?php echo $frm->hidden('con_importe_fijo')?>
	<?php echo $frm->hidden('prestamo')?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/mutual/mutual_producto_solicitudes/by_persona/".$persona['Persona']['id'] : $fwrd) ))?>
	
<?php else:?>

		<div class='notices_error'>NO POSEE BENEFICIOS CARGADOS!</div>
		<div class="actions"><?php echo $controles->botonGenerico('/pfyj/persona_beneficios/add/'.$persona['Persona']['id'],'controles/add.png','Cargar Beneficio')?></div>

<?php endif;?>		