<?php 
$styleUL = "list-style-type: none;border-left: 1px solid #666666;border-bottom:1px solid #666666;height: 36px;";
$styleLI = "float: left;background-color: #e2e6ea;padding: 7px 3px 3px 3px;height: 25px;border-top:1px solid #666666;border-right:1px solid #666666;";
?>
<?php 
	if(empty($persona)):
		$this->error(404, 'AVISO DEL SISTEMA', 'El recurso solicitado no esta disponible');
		exit;
	endif;
	
	$INI_FILE = $_SESSION['MUTUAL_INI'];
	$MOD_SIISA = (isset($INI_FILE['general']['modulo_siisa']) && $INI_FILE['general']['modulo_siisa'] != 0 ? TRUE : FALSE);
	
	
?>	
<h1>PADRON DE PERSONAS / SOCIOS</h1>
<?php echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'plugin' => 'pfyj'))?>

<?php 
//Configure::read('APLICACION.nombre')
$tabs = array(
				0 => array('url' => '/pfyj/personas/view/'.$persona['Persona']['id'],'label' => 'Resumen', 'icon' => 'controles/viewmag.png','atributos' => array(), 'confirm' => null),
//				1 => array('url' => '/pfyj/personas/bcra/'.$persona['Persona']['id'],'label' => 'BCRA', 'icon' => 'controles/information.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/pfyj/personas/edit/'.$persona['Persona']['id'],'label' => 'Datos Personales', 'icon' => 'controles/user.png','atributos' => array(), 'confirm' => null),
				3 => array('url' => '/pfyj/persona_beneficios/index/'.$persona['Persona']['id'],'label' => 'Beneficios', 'icon' => 'controles/chart_organisation.png','atributos' => array(), 'confirm' => null),
				4 => array('url' => '/pfyj/socios/index/'.$persona['Persona']['id'],'label' => 'Socio', 'icon' => 'controles/medal_gold_1.png','atributos' => array(), 'confirm' => null),
				5 => (!empty($persona['Socio']) && !empty($persona['Socio']['id']) ? array('url' => '/pfyj/socio_adicionales/index/'.$persona['Persona']['id'],'label' => 'Adicionales', 'icon' => 'controles/user_add.png','atributos' => array(), 'confirm' => null) : array()),
//                6 => (!empty($persona['Socio']) && !empty($persona['Socio']['id']) ? array('url' => '/mutual/liquidacion_socios/cargar_scoring_by_socio/'.$persona['Socio']['id'],'label' => 'Scoring', 'icon' => 'controles/calendar_2.png','atributos' => array(), 'confirm' => null) : array()),
// 				6 => array('url' => '/pfyj/personas/solicitudes_credito/'.$persona['Persona']['id'],'label' => 'Creditos', 'icon' => 'controles/money.png','atributos' => array(), 'confirm' => null),
				7 => array('url' => '/mutual/mutual_producto_solicitudes/by_persona/'.$persona['Persona']['id'].'/1','label' => 'Consumos', 'icon' => 'controles/cart_put.png','atributos' => array(), 'confirm' => null),
// 				8 => array('url' => '/mutual/mutual_producto_solicitudes/consumos_by_persona/'.$persona['Persona']['id'].'/1','label' => 'Productos', 'icon' => 'controles/cart_put.png','atributos' => array(), 'confirm' => null),
				9 => (!empty($persona['Socio']) && !empty($persona['Socio']['id']) ? array('url' => '/mutual/mutual_servicio_solicitudes/index/'.$persona['Persona']['id'].'/1','label' => 'Servicios', 'icon' => 'controles/vcard.png','atributos' => array(), 'confirm' => null) : array()),
				10 => (!empty($persona['Socio']) && !empty($persona['Socio']['id']) ? array('url' => '/mutual/orden_descuento_cuotas/estado_cuenta2/'.$persona['Socio']['id'].'/1','label' => 'Estado de Cuenta', 'icon' => 'controles/calculator.png','atributos' => array(), 'confirm' => null): array() ),
				11 => array('url' => '/pfyj/persona_novedades/index/'.$persona['Persona']['id'],'label' => 'Novedades', 'icon' => 'controles/note.png','atributos' => array(), 'confirm' => null),
                12 => array('url' => '/pfyj/personas/consultar_intranet/'. $persona['Persona']['id'],'label' => 'Consultar Intranet', 'icon' => 'controles/information.png','atributos' => array(), 'confirm' => null),
				14 => array('url' => '/pfyj/personas','label' => 'Otro', 'icon' => 'controles/reload3.png','atributos' => array(), 'confirm' => null),
				
			);
//				5 => (!empty($persona['Socio']) && !empty($persona['Socio']['id']) ? array('url' => '/mutual/mutual_producto_solicitudes/by_socio/'.$persona['Socio']['id'].'/1','label' => 'O.Compras / Servicios', 'icon' => 'controles/cart_put.png','atributos' => array(), 'confirm' => null): array() ),	
if(MODULO_V1){
	//$tabs[6] = array('url' => '/pfyj/personas/solicitudes_credito/'.$persona['Persona']['id'],'label' => 'Creditos', 'icon' => 'controles/money.png','atributos' => array(), 'confirm' => null);
	//$tabs[7] = array('url' => '/mutual/mutual_producto_solicitudes/by_persona/'.$persona['Persona']['id'].'/1','label' => 'Compras', 'icon' => 'controles/cart_put.png','atributos' => array(), 'confirm' => null);
	//ksort($tabs);
}	

if($MOD_SIISA) {
    $tabs[13] = array('url' => '/pfyj/personas/consulta_siisa/'.$persona['Persona']['id'],'label' => 'SIISA', 'icon' => 'controles/calculator.png','atributos' => array(), 'confirm' => null);
    ksort($tabs);
}
	
echo $cssMenu->menuTabs($tabs);			


?>
