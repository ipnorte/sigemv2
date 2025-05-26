<?php $cuenta = $this->requestAction("/cajabanco/banco_cuentas/get/$banco_cuenta_id");?>
<?php if(!empty($cuenta)):?>
	<div class="areaDatoForm">
		<?php if($cuenta['BancoCuenta']['banco_id'] != '99999'): ?>
			<h4>DATOS DE LA CUENTA BANCARIA #<?php echo $banco_cuenta_id?></h4>
			BANCO: <strong><?php echo $cuenta['BancoCuenta']['banco']?></strong>
			<br/>
			CUENTA: <strong><?php echo $cuenta['BancoCuenta']['numero']?></strong> &nbsp;
			DENOMINACION: <strong><?php echo $cuenta['BancoCuenta']['denominacion']?></strong>
			<br/>
			FECHA APERTURA: <strong><?php echo $util->armaFecha($cuenta['BancoCuenta']['fecha_apertura'])?></strong>
			&nbsp;
			CUENTA CONTABLE: <strong><?php echo $cuenta['BancoCuenta']['cuenta_contable']?></strong>
			<br/>
			ESTADO: <strong><?php echo ($cuenta['BancoCuenta']['activo'] == 1 ? "<span style='color:green;'>VIGENTE</span>" : "<span style='color:red;'>NO ACTIVA</span>")?></strong>
			<br/>
			<?php if(isset($mostrarChequeras) && $mostrarChequeras && !empty($cuenta['BancoCuentaChequera'])):?>
				
				<table>
					<tr>
						<th colspan="7">CHEQUERAS VINCULADAS</th>
					</tr>
					<tr>
						<th></th>
						<th>CHEQUERA</th>
						<th>SERIE</th>
						<th>NRO DESDE</th>
						<th>NRO HASTA</th>
						<th>PROXIMO</th>
						<th>ESTADO</th>
					</tr>
					<?php foreach($cuenta['BancoCuentaChequera'] as $chequera):?>
					
						<tr>
							
							<td>#<?php echo $chequera['id']?></td>
							<td><?php echo $chequera['concepto']?></td>
							<td align="center"><?php echo $chequera['serie']?></td>
							<td align="center"><?php echo $chequera['desde_numero']?></td>
							<td align="center"><?php echo $chequera['hasta_numero']?></td>
							<td align="center"><strong><?php echo $chequera['proximo_numero']?></strong></td>
							<td align="center"><?php echo $controles->onOff($chequera['activo'])?></td>
						
						</tr>
					
					<?php endforeach;?>
				
				</table>			
				
				
			<?php endif;?>
		<?php else: ?>
			<h4>CAJA GENERAL #<?php echo $banco_cuenta_id?></h4>
			BANCO: <strong><?php echo $cuenta['BancoCuenta']['banco']?></strong>
			<br/>
			CUENTA: <strong><?php echo $cuenta['BancoCuenta']['numero']?></strong> &nbsp;
			DENOMINACION: <strong><?php echo $cuenta['BancoCuenta']['denominacion']?></strong>
			<br/>
			ULTIMO CIERRE: <strong><?php echo $util->armaFecha($cuenta['BancoCuenta']['fecha_conciliacion'])?></strong>
			&nbsp;
			SALDO: <strong><?php echo $cuenta['BancoCuenta']['importe_conciliacion'] ?></strong>
			<br/>
			CUENTA CONTABLE: <strong><?php echo $cuenta['BancoCuenta']['cuenta_contable']?></strong>
			<br/>
		<?php endif; ?>
	</div>
<?php endif;?>
