<?php
    	// debug($movimiento);
    	// exit;
?>
<div class="areaDatoForm">
<?php echo $frm->create(null,array('name'=>'formMovimiento','id'=>'formMovimiento', 'action' => "edit/" . $movimiento['BancoCuentaMovimiento']['id'] ));?>
<table class="areaDatoForm">
	<tr>
		<td>FECHA OPERACION:</td>
		<td><?php echo date('d/m/Y',strtotime($movimiento['BancoCuentaMovimiento']['fecha_operacion']))?>
	</tr>
	<tr>
		<td>FECHA VENCIMIENTO:</td>
		<td><?php echo date('d/m/Y',strtotime($movimiento['BancoCuentaMovimiento']['fecha_vencimiento']))?>
	</tr>
	<tr>
		<td>CONCEPTO:</td>
		<td><strong><?php echo $movimiento['BancoCuentaMovimiento']['concepto']?></strong></td>
	</tr>
	<tr>
		<td>NUMERO OPERACION:</td>
		<td><?php echo $movimiento['BancoCuentaMovimiento']['numero_operacion'] ?>
	</tr>
	<tr>
		<td>DESTINATARIO:</td>
		<td><?php echo $movimiento['BancoCuentaMovimiento']['destinatario'] ?></td>
	</tr>
	<tr>
		<td>DESCRIPCION:</td>
		<td><?php echo $movimiento['BancoCuentaMovimiento']['descripcion'] ?></td>
	</tr>
	<tr>
		<td>IMPORTE:</td>
		<td><?php echo ($movimiento['BancoCuentaMovimiento']['debe'] == 0  ? number_format($movimiento['BancoCuentaMovimiento']['haber'],2) : number_format($movimiento['BancoCuentaMovimiento']['debe'],2))?></td>
	</tr>
	<tr>
		<td>HACIA CTA.BANCARIA:</td>
		<td><?php echo $this->renderElement('banco_cuentas/combo_cuentas',array(
								'plugin'=>'cajabanco',
								'label' => "",
								'model' => 'BancoCuentaMovimiento.hacia_banco_cuenta_id',
								'disabled' => false,
								'empty' => false,
								'selected' => 0,
								'ecepto' => $movimiento['BancoCuentaMovimiento']['banco_cuenta_id']))?>
		</td>			
	</tr>
</table>
		<?php echo $frm->hidden("BancoCuentaMovimiento.importe_cheque", array('value' => 0.00)); ?>
		<?php echo $frm->hidden("BancoCuentaMovimiento.banco_cuenta_id", array('value' => $cuenta['BancoCuenta']['id'])); ?>
		<?php echo $frm->btnGuardarCancelar(array('URL' => '/cajabanco/banco_cuenta_movimientos/resumen/' . $movimiento['BancoCuentaMovimiento']['banco_cuenta_id']))?>
</div>
		