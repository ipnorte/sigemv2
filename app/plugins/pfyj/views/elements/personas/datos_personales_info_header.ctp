<?php 
//traigo la persona
$persona = $this->requestAction('/pfyj/personas/get_persona/'.$persona_id);
echo $this->renderElement('personas/tdoc_apenom',array('persona' => $persona, 'plugin' => 'pfyj'));
?>