<?php echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'))?>
<?php // echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'))?>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<?php
echo $this->renderElement('orden_pago/edit_orden_pago',array('plugin' => 'proveedores','aOrdenPago' => $aOrdenPago));
?>
