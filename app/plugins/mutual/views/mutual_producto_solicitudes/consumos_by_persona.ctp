<?php if($menuPersonas == 1) echo $this->renderElement('personas/padron_header',array('persona' => $persona,'plugin'=>'pfyj'))?>

<h3>SOLICITUD DE PRODUCTOS</h3>

<div class="actions">
<?php echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/nuevo_credito/'.$persona['Persona']['id'],'controles/cart_add.png','SOLICITAR PRODUCTO')?>
</div>

<?php echo $this->renderElement('mutual_producto_solicitudes/grilla_creditos',array('solicitudes'=>$solicitudes,'edit' => true,'plugin' => 'mutual'))?>