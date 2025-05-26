<?php
$checkedA = '';
$checkedB = '';
if($dato['BancoCuentaSaldo']['tipo_conciliacion']==0) $checkedA = 'checked';
else $checkedB = 'checked';

$disabledSaldo = '';
if($cuenta['BancoCuenta']['banco_cuenta_saldo_id']!=$cuenta['BancoCuenta']['banco_cuenta_saldo_alta_id']) $disabledSaldo = 'disabled';

?>
<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'CONFIGURACION DE CUENTAS BANCARIAS :: SALDO INICIAL'))?>
<?php echo $this->renderElement('banco_cuentas/info_cuenta',array('plugin' => 'cajabanco','banco_cuenta_id' => $banco_cuenta_id))?>
<?php echo $frm->create(null,array('action' => "edit/$banco_cuenta_id"));?>
<div class="areaDatoForm">
	<table class="tbl_form">
		<?php if($cuenta['BancoCuenta']['banco_id'] == 99999): ?>
			<tr>
				<td>ULTIMO CIERRE DE PLANILLA DE CAJA:</td>
				<td><?php echo $frm->calendar('BancoCuentaSaldo.fecha_conciliacion',null, $dato['BancoCuentaSaldo']['fecha_cierre'],'1990',date("Y") + 1)?></td>
			</tr>
			<tr>
				<td>SALDO DE CAJA:</td>
				<td><?php echo $frm->money('BancoCuentaSaldo.importe_conciliacion','',$dato['BancoCuentaSaldo']['saldo_conciliacion'],'1990',date("Y") + 1) ?></td>
			</tr>
			<tr>
				<td><?php echo $frm->hidden('tipo_conciliacion', array('value' => 0))?></td>
			</tr>
		<?php else: ?>
			<tr>
				<td>CONCILIADO AL:</td>
				<td><?php echo $frm->calendar('BancoCuentaSaldo.fecha_conciliacion',null, $dato['BancoCuentaSaldo']['fecha_cierre'],'1990',date("Y") + 1)?></td>
			</tr>
			<tr>
				<td>IMPORTE CONCILIADO:</td>
				<td><?php echo $frm->money('BancoCuentaSaldo.importe_conciliacion','',$dato['BancoCuentaSaldo']['saldo_conciliacion']) ?></td>
			</tr>
			<tr>
				<td>TIPO DE CONCILIACION:</td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="radio" name="data[BancoCuentaSaldo][tipo_conciliacion]" id="CuentaAccion_a" value="0" <?php echo $checkedA?> <?php echo $disabledSaldo?>/>DEBITO</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="radio" name="data[BancoCuentaSaldo][tipo_conciliacion]" id="CuentaAccion_b" value="1" <?php echo $checkedB?> <?php echo $disabledSaldo?>/>CREDITO</td>
			</tr>
		<?php endif; ?>
	</table>
</div>
<?php echo $frm->hidden('id', array('value' => $dato['BancoCuentaSaldo']['id']))?>
<?php echo $frm->hidden('banco_cuenta_id',array('value' => $banco_cuenta_id))?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/cajabanco/banco_cuentas/index'))?>