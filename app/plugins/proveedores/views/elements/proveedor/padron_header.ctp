<h1>PADRON DE PROVEEDORES</h1>
<div class="areaDatoForm">
<h3 style="font-family: verdana;"><?php echo $proveedor['Proveedor']['cuit']?> - <?php echo $proveedor['Proveedor']['razon_social']?></h3>
</div>
<?php 
$tabs = array(
				1 => array('url' => '/proveedores/proveedores/edit/'.$proveedor['Proveedor']['id'],'label' => 'Datos Proveedor', 'icon' => 'controles/edit.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/proveedores/proveedores/comision_cobranza/'.$proveedor['Proveedor']['id'],'label' => 'Comisiones Cobranza', 'icon' => 'controles/calculator.png','atributos' => array(), 'confirm' => null),
				3 => array('url' => '/proveedores/proveedor_planes/index/'.$proveedor['Proveedor']['id'],'label' => 'Planes / Productos', 'icon' => 'controles/calculator.png','atributos' => array(), 'confirm' => null),
                                5 => array('url' => '/proveedores/proveedores/prioridad_imputacion/'.$proveedor['Proveedor']['id'],'label' => 'Prioridad Imputacion Cobranza', 'icon' => 'controles/money.png','atributos' => array(), 'confirm' => null),
				10 => array('url' => '/proveedores/proveedores','label' => 'Otro', 'icon' => 'controles/reload3.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs);			
?>
