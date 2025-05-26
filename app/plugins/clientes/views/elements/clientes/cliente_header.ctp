<?php 
$styleUL = "list-style-type: none;border-left: 1px solid #666666;border-bottom:1px solid #666666;height: 36px;";
$styleLI = "float: left;background-color: #e2e6ea;padding: 7px 3px 3px 3px;height: 25px;border-top:1px solid #666666;border-right:1px solid #666666;";
//debug($proveedores);

/*
if($proveedores['Proveedor']['tipo_proveedor']==0):
	$accion = 'factura/'.$proveedores['Proveedor']['id'];
	$label = 'NUEVA FACTURA';
elseif($proveedores['Proveedor']['tipo_proveedor']==1):
	$accion = 'liquidacion/'.$proveedores['Proveedor']['id'];
	$label = 'LIQUIDACIONES';
else:
	$accion = 'comision/'.$proveedores['Proveedor']['id'];
	$label = 'COMISIONES';
endif;
*/
?>

<h1>MOVIMIENTO CLIENTE</h1>
<?php echo $this->renderElement('clientes/datos_cliente',array('cliente'=>$cliente,'plugin' => 'clientes'))?>
<?php //DEBUG($proveedores)?>
<?php 
$tabs = array(
				0 => array('url' => '/clientes/clientes/pendientes/'.$cliente['Cliente']['id'],'label' => 'PENDIENTES', 'icon' => 'controles/page_world.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/clientes/clientes/cta_cte/'.$cliente['Cliente']['id'],'label' => 'CTA.CTE.', 'icon' => 'controles/viewmag.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/clientes/clientes/facturas/'.$cliente['Cliente']['id'],'label' => 'FACTURACION', 'icon' => 'controles/information.png','atributos' => array(), 'confirm' => null),
				3 => array('url' => '/clientes/clientes/recibo/'.$cliente['Cliente']['id'],'label' => 'RECIBO', 'icon' => 'controles/user.png','atributos' => array(), 'confirm' => null),
				4 => array('url' => '/clientes/clientes/compensar_pagos/'.$cliente['Cliente']['id'],'label' => 'COMPENSAR PAGOS', 'icon' => 'controles/zone_money.png','atributos' => array(), 'confirm' => null),
				5 => array('url' => '/clientes/clientes','label' => 'Otro', 'icon' => 'controles/reload3.png','atributos' => array(), 'confirm' => null),
				
			);
			
			
echo $cssMenu->menuTabs($tabs);			


?>