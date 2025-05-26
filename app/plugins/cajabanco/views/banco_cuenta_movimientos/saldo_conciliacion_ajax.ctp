<script language="Javascript" type="text/javascript">

		$('BancoCuentaSaldoConciliado').value = <?php echo $saldos['saldo_banco']?>;
		ok_conciliacion();

</script>

					<table>
						<tr>
							<th colspan="2">CONCILIACION</th>
						</tr>
						<tr>
							<td>SALDO ANTERIOR:</th>
							<td><?php echo $frm->input('BancoCuenta.saldo_anterior',array('value'=>$saldos['saldo_anterior'],'disabled'=> 'disabled')); ?></td>
						</tr>
						<tr>
							<td align="right">SALDO LIBRO BANCO:</td>
							<td><?php echo $frm->input('BancoCuenta.saldo_libro',array('value'=>$saldos['saldo_libro'],'disabled'=> 'disabled')); ?></td>
						</tr>
						<tr>
							<td align="right">SALDO NO CONCILIADO:</td>
							<td><?php echo $frm->input('BancoCuenta.saldo_no_conciliado',array('value'=>$saldos['saldo_no_conciliado'],'disabled'=> 'disabled')); ?></td>
						</tr>
						<tr>
							<td align="right">SALDO BANCO:</td>
							<td><?php echo $frm->input('BancoCuenta.saldo_banco',array('value'=>$saldos['saldo_banco'],'disabled'=> 'disabled')); ?>
								<div id="ok_banco" style="display:none"><img src="<?php echo $this->base?>/img/controles/ok.png" border="0" alt="" /></div>
							</td>
						</tr>
					</table>

<?php 
//	echo $frm->hidden('BancoCuenta.saldo_conciliado',array('value' => $saldos['saldo_banco']));
?>


				
				