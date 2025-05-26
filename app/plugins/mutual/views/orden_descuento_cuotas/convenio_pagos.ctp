<?php
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>

<h3>CONVENIOS DE PAGO DEL SOCIO</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<div class="actions"><?php echo $controles->botonGenerico('add/'.$socio['Socio']['id'],'controles/add.png','Nuevo Convenio de Pago')?></div>

<?php if(!empty($convenios)):?>
<?php debug($convenios)?>
<?php else:?>
	<h4>SIN CONVENIOS DE PAGO</h4>
<?php endif;?>