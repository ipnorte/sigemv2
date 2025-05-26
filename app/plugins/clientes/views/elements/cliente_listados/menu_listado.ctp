<?php 
$tabs = array(
				0 => array('url' => '/clientes/cliente_listados/iva_venta/','label' => 'IVA VENTA', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/clientes/cliente_listados/saldo_a_fecha/','label' => 'SALDO A FECHA', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/clientes/cliente_listados/listado_tipo_asiento/','label' => 'LISTADO TIPO ASIENTOS', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
				3 => array('url' => '/clientes/cliente_listados/factura_afip/','label' => 'AFIP WEBSERVICE', 'icon' => 'controles/edit.png','atributos' => array(), 'confirm' => null),
//				4 => array('url' => '/clientes/cliente_listados/listado_centro_costo/','label' => 'CENTRO COSTO', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),

    );
echo $cssMenu->menuTabs($tabs);			
?>