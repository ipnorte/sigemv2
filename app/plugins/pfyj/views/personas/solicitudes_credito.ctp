<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>

<h3>SOLICITUDES DE CONSUMO</h3>

<?php echo $this->renderElement('mutual_producto_solicitudes/menu',array('persona' => $persona,'plugin'=>'mutual'))?>

<?php echo $this->renderElement('solicitudes/solicitudes_socio',array('plugin' => 'v1','persona_id' => $persona['Persona']['id']))?>

