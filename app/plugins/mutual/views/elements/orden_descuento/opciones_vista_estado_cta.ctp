<?php 
if(!isset($menuPersonas)) $menuPersonas = 1;
$tabs = array(
				0 => array('url' => '/mutual/orden_descuento_cuotas/estado_cuenta2/'.$socio_id.'/'.$menuPersonas,'label' => 'Estado de Cuenta', 'icon' => 'controles/page_white_paste.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/mutual/orden_descuentos/by_socio/'.$socio_id.'/'.$menuPersonas,'label' => 'Consumos', 'icon' => 'controles/cart.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/mutual/liquidaciones/by_socio/'.$socio_id.'/'.$menuPersonas,'label' => 'Liquidaciones', 'icon' => 'controles/disk_multiple.png','atributos' => array(), 'confirm' => null),
				3 => array('url' => '/mutual/orden_descuento_cuotas/orden_cobro_xcaja/'.$socio_id,'label' => 'Cobro x Caja', 'icon' => 'controles/zone_money.png','atributos' => array('target'=>'blank'), 'confirm' => null),
				4 => array('url' => '/mutual/cancelacion_ordenes/by_socio/'.$socio_id,'label' => 'Cancelaciones', 'icon' => 'controles/calculator_add.png','atributos' => array(), 'confirm' => null),
//				5 => array('url' => '/pfyj/socio_convenios/index/'.$socio_id,'label' => 'Convenios', 'icon' => 'controles/calendar_2.png','atributos' => array(), 'confirm' => null),
				6 => array('url' => '/pfyj/socio_reintegros/by_socio/'.$socio_id,'label' => 'Reintegros', 'icon' => 'controles/pin.png','atributos' => array(), 'confirm' => null),
				7 => array('url' => '/mutual/orden_descuento_cobros/by_socio/'.$socio_id,'label' => 'Pagos', 'icon' => 'controles/money.png','atributos' => array(), 'confirm' => null),
// 				8 => array('url' => '/pfyj/persona_beneficios/reasignar_beneficio_puntual/'.$socio_id,'label' => 'Rep.Benef.Cuota', 'icon' => 'controles/chart_organisation.png','atributos' => array(), 'confirm' => null),
				9 => array('url' => '/mutual/orden_descuentos/reasignar_beneficio/'.$persona_id,'label' => 'Reasignar Beneficio', 'icon' => 'controles/chart_organisation.png','atributos' => array('target'=>'blank'), 'confirm' => null),
			);

$entes = $datos = $this->requestAction('/config/global_datos/get_ente_recaudadores');
if(!empty($entes)){
    $tabs[10] = array('url' => '/mutual/orden_descuento_cuotas/ente_recaudador/'.$socio_id,'label' => 'Ente Recaudador', 'icon' => 'controles/page_world.png','atributos' => array(), 'confirm' => null);
}
echo $cssMenu->menuTabs($tabs,false);			
?>

