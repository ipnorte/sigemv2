<?php 
$styleUL = "list-style-type: none;border-left: 1px solid #666666;border-bottom:1px solid #666666;height: 36px;";
$styleLI = "float: left;background-color: #e2e6ea;padding: 7px 3px 3px 3px;height: 25px;border-top:1px solid #666666;border-right:1px solid #666666;";

//$label_1 = 'CONCILIACION BANCARIA';
//$accion_1 = 'conciliacion/' . $cuenta['BancoCuenta']['id'];
//$label_2 = 'SALDOS CONCILIADOS';
//$accion_2 = 'saldo_conciliado/' . $cuenta['BancoCuenta']['id'];
//if($cuenta['BancoCuenta']['banco_id']=='99999'):
//	$label_1 = 'CIERRE DE CAJA';
//	$accion_1 = 'cierre_caja/' . $cuenta['BancoCuenta']['id'];
//	$label_2 = 'PLANILLAS DE CAJA';
//	$accion_2 = 'planillas/' . $cuenta['BancoCuenta']['id'];
////	$accion_2 = 'saldo_conciliado/' . $cuenta['BancoCuenta']['id'];
//endif;

?>

<h1>MOVIMIENTOS DE CAJA Y BANCO</h1>
<?php echo $this->renderElement('banco_cuentas/info_cuenta',array('plugin' => 'cajabanco','banco_cuenta_id' => $cuenta['BancoCuenta']['id']))?>
<?php //echo $this->renderElement('banco_cuenta_movimientos/datos_cuenta',array('cuenta'=>$cuenta,'plugin' => 'cajabanco'));
if($cuenta['BancoCuenta']['banco_id']=='99999'):

	$tabs = array(
				0 => array('url' => '/cajabanco/banco_cuenta_movimientos/resumen/'.$cuenta['BancoCuenta']['id'],'label' => 'RESUMEN', 'icon' => 'controles/information.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/cajabanco/banco_cuenta_movimientos/registracion/'.$cuenta['BancoCuenta']['id'],'label' => 'REGISTRACIONES', 'icon' => 'controles/zone_money.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/cajabanco/banco_cuenta_movimientos/efectivo_cheque_cartera/' . $cuenta['BancoCuenta']['id'],'label' => 'EFEC.CHEQUE CARTERA', 'icon' => 'controles/money.png','atributos' => array(), 'confirm' => null),
				3 => array('url' => '/cajabanco/banco_cuenta_movimientos/cierre_caja/' . $cuenta['BancoCuenta']['id'],'label' => 'CIERRE DE CAJA', 'icon' => 'controles/lock.png','atributos' => array(), 'confirm' => null),
				4 => array('url' => '/cajabanco/banco_cuenta_saldos/planillas/' . $cuenta['BancoCuenta']['id'],'label' => 'PLANILLAS DE CAJA', 'icon' => 'controles/report.png','atributos' => array(), 'confirm' => null),
//				5 => array('url' => '/cajabanco/banco_cuenta_saldos/libro_caja/' . $cuenta['BancoCuenta']['id'],'label' => 'LIBRO DE CAJA', 'icon' => 'controles/book.png','atributos' => array(), 'confirm' => null),
				6 => array('url' => '/cajabanco/banco_cuenta_movimientos','label' => 'Otra Cuenta', 'icon' => 'controles/reload3.png','atributos' => array(), 'confirm' => null),
				
			);

else:

	$tabs = array(
				0 => array('url' => '/cajabanco/banco_cuenta_movimientos/resumen/'.$cuenta['BancoCuenta']['id'],'label' => 'RESUMEN', 'icon' => 'controles/information.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/cajabanco/banco_cuenta_movimientos/registracion/'.$cuenta['BancoCuenta']['id'],'label' => 'REGISTRACIONES', 'icon' => 'controles/zone_money.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/cajabanco/banco_listados/libro_banco/' . $cuenta['BancoCuenta']['id'],'label' => 'LIBRO BANCO', 'icon' => 'controles/book_open.png','atributos' => array(), 'confirm' => null),
				3 => array('url' => '/cajabanco/banco_cuenta_movimientos/conciliacion/' . $cuenta['BancoCuenta']['id'],'label' => 'CONCILIACION BANCARIA', 'icon' => 'controles/lock.png','atributos' => array(), 'confirm' => null),
				4 => array('url' => '/cajabanco/banco_cuenta_saldos/saldo_conciliado/' . $cuenta['BancoCuenta']['id'],'label' => 'SALDOS CONCILIADOS', 'icon' => 'controles/report.png','atributos' => array(), 'confirm' => null),
				5 => array('url' => '/cajabanco/banco_cuentas/index_cheque/' . $cuenta['BancoCuenta']['id'],'label' => 'IMPRIMIR CHEQUES', 'icon' => 'controles/book.png','atributos' => array(), 'confirm' => null),
				6 => array('url' => '/cajabanco/banco_cuenta_movimientos','label' => 'Otra Cuenta', 'icon' => 'controles/reload3.png','atributos' => array(), 'confirm' => null),
				
			);

endif;			
			
echo $cssMenu->menuTabs($tabs);			


?>