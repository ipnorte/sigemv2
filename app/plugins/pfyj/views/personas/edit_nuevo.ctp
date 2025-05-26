<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>

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

<h3>DATOS PERSONALES</h3>
<?php if($this->data['Persona']['fallecida'] == 1):?>
<div class="notices_error">PERSONA REGISTRADA COMO FALLECIDA EL <?php echo $util->armaFecha($this->data['Persona']['fecha_fallecimiento'])?></div>
<?php endif;?>    

<div class="areaDatoForm">
<?php echo $this->renderElement('personas/forms/datos_personales_form',
        array(
            'persona' => $this->data['Persona'],
            'plugin' => 'pfyj',
            'disabled' => array(
                'Persona.cuit_cuil',
                'Persona.documento',
                'Persona.apellido',
                'Persona.nombre'
            ),
            'apenom' => !$this->data['Persona']['fallecida'],
        ));
?> 
<?php echo $this->renderElement('personas/forms/domicilio_form',array('persona' => $this->data['Persona'],'plugin' => 'pfyj'))?>
<?php echo $this->renderElement('personas/forms/contacto_form',array('persona' => $this->data['Persona'],'plugin' => 'pfyj'))?>                            
</div>    
<?php if($this->data['Persona']['fallecida'] == 0) echo $frm->btnGuardarCancelar(array('URL' => '/pfyj/personas'))?>