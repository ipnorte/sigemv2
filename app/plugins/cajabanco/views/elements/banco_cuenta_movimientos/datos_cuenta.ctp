<div class="areaDatoForm">
<?php if($cuenta['BancoCuenta']['banco_id']!='99999'): ?>
	<h4>DATOS CUENTA BANCARIA</h4>
	BANCO: <strong><?php echo $cuenta['BancoCuenta']['banco'] ?></strong>
	<br/>
	NRO. CUENTA: <strong><?php echo $cuenta['BancoCuenta']['numero'] ?></strong>
	&nbsp;
	DENOMINACION: <strong><?php echo $cuenta['BancoCuenta']['denominacion'] ?></strong>
	<br/>
	CUENTA CONTABLE: <strong><?php echo $cuenta['BancoCuenta']['cuenta_contable'] ?></strong>
	<br/>
<?php else: ?>
	<h4>DATOS DE LA CAJA GENERAL</h4>
	NRO. CUENTA: <strong><?php echo $cuenta['BancoCuenta']['numero'] ?></strong>
	&nbsp;
	DENOMINACION: <strong><?php echo $cuenta['BancoCuenta']['denominacion'] ?></strong>
	<br/>
	CUENTA CONTABLE: <strong><?php echo $cuenta['BancoCuenta']['cuenta_contable'] ?></strong>
	<br/>
<?php endif; ?>
</div>