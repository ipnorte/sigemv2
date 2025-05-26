<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'MOVIMIENTOS DE CAJA Y BANCO'))?>
<?php if(!empty($cuentas))echo $this->renderElement('banco_cuenta_movimientos/grilla_cuentas',array('cuentas'=>$cuentas,'plugin' => 'cajabanco'))?>
