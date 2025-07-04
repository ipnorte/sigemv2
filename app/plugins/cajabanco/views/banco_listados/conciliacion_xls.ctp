<?php
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Conciliacion.xls");
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");
?>

<h4>CONCILIACION</h4>

<div class="areaDatoForm">
	<table width="60%" align="center" border = "1">
		<tr>
			<td>NUMERO O IDENTIFICACION:</th>
			<td><?php echo $saldos['BancoCuentaSaldo']['numero_extracto']; ?></td>
			<td>FECHA CONCILIACION:</td>
			<td><?php echo date('d/m/Y',strtotime($saldos['BancoCuentaSaldo']['fecha_cierre']))?></td>
		</tr>
		
		<tr>

			<td colspan="2">
				
				<div id="div_conciliacion">
					<table width="100%">
						<tr>
							<th colspan="2">LIBRO BANCO</th>
						</tr>
						<tr>
							<td align="right">SALDO ANTERIOR:</th>
							<td align="right"><?php echo number_format($saldos['BancoCuentaSaldo']['saldo_anterior'],2); ?></td>
						</tr>
						<tr>
							<td align="right">SALDO LIBRO BANCO:</td>
							<td align="right"><?php echo number_format($saldos['BancoCuentaSaldo']['saldo_referencia_1'],2); ?></td>
						</tr>
						<tr>
							<td align="right">SALDO NO CONCILIADO:</td>
							<td align="right"><?php echo number_format($saldos['BancoCuentaSaldo']['saldo_referencia_2'],2); ?></td>
						</tr>
						<tr>
							<th align="right">SALDO BANCO:</th>
							<th align="right"><?php echo number_format($saldos['BancoCuentaSaldo']['saldo_conciliacion'],2); ?></th>
							
						</tr>
					</table>
				</div>
			</td>

			<td colspan="2">
				<table width="100%">
					<tr>
						<th colspan="2">EXTRACTO BANCO</th>
					</tr>
					<tr>
						<td align="right">SALDO AL INICIO:</th>
						<td align="right"><?php echo number_format($saldos['BancoCuentaSaldo']['saldo_anterior'],2); ?></td>
					</tr>
					<tr>
						<td align="right">DEBITOS:</th>
						<td align="right"><?php echo number_format($saldos['BancoCuentaSaldo']['debe'],2); ?></td>
					</tr>
					<tr>
						<td align="right">CREDITOS:</th>
						<td align="right"><?php echo number_format($saldos['BancoCuentaSaldo']['haber'],2); ?></td>
					</tr>
					<tr>
						<th align="right">SALDO AL FINAL:</th>
						<th align="right"><?php echo number_format($saldos['BancoCuentaSaldo']['saldo_conciliacion'],2); ?></th>
					</tr>
				</table>
			</td>

		</tr>
	</table>
</div>

<p></p>
<p></p>
<p></p>

<div>
	<table width="100%">
	
		<tr>
			<th></th>
			<th>FECHA OPER.</th>
			<th>FECHA VENC.</th>
			<th>CONCEPTO</th>
			<th>NRO. OPER.</th>
			<th>REFERENCIA</th>
			<th>DESCRIPCION</th>
			<th>DEBE</th>
			<th>HABER</th>
			<th>SALDO</th>
		</tr>
		<?php
			$saldo = $cuenta['BancoCuenta']['importe_conciliacion'];
			if($cuenta['BancoCuenta']['tipo_conciliacion'] == 1) $saldo *= -1;
		?>
		<tr class="altrow">
			<td></td>
			<td align="center"><?php echo date('d/m/Y',strtotime($cuenta['BancoCuenta']['fecha_conciliacion']))?></td>
			<td align="center"><?php echo date('d/m/Y',strtotime($cuenta['BancoCuenta']['fecha_conciliacion']))?></td>
			<td>SALDO ANTERIOR</td>
			<td></td>
			<td></td>
			<td></td>
	
			<?php if($cuenta['BancoCuenta']['tipo_conciliacion'] == 0): ?>
			<td align="right"><?php echo number_format($cuenta['BancoCuenta']['importe_conciliacion'],2) ?></td>
			<td></td>
			<?php else: ?>
			<td></td>
			<td align="right"><?php echo number_format($cuenta['BancoCuenta']['importe_conciliacion'],2) ?></td>
			<?php endif; ?>
			<td align="right"><?php echo number_format($saldo,2) ?></td>
		</tr>
		<?php
		$i = 1;
		foreach ($movimientos as $renglon):
			if($renglon['BancoCuentaMovimiento']['anulado'] == 0):
				$saldo += $renglon['BancoCuentaMovimiento']['debe'] - $renglon['BancoCuentaMovimiento']['haber'];
			else:
				$class = ' class="MUTUSICUJUDI"';
				$style = ' style="color:red"';
			endif; 
			$descripcion = '';
			if($renglon['BancoCuentaMovimiento']['reemplazar'] == 1):
				if($renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['tipo'] == 7):
					$descripcion = ' - REEM. ' . $renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['banco_cuenta'] . ' (' . $renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['concepto'] . ')';
				else:
					$descripcion = ' - REEM. ' . $renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['banco_str'] . '-' . $renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['cuenta_str'] . '- CH.NRO. ' . $renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['numero_operacion'];
				endif;
			endif;
		?>
			<tr<?php echo $class;?>>
				<td align="right"><?php echo $renglon['BancoCuentaMovimiento']['id']?></td>
				<td align="center"><?php echo date('d/m/Y',strtotime($renglon['BancoCuentaMovimiento']['fecha_operacion']))?>
				<td align="center"><?php echo date('d/m/Y',strtotime($renglon['BancoCuentaMovimiento']['fecha_vencimiento']))?>
				<td><strong><?php echo $renglon['BancoCuentaMovimiento']['concepto'] . ($renglon['BancoCuentaMovimiento']['anulado'] == 1 ? ' (ANULADO)' : '')?></strong></td>
				<td align="right"><?php echo $renglon['BancoCuentaMovimiento']['numero_operacion'] ?>
				<td><?php echo $renglon['BancoCuentaMovimiento']['destinatario'] ?></td>
				<td><?php echo $renglon['BancoCuentaMovimiento']['descripcion']?>
				<td align="right"><?php echo ($renglon['BancoCuentaMovimiento']['debe'] == 0  ? '' : number_format($renglon['BancoCuentaMovimiento']['debe'],2))?></td>
				<td align="right"><?php echo ($renglon['BancoCuentaMovimiento']['haber'] == 0 ? '' : number_format($renglon['BancoCuentaMovimiento']['haber'],2))?></td>
				<td align="right"><?php echo number_format($saldo,2)?></td>
			</tr>
		<?php endforeach; ?>	
	</table>
</div>
