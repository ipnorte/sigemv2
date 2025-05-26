<div class="areaDatoForm">
<table class="areaDatoForm">

	<tr border="0">
		<th>#</th>
		<th>FECHA</th>
		<th>CONCEPTO</th>
		<th>NRO.OPERACION</th>
		<th>REFERENCIA</th>
		<th>DESCRIPCION</th>
		<th>DEBE</th>
		<th>HABER</th>
		<th>SALDO</th>
	</tr>
	<tr class="altrow">
		<td></td>
		<td align="center"><?php echo date('d/m/Y',strtotime($cuenta['BancoCuenta']['fecha_conciliacion']))?></td>
		<td>SALDO ANTERIOR</td>
		<td></td>
		<td></td>
		<td></td>
		<td align="right"><?php echo number_format($cuenta['BancoCuenta']['importe_conciliacion'],2) ?></td>
		<td></td>
		<td align="right"><?php echo number_format($cuenta['BancoCuenta']['importe_conciliacion'],2) ?></td>
	</tr>
	<?php
	$saldo = $cuenta['BancoCuenta']['importe_conciliacion'];
	$i = 1;
	foreach ($movimientos as $renglon):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		$saldo += $renglon['BancoCuentaMovimiento']['debe'] - $renglon['BancoCuentaMovimiento']['haber']; 
	?>
		<tr<?php echo $class;?> >
			<td align="right"><?php echo $controles->linkModalBox($renglon['BancoCuentaMovimiento']['id'],array('title' => 'MOVIMIENTO #' . $renglon['BancoCuentaMovimiento']['id'],'url' => '/cajabanco/banco_cuenta_movimientos/edit_comprobante/'.$renglon['BancoCuentaMovimiento']['id'],'h' => 450, 'w' => 750))?></td>
			<td align="center"><?php echo $controles->linkModalBox(date('d/m/Y',strtotime($renglon['BancoCuentaMovimiento']['fecha_operacion'])),array('title' => 'FECHA: ' . date('d/m/Y',strtotime($renglon['BancoCuentaMovimiento']['fecha_operacion'])),'url' => '/cajabanco/banco_cuenta_movimientos/edit_fecha/'.$renglon['BancoCuentaMovimiento']['id'],'h' => 450, 'w' => 750))?></td>
			<td><strong><?php echo $renglon['BancoCuentaMovimiento']['concepto']?></strong></td>
			<td align="right"><?php echo $renglon['BancoCuentaMovimiento']['numero_operacion'] ?>
			<td><?php echo $renglon['BancoCuentaMovimiento']['destinatario'] ?></td>
			<?php if($renglon['BancoCuentaMovimiento']['recibo_id'] > 0 || $renglon['BancoCuentaMovimiento']['orden_pago_id'] > 0):?>
				<?php 
				$title = $renglon['BancoCuentaMovimiento']['descripcion'];
				if($renglon['BancoCuentaMovimiento']['recibo_id'] > 0):
					$url =  '/clientes/recibos/imprimir_recibo_pdf/' . $renglon['BancoCuentaMovimiento']['recibo_id'];
				else:
					$url =  '/proveedores/orden_pagos/imprimir_orden_pago_pdf/' . $renglon['BancoCuentaMovimiento']['orden_pago_id'];
				endif;
				?>
<!-- 				<td><?php echo $controles->linkModalBox($renglon['BancoCuentaMovimiento']['descripcion'],array('title' => $title,'url' => $url,'h' => 450, 'w' => 750))?></td> -->
				<td style="color:blue;"><?php echo $controles->btnImprimirPDF($renglon['BancoCuentaMovimiento']['descripcion'],$url,'blank')?></td>
			<?php else:?>
				<td><?php echo $renglon['BancoCuentaMovimiento']['descripcion']?>
			<?php endif;?>
<!-- 			<td><?php echo $renglon['BancoCuentaMovimiento']['descripcion'] ?></td> -->
			<td align="right"><?php echo ($renglon['BancoCuentaMovimiento']['debe'] == 0  ? '' : number_format($renglon['BancoCuentaMovimiento']['debe'],2))?></td>
			<td align="right"><?php echo ($renglon['BancoCuentaMovimiento']['haber'] == 0 ? '' : number_format($renglon['BancoCuentaMovimiento']['haber'],2))?></td>
			<td align="right"><?php echo number_format($saldo,2)?></td>
		</tr>
	<?php endforeach; ?>	
</table>
</div>
