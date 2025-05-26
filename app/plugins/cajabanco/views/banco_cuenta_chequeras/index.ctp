<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'CONFIGURACION DE CUENTAS BANCARIAS :: ADMINISTRACION DE CHEQUERAS'))?>
<?php echo $this->renderElement('banco_cuentas/info_cuenta',array('plugin' => 'cajabanco','banco_cuenta_id' => $banco_cuenta_id))?>
<div class="actions">
<?php if($cuenta['BancoCuenta']['activo'] == 1):?>
	<?php echo $controles->botonGenerico('add/'.$banco_cuenta_id,'controles/add.png','Nueva Chequera')?>
	&nbsp;|&nbsp;
<?php endif;?>
<?php echo $controles->btnRew('Regresar al Listado de Cuentas Bancarias','/cajabanco/banco_cuentas')?>
</div>
<h3>CHEQUERAS VINCULADAS A ESTA CUENTA</h3>
	<table>
	
		<tr>
			<th></th>
			<th></th>
			<th>CHEQUERA</th>
			<th>SERIE</th>
			<th>NRO DESDE</th>
			<th>NRO HASTA</th>
			<th>PROXIMO</th>
			<th>ESTADO</th>
		</tr>
		<?php foreach($chequeras as $chequera):?>
		
			<tr>
				
				<td>#<?php echo $chequera['BancoCuentaChequera']['id']?></td>
				<td><?php echo $controles->getAcciones($chequera['BancoCuentaChequera']['id'],false) ?></td>
				<td><?php echo $chequera['BancoCuentaChequera']['concepto']?></td>
				<td align="center"><?php echo $chequera['BancoCuentaChequera']['serie']?></td>
				<td align="center"><?php echo $chequera['BancoCuentaChequera']['desde_numero']?></td>
				<td align="center"><?php echo $chequera['BancoCuentaChequera']['hasta_numero']?></td>
				<td align="center"><strong><?php echo $chequera['BancoCuentaChequera']['proximo_numero']?></strong></td>
				<td align="center"><?php echo $controles->onOff($chequera['BancoCuentaChequera']['activo'])?></td>
			
			</tr>
		
		<?php endforeach;?>
	
	</table>
