<?php //   echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>
<?php echo $this->renderElement('personas/menu_inicial',array('plugin' => 'pfyj'))?>
<script type="text/javascript">
	function validateForm(){
		
//		if(!validRequired('PersonaDocumento','')) return false;
//		if(!validRequired('PersonaApellido','')) return false;
//		if(!validRequired('PersonaNombre','')) return false;
//		if(!controlCUIT($('PersonaCuitCuil').getValue(),'PersonaCuitCuil','')) return false;
//		document.getElementById('PersonaDocumento').value = rellenar($('PersonaDocumento').getValue(),'0',8,'L');
//		if(!cuit_ndoc('PersonaDocumento','PersonaCuitCuil','')) return false;
//		if(!validRequired('PersonaTelefonoFijo','')) return false;
//		if(!validRequired('PersonaCalle','')) return false;
//		if(!validRequired('PersonaNumeroCalle','')) return false;
//		if(!validRequired('PersonaLocalidadAproxima','')) return false;
//		if(!validRequired('PersonaCodigoPostal','')) return false;
		return true;
	}
</script>

<?php echo $form->create(null,array('name'=>'formAddPersona','id'=>'formAddPersona','onsubmit' => "return validateForm()" ));?>

<h3>ALTA NUEVA PERSONA</h3>
			<div class="areaDatoForm">
                            
<?php echo $this->renderElement('personas/forms/datos_personales_form',
        array(
            'persona' => $this->data['Persona'],
            'plugin' => 'pfyj',
        ));
?> 
<?php echo $this->renderElement('personas/forms/domicilio_form',array('persona' => $this->data['Persona'],'plugin' => 'pfyj'))?>
<?php echo $this->renderElement('personas/forms/contacto_form',array('persona' => $this->data['Persona'],'plugin' => 'pfyj'))?>                            
<?php echo $frm->hidden('Persona.idr_persona',array('value' => 0)); ?>
<?php echo $frm->hidden('Persona.fallecida',array('value' => 0)); ?>
<?php echo $frm->hidden('Persona.id',array('value' => 0)); ?>                            
</div>    
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => '*** DAR DE ALTA LA PERSONA ***','URL' => '/pfyj/personas'))?>