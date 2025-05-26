<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'SOLICITUDES'))?>
<?php 
$tabs = array(
				0 => array('url' => '/ventas/solicitudes/search','label' => 'Busqueda y Consulta', 'icon' => 'controles/search.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/ventas/solicitudes/alta','label' => 'Emitir Nueva Solicitud', 'icon' => 'controles/cart_put.png','atributos' => array(), 'confirm' => null),
                                2 => array('url' => '/ventas/solicitudes/estado_cuenta','label' => 'Estado de Cuenta', 'icon' => 'controles/report.png','atributos' => array(), 'confirm' => null),
                                3 => array('url' => '/ventas/solicitudes/consultar_intranet','label' => 'Consultar Intranet', 'icon' => 'controles/information.png','atributos' => array(), 'confirm' => null),
//                                3 => array('url' => '/ventas/vendedores/listado_remitos_vendedor','label' => 'Constancias Presentacion', 'icon' => 'controles/pdf.png','atributos' => array(), 'confirm' => null),
                                4 => array('url' => '/ventas/vendedores/listados/NULL/1','label' => 'Listados', 'icon' => 'controles/ms_excel.png','atributos' => array(), 'confirm' => null),    
			);
echo $cssMenu->menuTabs($tabs);	
?>