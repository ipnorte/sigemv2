<?php $persona = $this->requestAction('/pfyj/personas/get_persona/'.$persona_id);?>
<?php echo $this->renderElement('personas/apenom_link_padron',array('persona' => $persona, 'plugin' => 'pfyj'))?>
