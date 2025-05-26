<?php if($persona['Persona']['fallecida'] == 0):?>
<div class="actions">
<?php echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/by_persona/'.$persona['Persona']['id'],'controles/folder-open.png','Consumos')?>    
&nbsp;|&nbsp;    
<?php echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/add/'.$persona['Persona']['id'],'controles/cart_add.png','Nueva Orden de Compra')?>
&nbsp;|&nbsp;
<?php echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/nuevo_credito/'.$persona['Persona']['id'],'controles/cart_add.png','Nuevo Credito')?>

<?php if(isset($_SESSION['MUTUAL_INI']['general']['modulo_ayuda_economica']) && $_SESSION['MUTUAL_INI']['general']['modulo_ayuda_economica'] == 1 && 1==2):?>        
    &nbsp;|&nbsp;
    <?php echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/nuevo_credito/'.$persona['Persona']['id']."/1",'controles/cart_add.png','Ayuda Economica (Res. No1418/03 INAES)')?>
<?php endif;?>
&nbsp;|&nbsp;    
<?php echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/anuladas_by_persona/'.$persona['Persona']['id'],'controles/b_drop.png','Anuladas')?>
&nbsp;|&nbsp;
<?php //$tabs[6] = array('url' => '/pfyj/personas/solicitudes_credito/'.$persona['Persona']['id'],'label' => 'Creditos', 'icon' => 'controles/money.png','atributos' => array(), 'confirm' => null);?>
<?php echo $controles->botonGenerico('/pfyj/personas/solicitudes_credito/'.$persona['Persona']['id'],'controles/viewmag.png','Creditos Anteriores')?>
</div>
<hr>
<?php endif;?>
