<?php echo $this->renderElement('banco_cuenta_movimientos/banco_cuenta_movimiento_header',array('cuenta' => $cuenta))?>
<h4>SALDO CONCILIADO</h4>

<?php 
//debug($cuenta);
//debug($conciliaciones);
?>

<div class="areaDatoForm">
<table class="areaDatoForm">

	<tr>
		<th></th>
		<th></th>
		<th colspan=4 align="center" style="border: 1px solid white">LIBRO BANCO</th>
		<th colspan=4 align="center" style="border: 1px solid white">EXTRACTO BANCARIO</th>
		<th></th>
	</tr>


	<tr>
		<th>NRO.EXTRACTO</th>
		<th>FECHA CIERRE/CONCILIACION</th>
		<th style="border: 1px solid white">SALDO ANTERIOR</th>
		<th style="border: 1px solid white">SALDO LIBRO BCO.</th>
		<th style="border: 1px solid white">SALDO NO CONCILIADO</th>
		<th style="border: 1px solid white">CONCILIACION</th>
		<th style="border: 1px solid white">SALDO AL INICIO</th>
		<th style="border: 1px solid white">DEBE</th>
		<th style="border: 1px solid white">HABER</th>
		<th style="border: 1px solid white">SALDO AL FINAL</th>
		<th></th>
	</tr>
	<?php
	foreach ($conciliaciones as $conciliacion):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr <?php echo $class;?>>
		<?php 
			if($cuenta['BancoCuenta']['banco_cuenta_saldo_alta_id'] == $conciliacion['BancoCuentaSaldo']['id']):
		?>
		<td>ALTA DE CUENTA</td>
		<?php 
			else:
		?>
		<td><?php echo $controles->linkModalBox($conciliacion['BancoCuentaSaldo']['numero_extracto'],array('title' => 'CONCILIACION NRO.' . $conciliacion['BancoCuentaSaldo']['numero_extracto'],'url' => '/cajabanco/banco_cuenta_saldos/view_conciliacion/'.$conciliacion['BancoCuentaSaldo']['id'].'/0','h' => 450, 'w' => 950))?></td>
		<?php 
			endif;
		?>
		<td align="center"><?php echo date('d/m/Y',strtotime($conciliacion['BancoCuentaSaldo']['fecha_cierre']))?></td>
		<td align="right"><?php echo number_format($conciliacion['BancoCuentaSaldo']['saldo_anterior'],2)?></td>
		<td align="right"><?php echo number_format($conciliacion['BancoCuentaSaldo']['saldo_referencia_1'],2)?></td>
		<td align="right"><?php echo number_format($conciliacion['BancoCuentaSaldo']['saldo_referencia_2'],2)?></td>
		<td align="right"><?php echo number_format($conciliacion['BancoCuentaSaldo']['saldo_conciliacion'],2)?></td>
		<td align="right"><?php echo number_format($conciliacion['BancoCuentaSaldo']['saldo_anterior'],2)?></td>
		<td align="right"><?php echo number_format($conciliacion['BancoCuentaSaldo']['debe'],2)?></td>
		<td align="right"><?php echo number_format($conciliacion['BancoCuentaSaldo']['haber'],2)?></td>
		<td align="right"><?php echo number_format($conciliacion['BancoCuentaSaldo']['saldo_extracto'],2)?></td>
		<?php 
			if($cuenta['BancoCuenta']['banco_cuenta_saldo_id'] == $conciliacion['BancoCuentaSaldo']['id'] && $cuenta['BancoCuenta']['banco_cuenta_saldo_alta_id'] != $conciliacion['BancoCuentaSaldo']['id']):
		?>
				<td align="center"><?echo $controles->botonGenerico('abrir_conciliacion/'. $conciliacion['BancoCuentaSaldo']['id'] . '/' . $cuenta['BancoCuenta']['id'],'controles/book_open.png', '', null, 'ESTA POR ABRIR LA CONCILIACION.\nESTA SEGURO? ');?></td>
		<?php 
			else:
		?>
		<td></td>
		<?php 
			endif;
		?>
	</tr>
	<?php endforeach; ?>	
</table>
</div>
