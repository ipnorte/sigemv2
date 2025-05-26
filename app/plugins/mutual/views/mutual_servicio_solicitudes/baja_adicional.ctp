<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona,'plugin' => 'pfyj'))?>
<h3>BAJA DE ADICIONAL DE LA ORDEN DE SERVICIO</h3>

<?php echo $this->renderElement('mutual_servicio_solicitudes/ficha',array('plugin' => 'mutual','id' => $solicitud['MutualServicioSolicitud']['id']))?>

<?php echo $frm->create(null,array('action' => 'baja_adicional/'.$solicitud['MutualServicioSolicitud']['id'].'/'.$adicional['SocioAdicional']['id'].'/'.$solicitud_adicional_id,'id' => 'BajaAdicionalOrdenServicio','onsubmit' => "return validateForm();"))?>

<script type="text/javascript">

Event.observe(window, 'load', function(){


	document.getElementById("MutualServicioSolicitudAdicionalFechaBajaDay").value = "<?php echo date("d",strtotime($fechaCobertura))?>";
	document.getElementById("MutualServicioSolicitudAdicionalFechaBajaMonth").value = "<?php echo date("m",strtotime($fechaCobertura))?>";
	document.getElementById("MutualServicioSolicitudAdicionalFechaBajaYear").value = "<?php echo date("Y",strtotime($fechaCobertura))?>";

	$('MutualServicioSolicitudAdicionalFechaBajaDay').disable();
	$('MutualServicioSolicitudAdicionalFechaBajaMonth').disable();
	$('MutualServicioSolicitudAdicionalFechaBajaYear').disable();


	<?php if(date("Ym",strtotime($fechaCobertura)) > $solicitud['MutualServicioSolicitud']['periodo_desde']):?>
	
	document.getElementById("MutualServicioSolicitudAdicionalPeriodoHastaMonth").value = "<?php echo date("m",strtotime($fechaCobertura))?>";
	document.getElementById("MutualServicioSolicitudAdicionalPeriodoHastaYear").value = "<?php echo date("Y",strtotime($fechaCobertura))?>";

	<?php else:?>
	document.getElementById("MutualServicioSolicitudAdicionalPeriodoHastaMonth").value = "<?php echo substr($solicitud['MutualServicioSolicitud']['periodo_desde'],4,2)?>";
	document.getElementById("MutualServicioSolicitudAdicionalPeriodoHastaYear").value = "<?php echo substr($solicitud['MutualServicioSolicitud']['periodo_desde'],0,4)?>";
	<?php endif;?>
	
	
//	document.getElementById("MutualServicioSolicitudAdicionalPeriodoHastaMonth").value = "<?php echo date("m",strtotime($fechaCobertura))?>";
//	document.getElementById("MutualServicioSolicitudAdicionalPeriodoHastaYear").value = "<?php echo date("Y",strtotime($fechaCobertura))?>";

	$('MutualServicioSolicitudAdicionalPeriodoHastaMonth').disable();
	$('MutualServicioSolicitudAdicionalPeriodoHastaYear').disable();
	

//	$('MutualServicioSolicitudAdicionalBorrarRegistro').observe('click',function(){
//
//		if(document.getElementById("MutualServicioSolicitudAdicionalBorrarRegistro").checked){
//			$('MutualServicioSolicitudAdicionalFechaBajaDay').disable();
//			$('MutualServicioSolicitudAdicionalFechaBajaMonth').disable();
//			$('MutualServicioSolicitudAdicionalFechaBajaYear').disable();
//
//			$('MutualServicioSolicitudAdicionalPeriodoHastaMonth').disable();
//			$('MutualServicioSolicitudAdicionalPeriodoHastaYear').disable();
//			
//		}else{
//			$('MutualServicioSolicitudAdicionalFechaBajaDay').enable();
//			$('MutualServicioSolicitudAdicionalFechaBajaMonth').enable();
//			$('MutualServicioSolicitudAdicionalFechaBajaYear').enable();
//
//			$('MutualServicioSolicitudAdicionalPeriodoHastaMonth').enable();
//			$('MutualServicioSolicitudAdicionalPeriodoHastaYear').enable();			
//		}		
//		
//	});
	
});

function validateForm(){
	var msgConfirm = "ATENCION!\n\n *** DAR DE BAJA ADICIONAL ***\n\n";
	msgConfirm = msgConfirm + $("MutualServicioSolicitudAdicionalApenom").getValue() + "\n";
	if(!document.getElementById("MutualServicioSolicitudAdicionalBorrarRegistro").checked){
		msgConfirm = msgConfirm + "COBERTURA HASTA EL: " + getStrFecha("MutualServicioSolicitudAdicionalFechaBaja") + "\n";
		msgConfirm = msgConfirm + "LIQUIDAR HASTA: " + getStrPeriodo("MutualServicioSolicitudAdicionalPeriodoHasta") + "\n";
	}else{
		msgConfirm = msgConfirm + "*** BORRAR EL ADICIONAL DEL SERVICIO ***";
	}
	return confirm(msgConfirm);
}

</script>

<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>ADICIONAL</td><td><?php echo $frm->input('MutualServicioSolicitudAdicional.apenom',array('value' => $adicional['SocioAdicional']['tdoc_ndoc_apenom'],'disabled' => true,'size' => 50))?></td>
		</tr>
		<tr>
			<td>COBERTURA HASTA EL</td><td><?php echo $frm->input('MutualServicioSolicitudAdicional.fecha_baja',array('dateFormat' => 'DMY','label'=>'','minYear' => date('Y'), 'maxYear' => date("Y") + 1))?></td>
		</tr>		
		<tr>
			<td>LIQUIDAR HASTA</td><td><?php echo $frm->periodo('MutualServicioSolicitudAdicional.periodo_hasta',null,date('Y')."-".date('m')."-01",date('Y'),date('Y')+1)?></td>
		</tr>
		<tr>
			<td>OBSERVACIONES</td>
			<td>
			<?php echo $frm->textarea('MutualServicioSolicitudAdicional.observaciones',array('cols' => 60, 'rows' => 10))?>
			</td>
		</tr>				
		<tr>
			<td>QUITAR DE LA SOLICITUD</td><td><input type="checkbox" name="data[MutualServicioSolicitudAdicional][borrar_registro]" id="MutualServicioSolicitudAdicionalBorrarRegistro" value="1"/></td>
		</tr>	
	</table>

</div>
<?php echo $frm->hidden('MutualServicioSolicitudAdicional.id',array('value' => $solicitud_adicional_id)); ?>
<?php echo $frm->hidden('MutualServicioSolicitudAdicional.fecha_baja',array('value' => $fechaCobertura)); ?>
<?php echo $frm->hidden('MutualServicioSolicitudAdicional.periodo_hasta',array('value' => date("Ym",strtotime($fechaCobertura)))); ?>
<?php echo $frm->hidden('MutualServicioSolicitudAdicional.mutual_servicio_solicitud_id',array('value' => $solicitud['MutualServicioSolicitud']['id'])); ?>
<?php echo $frm->hidden('MutualServicioSolicitudAdicional.socio_adicional_id',array('value' => $adicional['SocioAdicional']['id'])); ?>
<?php echo $frm->hidden('MutualServicioSolicitudAdicional.persona_id',array('value' => $persona['Persona']['id'])); ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/mutual/mutual_servicio_solicitudes/index/'.$persona['Persona']['id']))?>

