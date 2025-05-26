<?php 
$persona = $this->requestAction('/pfyj/socios/get_persona/'.$socio_id);
echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'link' => true,'plugin' => 'pfyj'))
?>

