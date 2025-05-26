<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>
<h3>GENERAR ALTA DIRECTA DEL SOCIO</h3>
<script type="text/javascript">

	function confirmarForm(){
		var msgConfirm = "ATENCION!\n\n";
		msgConfirm = msgConfirm + "DAR DE ALTA COMO SOCIO A: \n\n";
		msgConfirm = msgConfirm + "<?php echo $util->globalDato($persona['Persona']['tipo_documento'])." ".$persona['Persona']['documento']." - ".$persona['Persona']['apellido'].", ".$persona['Persona']['nombre']?>";
		msgConfirm = msgConfirm + "\n\n";
		msgConfirm = msgConfirm + "FECHA DE ALTA: " + getStrFecha('SocioFechaAlta');
		msgConfirm = msgConfirm + "\n\n";
		msgConfirm = msgConfirm + "A PARTIR DE: " + getStrPeriodo('SocioPeriodoIni');
		msgConfirm = msgConfirm + "\n\n";
		msgConfirm = msgConfirm + "BENEFICIO: " + getTextoSelect('SocioPersonaBeneficioId');
		msgConfirm = msgConfirm + "\n\n";
		return confirm(msgConfirm);
	}
	
</script>
<?php echo $form->create(null,array('name'=>'formAltaDirectaSocio','id'=>'formAltaDirectaSocio','onsubmit' => "return confirmarForm()",'action' => 'alta_directa/'. $persona['Persona']['id']));?>
<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>BENEFICIO</td><td><?php echo $this->renderElement('persona_beneficios/combo_beneficios',array('plugin' => 'pfyj','persona_id' => $persona['Persona']['id'],'soloActivos' => true))?></td>
		</tr>
		<tr>
			<td>FECHA ALTA</td>
			<td><?php echo $frm->input('Socio.fecha_alta',array('dateFormat' => 'DMY','minYear'=> date('Y') - 15, 'maxYear' => date("Y") + 1))?></td>
		</tr>
		<tr>
			<td>PERIODO DE INICIO</td>
			<td><?php echo $frm->periodo('Socio.periodo_ini',null,null,date('Y') - 15,date('Y') + 1)?></td>
		</tr>		 
	
	</table>

</div>
<?php echo $frm->hidden('Socio.id',array('value' => 0)); ?>
<?php echo $frm->hidden('Socio.persona_id',array('value' => $persona['Persona']['id'])); ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'PROCESAR','URL' => ( empty($fwrd) ? "/pfyj/socios/index/".$persona['Persona']['id'] : $fwrd) ))?>
