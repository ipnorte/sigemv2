<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona,'plugin' => 'pfyj'))?>
<h3>AGREGRAR ADICIONAL A LA ORDEN DE SERVICIO</h3>

<?php echo $this->renderElement('mutual_servicio_solicitudes/ficha',array('plugin' => 'mutual','id' => $solicitud['MutualServicioSolicitud']['id']))?>

<?php echo $frm->create(null,array('action' => 'agregar_adicional/'.$solicitud['MutualServicioSolicitud']['id'],'id' => 'AddAdicionalOrdenServicio','onsubmit' => "return validateForm();"))?>
<?php //   if(!empty($adicionales)):?>

<script type="text/javascript">

var adicionales = <?php echo (!empty($adicionales) ? count($adicionales) : 0 )?>;

Event.observe(window, 'load', function(){


	document.getElementById("MutualServicioSolicitudAdicionalFechaAltaDay").value = "<?php echo date("d",strtotime($fechaCobertura))?>";
	document.getElementById("MutualServicioSolicitudAdicionalFechaAltaMonth").value = "<?php echo date("m",strtotime($fechaCobertura))?>";
	document.getElementById("MutualServicioSolicitudAdicionalFechaAltaYear").value = "<?php echo date("Y",strtotime($fechaCobertura))?>";

	$('MutualServicioSolicitudAdicionalFechaAltaDay').disable();
	$('MutualServicioSolicitudAdicionalFechaAltaMonth').disable();
	$('MutualServicioSolicitudAdicionalFechaAltaYear').disable();

	<?php if(date("Ym",strtotime($fechaCobertura)) > $solicitud['MutualServicioSolicitud']['periodo_desde']):?>
	
	document.getElementById("MutualServicioSolicitudAdicionalPeriodoDesdeMonth").value = "<?php echo date("m",strtotime($fechaCobertura))?>";
	document.getElementById("MutualServicioSolicitudAdicionalPeriodoDesdeYear").value = "<?php echo date("Y",strtotime($fechaCobertura))?>";

	<?php else:?>
	document.getElementById("MutualServicioSolicitudAdicionalPeriodoDesdeMonth").value = "<?php echo substr($solicitud['MutualServicioSolicitud']['periodo_desde'],4,2)?>";
	document.getElementById("MutualServicioSolicitudAdicionalPeriodoDesdeYear").value = "<?php echo substr($solicitud['MutualServicioSolicitud']['periodo_desde'],0,4)?>";
	<?php endif;?>
	
	$('MutualServicioSolicitudAdicionalPeriodoDesdeMonth').disable();
	$('MutualServicioSolicitudAdicionalPeriodoDesdeYear').disable();
	

	<?php if(empty($adicionales)):?>
		$('AddAdicionalOrdenServicio').disable();
		$('btn_submit').disable();
		$("btn_cancel").enable();
		return;
	<?php endif;?>	
	

	
});

function validateForm(){

	var msgConfirm = "ATENCION!\n\n *** AGREGAR ADICIONALES AL SERVICIO ***\n\n";
	var error = true;
	for (i=0; i < adicionales; i++){
		oChkCheck = document.getElementById('chk_' + i);
		if(oChkCheck.checked){
			msgConfirm = msgConfirm + "* " + document.getElementById('adicional_' + i).value + "\n";
			error = false;
		}
	}
	if(error){
		alert("DEBE SELECCIONAR AL MENOS UN ADICIONAL!");
		return false;
	}
	msgConfirm = msgConfirm + "\n";
	msgConfirm = msgConfirm + "FECHA ALTA SERVICIO: " + getStrFecha("MutualServicioSolicitudAdicionalFechaAlta") + "\n";
	msgConfirm = msgConfirm + "LIQUIDAR A PARTIR DE: " + getStrPeriodo("MutualServicioSolicitudAdicionalPeriodoDesde") + "\n";
	return confirm(msgConfirm);

}

</script>

<div class="areaDatoForm">
	
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
			<?php if(empty($adicionales)):?>
				<tr>
					<td colspan="5" style="color: red;">*** TODOS LOS ADICIONALES VINCULADOS AL SOCIO ESTAN INCORPORADOS A ESTA ORDEN DE SERVICIO ***</td>
				</tr>
			<?php endif;?>
		<?php $i = 0;?>	
		<?php foreach($adicionales as $adicional):?>
			<tr id="<?php echo $adicional['SocioAdicional']['id']?>">
				<td>
					<input type="checkbox" id="chk_<?php echo $i?>" name="data[MutualServicioSolicitudAdicional][socio_adicional_id][<?php echo $adicional['SocioAdicional']['id']?>]" value="1" onclick="toggleCell('<?php echo $adicional['SocioAdicional']['id']?>', this)"/>
					<input type="hidden" id="adicional_<?php echo $i?>" value="<?php echo $adicional['SocioAdicional']['tdoc_ndoc_apenom']?>"/>
				</td>
				<td><?php echo $adicional['SocioAdicional']['tdoc_ndoc']?></td>
				<td><?php echo $adicional['SocioAdicional']['apenom']?></td>
				<td align="center"><?php echo $adicional['SocioAdicional']['sexo']?></td>
				<td><?php echo $adicional['SocioAdicional']['vinculo_desc']?></td>
			</tr>
			<?php $i++;?>	
		<?php endforeach;?>
		<tr>
			<td colspan="2">FECHA COBERTURA DESDE</td>
			<td colspan="3"><?php echo $frm->input('MutualServicioSolicitudAdicional.fecha_alta',array('dateFormat' => 'DMY','label'=>'','minYear' => date('Y'), 'maxYear' => date("Y") + 1))?></td>
		</tr>		
		<tr>
			<td colspan="2">LIQUIDAR A PARTIR DE</td>
			<td colspan="3"><?php echo $frm->periodo('MutualServicioSolicitudAdicional.periodo_desde',null,date('Y')."-".date('m')."-01",date('Y'),date('Y')+1)?></td>
		</tr>
		</table>
		<?php if($solicitud['MutualServicioSolicitud']['persona_beneficio_id'] == 0):?>
		<h3>DATOS DEL DESCUENTO</h3>
		<table class="tbl_form">
			<tr>
				<td>BENEFICIO</td>
				<td><?php echo $this->renderElement('persona_beneficios/combo_beneficios',array('plugin' => 'pfyj','persona_id' => $persona['Persona']['id'],'soloActivos' => 1))?></td>
			</tr>		
		
		</table>
		<?php endif;?>
			
	
</div>
<?php echo $frm->hidden('MutualServicioSolicitudAdicional.fecha_alta',array('value' => $fechaCobertura)); ?>
<?php echo $frm->hidden('MutualServicioSolicitudAdicional.periodo_desde',array('value' => date("Ym",strtotime($fechaCobertura))));?>
<?php echo $frm->hidden('MutualServicioSolicitudAdicional.mutual_servicio_solicitud_id',array('value' => $solicitud['MutualServicioSolicitud']['id'])); ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/mutual/mutual_servicio_solicitudes/index/'.$persona['Persona']['id']))?>
<?php //   endif;?>	


<?php //   debug($solicitud)?>