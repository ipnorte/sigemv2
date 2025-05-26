<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>

<?php echo $this->renderElement('personas/datos_personales',array('persona_id'=>$persona['Persona']['id'],'plugin' => 'pfyj'))?>
<?php //echo $this->renderElement('personas/consulta_bcra',array('cuit'=> $persona['Persona']['cuit_cuil'],'plugin' => 'pfyj','historico' => TRUE, 'afip' => FALSE))?>
<?php // echo $this->renderElement('persona_beneficios/beneficios_by_persona',array('persona_id'=>$persona['Persona']['id'],'plugin' => 'pfyj'))?>
<?php echo $this->renderElement('persona_novedades/novedades_by_persona',array('persona_id'=>$persona['Persona']['id'],'plugin' => 'pfyj'))?>

<?php if(!empty($persona['Socio']['id'])):?>
<?php //   echo $this->requestAction('/pfyj/socios/view/'.$persona['Socio']['id'])?>
<?php endif;?>
<?php //   debug($persona)?> 
