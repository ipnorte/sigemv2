<?php echo $this->renderElement('proveedor/padron_header',array('proveedor' => $proveedor))?>
<h3>ADMINISTRACION DE PRODUCTOS</h3>

<div class="actions"><?php echo $controles->botonGenerico('/proveedores/proveedores/nuevo_producto/'.$proveedor['Proveedor']['id'],'controles/add.png','NUEVO PRODUCTO')?></div>
