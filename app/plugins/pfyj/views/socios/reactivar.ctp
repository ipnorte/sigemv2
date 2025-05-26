<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>
<h3>PASAR A VIGENTE AL SOCIO</h3>
<script type="text/javascript">

	function confirmarForm(){
		var msgConfirm = "ATENCION!\n\n";
		msgConfirm = msgConfirm + "PASAR A VIGENTE AL SOCIO ";
		msgConfirm = msgConfirm + "A PARTIR DE: " + getStrPeriodo('SocioPeriodoIni') + "\n";
		msgConfirm = msgConfirm + "\n\n";
		return confirm(msgConfirm);
	}
	
</script>
<?php echo $form->create(null,array('name'=>'formAltaDirectaSocio','id'=>'formAltaDirectaSocio','onsubmit' => "return confirmarForm()",'action' => 'reactivar/'. $socio['Socio']['id']));?>
<div class="areaDatoForm">

	<table class="tbl_form">
		<tr>
			<td>PERIODO DE INICIO</td>
			<td><?php echo $frm->periodo('Socio.periodo_ini',null,null,date('Y') - 1,date('Y')+1)?></td>
		</tr>		 
	
	</table>

</div>
<?php echo $frm->hidden('Socio.id',array('value' => $socio['Socio']['id'])); ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'PROCESAR','URL' => ( empty($fwrd) ? "/pfyj/socios/index/".$socio['Socio']['persona_id'] : $fwrd) ))?>
