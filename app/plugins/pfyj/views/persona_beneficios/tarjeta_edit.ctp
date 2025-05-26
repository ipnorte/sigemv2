<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	//$('btn_submit').disable();
});

function validateFormEdit(){
	var ret = validarTarjetaRequired(3);
	if(!ret){return false;}
	return confirm('Actualizar Tarjeta de Débito');
}
</script>

<div class="areaDatoForm2">
	ORGANISMO: <strong><?php echo $beneficio['PersonaBeneficio']['codigo_beneficio_desc']?></strong>
	<br/>
	BENEFICIO: <strong><?php echo $beneficio['PersonaBeneficio']['string']?></strong>
	<br/>
	PORCENTAJE: <strong><?php echo $util->nf($beneficio['PersonaBeneficio']['porcentaje'])?> %</strong>	
</div>
<h3>Nueva Tarjeta de Débito</h3>
<?php echo $form->create(null,array('name'=>'formEditTarjeta','id'=>'formEditTarjeta','onsubmit' => "return validateFormEdit()",'action' => 'tarjeta_edit/'. $beneficio['PersonaBeneficio']['id']));?>
<div class="areaDatoForm">
	<?php echo $this->renderElement('persona_beneficios/tarjeta_debito_form',array('plugin' => 'pfyj'))?>
	<?php echo $frm->hidden('PersonaBeneficio.id',array('value' => $beneficio['PersonaBeneficio']['id'])); ?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/pfyj/persona_beneficios/index/".$beneficio['PersonaBeneficio']['persona_id'] : $fwrd) ))?> 
</div>


