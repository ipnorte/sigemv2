<?php 
$tabs = array(
				0 => array('url' => '/proveedores/proveedor_listados/iva_compra/','label' => 'IVA COMPRA', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/proveedores/proveedor_listados/saldo_a_fecha/','label' => 'SALDO A FECHA', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/proveedores/proveedor_listados/listado_tipo_asiento/','label' => 'LISTADO TIPO ASIENTOS', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
				3 => array('url' => '/proveedores/proveedor_listados/listado_concepto_gasto/','label' => 'LISTADO CONCEPTO GASTOS', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
//				4 => array('url' => '/proveedores/proveedor_listados/listado_centro_costo/','label' => 'CENTRO COSTO', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),

    );
echo $cssMenu->menuTabs($tabs);			
?>