<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'CONFIGURACION DE CUENTAS BANCARIAS'))?>
<div class="actions">
<?echo $controles->botonGenerico('add','controles/add.png','Nueva Cuenta')?>
</div>

<?php echo $this->renderElement('paginado')?>

	<table>
	
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th><?php echo $paginator->sort('BANCO','Banco.nombre');?></th>
			<th>DENOMINACION</th>
			<th>NRO CUENTA</th>
			<th><?php echo $paginator->sort('ESTADO','BancoCuenta.activo');?></th>
			<th><?php echo $paginator->sort('FORMATO CH.','BancoCuenta.formato_cheque');?></th>
			<th>FECHA APERTURA</th>
			<th>CUENTA CONTABLE</th>
			<th>FECHA CIERRE</th>
			<th>SALDO</th>
		</tr>
		<?php foreach($cuentas as $cuenta):
			if($cuenta['BancoCuenta']['tipo_conciliacion'] == 1) $cuenta['BancoCuenta']['importe_conciliacion'] *= -1;
		?>
		
			<tr <?php echo ($cuenta['BancoCuenta']['banco_id'] == 99999 ? ' class="altrow"' : "")?>>
				<td>#<?php echo $cuenta['BancoCuenta']['id']?></td>
				<td><?php echo $controles->getAcciones($cuenta['BancoCuenta']['id'],false) ?></td>
				<td><?php if($cuenta['BancoCuenta']['chequeras'] == 1) echo $controles->botonGenerico('/cajabanco/banco_cuenta_chequeras/index/' . $cuenta['BancoCuenta']['id'],'controles/money.png')?></td>
				<td><?php if($cuenta['BancoCuenta']['banco_cuenta_saldo_id'] == $cuenta['BancoCuenta']['banco_cuenta_saldo_alta_id']) echo $controles->botonGenerico('/cajabanco/banco_cuenta_saldos/edit/' . $cuenta['BancoCuenta']['id'],'controles/money_dollar.png')?></td>
				<td><strong><?php echo $cuenta['BancoCuenta']['banco']?></strong></td>
				<td><?php echo $cuenta['BancoCuenta']['denominacion']?></td>
				<td><strong><?php echo $cuenta['BancoCuenta']['numero']?></strong></td>
				<td align="center"><?php echo $controles->onOff($cuenta['BancoCuenta']['activo'])?></td>
				<td><?php if($cuenta['BancoCuenta']['chequeras'] == 1) echo $controles->botonGenerico('/cajabanco/banco_cuentas/relacionar_banco_formato_cheque/' . $cuenta['BancoCuenta']['id'],'controles/vcard.png')?></td>
				<td align="center"><?php echo $util->armaFecha($cuenta['BancoCuenta']['fecha_apertura'])?></td>
				<td><?php echo $cuenta['BancoCuenta']['cuenta_contable']?></td>
				<td align="center"><?php echo $util->armaFecha($cuenta['BancoCuenta']['fecha_conciliacion'])?></td>
				<td align="right"><?php echo $cuenta['BancoCuenta']['importe_conciliacion'] ?></td>
				
			
			</tr>
		
		<?php endforeach;?>
	
	</table>


<?php echo $this->renderElement('paginado')?>