<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'CONFIGURACION DE CUENTAS BANCARIAS :: ADMINISTRACION DE CHEQUERAS'))?>
<?php echo $this->renderElement('banco_cuentas/info_cuenta',array('plugin' => 'cajabanco','banco_cuenta_id' => $banco_cuenta_id))?>
<?php echo $frm->create('BancoCuentaChequera',array('action' => "add/$banco_cuenta_id"));?>
<div class="areaDatoForm">
	<h3>ALTA NUEVA CHEQUERA</h3>
	<table class="tbl_form">
		<tr>
			<td>CONCEPTO</td><td><?php echo $frm->input('concepto',array('size' => 60, 'maxlength' => 100))?></td>
		</tr>
		<tr>
			<td>SERIE</td><td><?php echo $frm->input('serie',array('size' => 2, 'maxlength' => 1))?></td>
		</tr>		
		<tr>
			<td>DESDE NUMERO</td><td><?php echo $frm->number('desde_numero',array('size' => 8, 'maxlength' => 8))?></td>
		</tr>
		<tr>
			<td>HASTA NUMERO</td><td><?php echo $frm->number('hasta_numero',array('size' => 8, 'maxlength' => 8))?></td>
		</tr>
		<tr>
			<td>VIGENTE</td><td><?php echo $frm->input('activo')?></td>
		</tr>						
	</table>
</div>
<?php echo $frm->hidden('id',array('value' => 0))?>
<?php echo $frm->hidden('banco_cuenta_id',array('value' => $banco_cuenta_id))?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/cajabanco/banco_cuenta_chequeras/index/' . $banco_cuenta_id))?>