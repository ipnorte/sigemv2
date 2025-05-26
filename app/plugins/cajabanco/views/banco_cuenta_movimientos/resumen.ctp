<?php echo $this->renderElement('banco_cuenta_movimientos/banco_cuenta_movimiento_header',array('cuenta' => $cuenta))?>
<h4>RESUMEN</h4>
<?php
	if($cuenta['BancoCuenta']['banco_id'] == '99999'):
		echo $this->renderElement('banco_cuenta_movimientos/banco_cuenta_movimiento_caja', array('cuenta' => $cuenta, 'movimientos' => $movimientos));
	else:
		echo $this->renderElement('banco_cuenta_movimientos/banco_cuenta_movimiento_banco', array('cuenta' => $cuenta, 'movimientos' => $movimientos, 'conciliacion' => 0));
	endif;

?>
