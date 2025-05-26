<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>

<h3>ORDENES DE SERVICIOS</h3>

<div class="actions"><?php echo $controles->botonGenerico('/pfyj/socio_servicio_solicitudes/add/'.$persona['Persona']['id'],'controles/cart_add.png','Nueva Orden de Servicio')?></div>
