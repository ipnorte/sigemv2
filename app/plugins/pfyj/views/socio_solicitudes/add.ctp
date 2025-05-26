<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>


<?php if(count($persona['PersonaBeneficio']) != 0):?>


	<?php echo $form->create(null,array('action' => 'add/'. $persona['Persona']['id']));?>
	
	
	
	<div class="areaDatoForm">
	
		<h3>ALTA DE NUEVO SOCIO :: SOLICITUD DE AFILIACION</h3>
		
		
		<div class="row">
			<?php echo $this->requestAction('/pfyj/persona_beneficios/combo/SocioSolicitud/'.$persona['Persona']['id'])?>
			<div class="ayudaCampoForm">Beneficio por el cual se descuenta la CUOTA SOCIAL</div>
		</div>
		<div class="row">
			<?php echo $frm->input('SocioSolicitud.fecha',array('dateFormat' => 'MY','label'=>'INICIA DTO CUOTA SOCIAL','minYear'=>'1900', 'maxYear' => date("Y") + 1))?>
		</div>
		<div class="row">OBSERVACIONES</div>
		<div class="row">
			<?php echo $frm->textarea('SocioSolicitud.observaciones',array('cols' => 60, 'rows' => 10))?>
		</div>	
		<div style="clear: both;"></div>
	</div>
	<?php echo $frm->hidden('SocioSolicitud.persona_id',array('value' => $persona['Persona']['id'])); ?>
	<?php echo $frm->hidden('SocioSolicitud.tipo_solicitud',array('value' => $tipo)); ?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/pfyj/socios/index/".$persona['Persona']['id'] : $fwrd) ))?>

<?php else:?>

		<div class='notices_error'>NO POSEE BENEFICIOS CARGADOS!</div>
		<div class="actions"><?php echo $controles->botonGenerico('/pfyj/persona_beneficios/add/'.$persona['Persona']['id'],'controles/chart_organisation.png','Cargar Beneficio')?></div>

<?php endif;?>	