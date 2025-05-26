

<?php echo $this->renderElement('personas/menu_inicial',array('plugin' => 'pfyj'))?>

<?php 
//$tabs = array(
//				0 => array('url' => '/pfyj/personas/add','label' => 'Nueva Persona', 'icon' => 'controles/add.png','atributos' => array(), 'confirm' => null),
//				1 => array('url' => '/pfyj/personas/imprimir_padron','label' => 'Imprimir Padron', 'icon' => 'controles/pdf.png','atributos' => array(), 'confirm' => null),
//                                2 => array('url' => '/pfyj/personas/consultar_intranet','label' => 'Consultar Deuda en la Intranet', 'icon' => 'controles/information.png','atributos' => array(), 'confirm' => null),
//			);
//echo $cssMenu->menuTabs($tabs);	
?>
<?php echo $this->renderElement('personas/search',array(
														'nro_socio' => true,
														'busquedaAvanzada' => true, 
														'showOnLoad' => (isset($this->data['Persona']['busquedaAvanzada']) && $this->data['Persona']['busquedaAvanzada'] ? true : false),
														'tipo_busqueda_avanzada' => 'by_beneficio',
														
));
echo $this->renderElement(
							'personas/grilla_personas_paginada',
							array(
									'plugin' => 'pfyj',
									'busquedaAvanzada' => true,
									'datos_post' => $this->data
));
?>


