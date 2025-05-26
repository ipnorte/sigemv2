<?php echo $this->renderElement('personas/padron_header',array('persona' => $personaSigem))?>
<?php echo $this->renderElement('mutual_producto_solicitudes/menu',array('persona' => $personaSigem,'plugin'=>'mutual'))?>
<?php echo $this->renderElement('solicitudes/add_recibo',array('plugin' => 'v1','persona' => $persona, 'solicitud' => $solicitud, 'persona_id' => $personaSigem['Persona']['id'], 'cancelaciones' => $cancelaciones, 'rows' => $rows))?>
