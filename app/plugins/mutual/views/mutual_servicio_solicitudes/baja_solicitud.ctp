<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona,'plugin' => 'pfyj'))?>
<h3>BAJA ORDEN DE SERVICIO</h3>

<?php echo $this->renderElement('mutual_servicio_solicitudes/ficha',array('plugin' => 'mutual','id' => $solicitud['MutualServicioSolicitud']['id']))?>

<?php echo $frm->create(null,array('action' => 'baja_solicitud/'.$solicitud['MutualServicioSolicitud']['id'],'id' => 'BajaOrdenServicio','onsubmit' => "return validateForm();"))?>

<script type="text/javascript">

Event.observe(window, 'load', function(){

	document.getElementById("MutualServicioSolicitudFechaBajaServicioDay").value = "<?php echo date("d",strtotime($fechaCobertura))?>";
	document.getElementById("MutualServicioSolicitudFechaBajaServicioMonth").value = "<?php echo date("m",strtotime($fechaCobertura))?>";
	document.getElementById("MutualServicioSolicitudFechaBajaServicioYear").value = "<?php echo date("Y",strtotime($fechaCobertura))?>";

	$('MutualServicioSolicitudFechaBajaServicioDay').disable();
	$('MutualServicioSolicitudFechaBajaServicioMonth').disable();
	$('MutualServicioSolicitudFechaBajaServicioYear').disable();


	<?php if(date("Ym",strtotime($fechaCobertura)) > $solicitud['MutualServicioSolicitud']['periodo_desde']):?>
	
	document.getElementById("MutualServicioSolicitudPeriodoHastaMonth").value = "<?php echo date("m",strtotime($fechaCobertura))?>";
	document.getElementById("MutualServicioSolicitudPeriodoHastaYear").value = "<?php echo date("Y",strtotime($fechaCobertura))?>";

	<?php else:?>
	document.getElementById("MutualServicioSolicitudPeriodoHastaMonth").value = "<?php echo substr($solicitud['MutualServicioSolicitud']['periodo_desde'],4,2)?>";
	document.getElementById("MutualServicioSolicitudPeriodoHastaYear").value = "<?php echo substr($solicitud['MutualServicioSolicitud']['periodo_desde'],0,4)?>";
	<?php endif;?>

	
//	document.getElementById("MutualServicioSolicitudPeriodoHastaMonth").value = "<?php echo date("m",strtotime($fechaCobertura))?>";
//	document.getElementById("MutualServicioSolicitudPeriodoHastaYear").value = "<?php echo date("Y",strtotime($fechaCobertura))?>";

	$('MutualServicioSolicitudPeriodoHastaMonth').disable();
	$('MutualServicioSolicitudPeriodoHastaYear').disable();
	
});

function validateForm(){
	var msgConfirm = "ATENCION!\n\n *** BAJA DEL SERVICIO ***\n\n";
	msgConfirm = msgConfirm + "COBERTURA HASTA EL : " + getStrFecha("MutualServicioSolicitudFechaBajaServicio") + "\n";
	msgConfirm = msgConfirm + "LIQUIDAR HASTA: " + getStrPeriodo("MutualServicioSolicitudPeriodoHasta") + "\n";

	<?php if(!empty($solicitud['MutualServicioSolicitudAdicional'])):?>

		msgConfirm = msgConfirm + "\nADICIONALES: \n";
	
		<?php foreach($solicitud['MutualServicioSolicitudAdicional'] as $adicional):?>

		msgConfirm = msgConfirm + "* <?php echo $adicional['adicional_tdoc_ndoc_apenom']?>\n";		

		<?php endforeach;?>

	<?php endif;?>
	return confirm(msgConfirm);
}

</script>


<div class="areaDatoForm">
	<table class="tbl_form">

		<tr>
			<td>COBERTURA HASTA EL</td><td><?php echo $frm->input('MutualServicioSolicitud.fecha_baja_servicio',array('dateFormat' => 'DMY','value'=> $fechaCobertura,'minYear' => date('Y'), 'maxYear' => date("Y") + 1))?></td>
		</tr>
		<tr>
			<td>LIQUIDAR HASTA</td>
			<td><?php echo $frm->periodo('MutualServicioSolicitud.periodo_hasta',null,date('Y')."-".date('m')."-01",date('Y'),date('Y')+1)?></td>
		</tr>
		<tr>
			<td>OBSERVACIONES</td>
			<td>
			<?php echo $frm->textarea('MutualServicioSolicitud.observaciones',array('cols' => 60, 'rows' => 10,'value' => $solicitud['MutualServicioSolicitud']['observaciones']))?>
			</td>
		</tr>
	</table>
	

</div>

<div class="notices_error" style="width: 100%;">RECUERDE HACER <strong>FIRMAR LA SOLICITUD DE BAJA</strong> UNA VEZ PROCESADO EL PRESENTE FORMULARIO!!</div>
<div ></div>
<?php echo $frm->hidden('MutualServicioSolicitud.fecha_baja_servicio',array('value' => $fechaCobertura)); ?>
<?php echo $frm->hidden('MutualServicioSolicitud.periodo_hasta',array('value' => date("Ym",strtotime($fechaCobertura)))); ?>
<?php echo $frm->hidden('MutualServicioSolicitud.mutual_servicio_id',array('value' => $solicitud['MutualServicioSolicitud']['id'])); ?>
<?php echo $frm->hidden('MutualServicioSolicitud.persona_id',array('value' => $solicitud['MutualServicioSolicitud']['persona_id'])); ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/mutual/mutual_servicio_solicitudes/index/'.$persona['Persona']['id']))?>

<?php //   debug($solicitud)?>