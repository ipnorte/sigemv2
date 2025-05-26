<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>

<?php if(count($persona['PersonaBeneficio']) != 0):?>


	<?php echo $form->create(null,array('action' => 'edit/'. $persona['Persona']['id']));?>
	
	<?php echo $this->requestAction('/pfyj/socios/view/'.$this->data['Socio']['id'])?>
	
	<div style="clear: both;"></div>
	
	<div class="areaDatoForm">
	
		<h3>MODIFICAR BENEFICIO ASIGNADO A LA CUOTA SOCIAL</h3>
		
		
		<div class="row">
			<?php echo $this->requestAction('/pfyj/persona_beneficios/combo/SocioSolicitud/'.$persona['Persona']['id'])?>
			<div class="ayudaCampoForm">Beneficio por el cual se descuenta la CUOTA SOCIAL</div>
		</div>
		<div style="clear: both;"></div>
	</div>
	<?php echo $frm->hidden('Socio.persona_id',array('value' => $persona['Persona']['id'])); ?>
	<?php echo $frm->hidden('Socio.id'); ?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/pfyj/socios/index/".$persona['Persona']['id'] : $fwrd) ))?>


<?php else:?>

		<div class='notices_error'>NO TIENE BENEFICIOS ACTIVOS</div>
		<div class="actions"><?php echo $controles->botonGenerico('/pfyj/persona_beneficios/add/'.$persona['Persona']['id'],'controles/chart_organisation.png','Cargar Beneficio')?></div>

<?php endif;?>	