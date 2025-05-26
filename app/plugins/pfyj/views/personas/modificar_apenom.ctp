<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>
<h3>DATOS PERSONALES</h3>
<script type="text/javascript">
	function validateForm(){
		
		if(!validRequired('PersonaApellido','')) return false;
		if(!validRequired('PersonaNombre','')) return false;
		if(!controlCUIT($('PersonaCuitCuil').getValue(),'PersonaCuitCuil','')) return false;
		if(!cuit_ndoc('PersonaDocumento','PersonaCuitCuil','')) return false;
		return true;
	}
</script>
<?php echo $form->create(null,array('action' => 'modificar_apenom/'.$this->data['Persona']['id'],'name'=>'formAddPersona','id'=>'formAddPersona','onsubmit' => "return validateForm()" ));?>

<div class="areaDatoForm">
<h4>Actualización de Datos Personales Criticos</h4>
<hr/>
<table class="tbl_form">
		<tr>
			<td><?php echo $this->requestAction('/config/global_datos/combo/Tipo Documento/Persona.tipo_documento/PERSTPDC/1/0/'.$this->data['Persona']['tipo_documento']);?></td>
			<td><?php echo $frm->input('Persona.documento',array('label'=>'Documento','size'=>'15','maxlength'=>'11','disabled'=>'disabled'));?></td>
                        <td><?php echo $frm->number('Persona.cuit_cuil',array('label'=>'CUIT/CUIL *','size'=>12,'maxlength'=>11)); ?></td>
		</tr>
</table>                
<table class="tbl_form">                
		<tr>
			<td><?php echo $frm->input('Persona.apellido',array('label'=>'Apellido *','size'=>40,'maxlength'=>100)); ?></td>
			<td><?php echo $frm->input('Persona.nombre',array('label'=>'Nombres *','size'=>40,'maxlength'=>100)); ?></td>
		</tr>
				

</table>
</div>
			<?php echo $frm->hidden('Persona.id'); ?>
<?php if($this->data['Persona']['fallecida'] == 0) echo $frm->btnGuardarCancelar(array('URL' => '/pfyj/personas/edit/'.$this->data['Persona']['id']))?>