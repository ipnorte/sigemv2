<?php if($menu == 1):
 		echo $this->renderElement('banco_cuenta_movimientos/banco_cuenta_movimiento_header',array('cuenta' => $cuenta));
 	  else:
		echo $this->renderElement('banco_cuentas/info_cuenta',array('plugin' => 'cajabanco','banco_cuenta_id' => $cuenta['BancoCuenta']['id']));
 	  endif
?>
 	  
<h4>CONCILIACION</h4>
<?php echo $controles->botonGenerico('/cajabanco/banco_listados/conciliacion_salida/' . $saldos['BancoCuentaSaldo']['id'] . '/XLS','controles/ms_excel.png')?>
<?php echo $controles->botonGenerico('/cajabanco/banco_listados/conciliacion_salida/' . $saldos['BancoCuentaSaldo']['id'] . '/PDF','controles/pdf.png', null, array('target' => '_blank'))?>
<div class="areaDatoForm">
	<table width="60%" align="center">
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

<?php
	echo $this->renderElement('banco_cuenta_movimientos/banco_cuenta_movimiento_banco', array('cuenta' => $cuenta, 'movimientos' => $movimientos, 'conciliacion' => 1));

?>
