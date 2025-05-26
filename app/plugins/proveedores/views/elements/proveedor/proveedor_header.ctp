<?php 
$styleUL = "list-style-type: none;border-left: 1px solid #666666;border-bottom:1px solid #666666;height: 36px;";
$styleLI = "float: left;background-color: #e2e6ea;padding: 7px 3px 3px 3px;height: 25px;border-top:1px solid #666666;border-right:1px solid #666666;";
//debug($proveedores);

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
?>

<h1>MOVIMIENTO PROVEEDOR</h1>
<?php echo $this->renderElement('proveedor/datos_proveedor',array('proveedor_id'=>$proveedores['Proveedor']['id'],'plugin' => 'proveedores'))?>

<?php 
if($proveedores['Proveedor']['tipo_proveedor']==1):
	$tabs = array(
//					0 => array('url' => '/proveedores/movimientos/pendientes/'.$proveedores['Proveedor']['id'],'label' => 'PENDIENTES', 'icon' => 'controles/page_world.png','atributos' => array(), 'confirm' => null),
//					1 => array('url' => '/proveedores/movimientos/cta_cte/'.$proveedores['Proveedor']['id'],'label' => 'CTA.CTE.', 'icon' => 'controles/book_open.png','atributos' => array(), 'confirm' => null),
//					2 => array('url' => '/proveedores/movimientos/liquidacion/'.$proveedores['Proveedor']['id'],'label' => 'LIQUIDACIONES', 'icon' => 'controles/information.png','atributos' => array(), 'confirm' => null),
//					3 => array('url' => '/proveedores/movimientos/factura/'.$proveedores['Proveedor']['id'],'label' => 'NUEVA FACTURA', 'icon' => 'controles/table_edit.png','atributos' => array(), 'confirm' => null),
//					4 => array('url' => '/proveedores/movimientos/orden_pago/'.$proveedores['Proveedor']['id'],'label' => 'ORDEN DE PAGO', 'icon' => 'controles/text_list_bullets.png','atributos' => array(), 'confirm' => null),
//					5 => array('url' => '/proveedores/movimientos/cancelaciones/'.$proveedores['Proveedor']['id'],'label' => 'CANCELACIONES', 'icon' => 'controles/profiler.gif','atributos' => array(), 'confirm' => null),
//					6 => array('url' => '/proveedores/movimientos/compensar_pagos/'.$proveedores['Proveedor']['id'],'label' => 'COMPENSAR PAGOS', 'icon' => 'controles/zone_money.png','atributos' => array(), 'confirm' => null),
//                                        if($Proveedores['Proveedor']['liquida_prestamo'] === 1):
//                                            7 => array('url' => '/proveedores/movimientos/compensar_pagos/'.$proveedores['Proveedor']['id'],'label' => 'COMPENSAR PAGOS', 'icon' => 'controles/zone_money.png','atributos' => array(), 'confirm' => null),
//                                        endif;
//					8 => array('url' => '/proveedores/movimientos','label' => 'Otro', 'icon' => 'controles/reload3.png','atributos' => array(), 'confirm' => null),
				);
	$tabs[0] = array('url' => '/proveedores/movimientos/pendientes/'.$proveedores['Proveedor']['id'],'label' => 'PENDIENTES', 'icon' => 'controles/page_world.png','atributos' => array(), 'confirm' => null);
	$tabs[1] = array('url' => '/proveedores/movimientos/cta_cte/'.$proveedores['Proveedor']['id'],'label' => 'CTA.CTE.', 'icon' => 'controles/book_open.png','atributos' => array(), 'confirm' => null);
	$tabs[2] = array('url' => '/proveedores/movimientos/factura/'.$proveedores['Proveedor']['id'],'label' => 'NUEVA FACTURA', 'icon' => 'controles/table_edit.png','atributos' => array(), 'confirm' => null);
	$tabs[3] = array('url' => '/proveedores/movimientos/orden_pago/'.$proveedores['Proveedor']['id'],'label' => 'ORDEN DE PAGO', 'icon' => 'controles/text_list_bullets.png','atributos' => array(), 'confirm' => null);
	$tabs[4] = array('url' => '/proveedores/movimientos/cancelaciones/'.$proveedores['Proveedor']['id'],'label' => 'CANCELACIONES', 'icon' => 'controles/profiler.gif','atributos' => array(), 'confirm' => null);
	$tabs[5] = array('url' => '/proveedores/movimientos/compensar_pagos/'.$proveedores['Proveedor']['id'],'label' => 'COMPENSAR PAGOS', 'icon' => 'controles/zone_money.png','atributos' => array(), 'confirm' => null);
//        if($proveedores['Proveedor']['liquida_prestamo'] === '1'):
            $tabs[6] = array('url' => '/proveedores/movimientos/cta_cte_operativo/'.$proveedores['Proveedor']['id'],'label' => 'CTA.CTE OPERATIVO', 'icon' => 'controles/book_open.png','atributos' => array(), 'confirm' => null);
//        endif;
	$tabs[7] = array('url' => '/proveedores/movimientos','label' => 'Otro', 'icon' => 'controles/reload3.png','atributos' => array(), 'confirm' => null);
else:
	$tabs = array(
					0 => array('url' => '/proveedores/movimientos/pendientes/'.$proveedores['Proveedor']['id'],'label' => 'PENDIENTES', 'icon' => 'controles/page_world.png','atributos' => array(), 'confirm' => null),
					1 => array('url' => '/proveedores/movimientos/cta_cte/'.$proveedores['Proveedor']['id'],'label' => 'CTA.CTE.', 'icon' => 'controles/book_open.png','atributos' => array(), 'confirm' => null),
					2 => array('url' => '/proveedores/movimientos/factura/'.$proveedores['Proveedor']['id'],'label' => 'NUEVA FACTURA', 'icon' => 'controles/table_edit.png','atributos' => array(), 'confirm' => null),
					3 => array('url' => '/proveedores/movimientos/orden_pago/'.$proveedores['Proveedor']['id'],'label' => 'Orden de Pago', 'icon' => 'controles/text_list_bullets.png','atributos' => array(), 'confirm' => null),
					4 => array('url' => '/proveedores/movimientos/compensar_pagos/'.$proveedores['Proveedor']['id'],'label' => 'COMPENSAR PAGOS', 'icon' => 'controles/zone_money.png','atributos' => array(), 'confirm' => null),
					5 => array('url' => '/proveedores/movimientos','label' => 'Otro', 'icon' => 'controles/reload3.png','atributos' => array(), 'confirm' => null),
				);
endif;
//$tabs = array(
//				0 => array('url' => '/proveedores/movimientos/cta_cte/'.$proveedores['Proveedor']['id'],'label' => 'CTA.CTE.', 'icon' => 'controles/viewmag.png','atributos' => array(), 'confirm' => null),
//				1 => array('url' => '/proveedores/movimientos/'.$accion,'label' => $label, 'icon' => 'controles/information.png','atributos' => array(), 'confirm' => null),
//				2 => array('url' => '/proveedores/movimientos/orden_pago/'.$proveedores['Proveedor']['id'],'label' => 'Orden de Pago', 'icon' => 'controles/user.png','atributos' => array(), 'confirm' => null),
//				3 => array('url' => '/proveedores/movimientos','label' => 'Otro', 'icon' => 'controles/reload3.png','atributos' => array(), 'confirm' => null),
//				
//			);
			
// debug($tabs);

echo $cssMenu->menuTabs($tabs);			


?>