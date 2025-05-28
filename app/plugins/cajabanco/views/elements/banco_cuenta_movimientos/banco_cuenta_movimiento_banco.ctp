<div class="areaDatoForm">
<table class="areaDatoForm">

	<tr border="0">
		<th>#</th>
		<th>FECHA OPER.</th>
		<th>FECHA VENC.</th>
		<th>CONCEPTO</th>
		<th>NRO. OPER.</th>
		<th>REFERENCIA</th>
		<th>DESCRIPCION</th>
		<th>DEBE</th>
		<th>HABER</th>
		<th>SALDO</th>
		<th></th>
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
		<td></td>
	</tr>
	<?php
	$i = 1;
	foreach ($movimientos as $renglon):
		$class = null;
		$style = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		if($renglon['BancoCuentaMovimiento']['anulado'] == 0):
			$saldo += $renglon['BancoCuentaMovimiento']['debe'] - $renglon['BancoCuentaMovimiento']['haber'];
		else:
			$class = ' class="MUTUSICUJUDI"';
			$style = ' style="color:red"';
		endif; 
		$descripcion = ''; // $renglon['BancoCuentaMovimiento']['descripcion'];
		if($renglon['BancoCuentaMovimiento']['reemplazar'] == 1):
			if($renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['tipo'] == 7):
				$descripcion = ' - REEM. ' . $renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['banco_cuenta'] . ' (' . $renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['concepto'] . ')';
			else:
				$descripcion = ' - REEM. ' . $renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['banco_str'] . '-' . $renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['cuenta_str'] . '- CH.NRO. ' . $renglon['BancoCuentaMovimiento']['reemplazado'][0]['BancoCuentaMovimiento']['numero_operacion'];
			endif;
		endif;
	?>
		<tr<?php echo $class;?>>
<!-- 			<td><?php echo $controles->botonGenerico('edit/'.$renglon['BancoCuentaMovimiento']['id'],'controles/page_white_paste.png')?></td> -->
			<td align="right"><?php echo $controles->linkModalBox($renglon['BancoCuentaMovimiento']['id'],array('title' => 'MOVIMIENTO #' . $renglon['BancoCuentaMovimiento']['id'],'url' => '/cajabanco/banco_cuenta_movimientos/edit_comprobante/'.$renglon['BancoCuentaMovimiento']['id'],'h' => 450, 'w' => 750))?></td>
			<td align="center"><?php echo date('d/m/Y',strtotime($renglon['BancoCuentaMovimiento']['fecha_operacion']))?>
			<td align="center"><?php echo date('d/m/Y',strtotime($renglon['BancoCuentaMovimiento']['fecha_vencimiento']))?>
			<td<?php echo $style;?>><strong><?php echo $renglon['BancoCuentaMovimiento']['concepto'] . ($renglon['BancoCuentaMovimiento']['anulado'] == 1 ? ' (ANULADO)' : '')?></strong></td>
			<td align="right"><?php echo $renglon['BancoCuentaMovimiento']['numero_operacion'] ?>
			<td><?php echo $renglon['BancoCuentaMovimiento']['destinatario'] ?></td>
<!-- 			<td><?php echo $descripcion ?></td> -->
			<?php if($renglon['BancoCuentaMovimiento']['recibo_id'] > 0 || $renglon['BancoCuentaMovimiento']['orden_pago_id'] > 0):?>
				<?php 
				$title = $renglon['BancoCuentaMovimiento']['descripcion'];
				if($renglon['BancoCuentaMovimiento']['recibo_id'] > 0):
					$url =  '/clientes/recibos/imprimir_recibo_pdf/' . $renglon['BancoCuentaMovimiento']['recibo_id'];
				else:
					$url =  '/proveedores/orden_pagos/imprimir_orden_pago_pdf/' . $renglon['BancoCuentaMovimiento']['orden_pago_id'];
				endif;
			?>
<!--				<td style="color:blue;"><?php echo $controles->linkModalBox($renglon['BancoCuentaMovimiento']['descripcion'],array('title' => $title,'url' => $url,'h' => 450, 'w' => 750)) . $descripcion?></td> -->
				<td style="color:blue;"><?php echo $controles->btnImprimirPDF($renglon['BancoCuentaMovimiento']['descripcion'],$url,'blank') . $descripcion?></td>
			<?php else:?>
				<td><?php echo $renglon['BancoCuentaMovimiento']['descripcion']?>
			<?php endif;?>
			<td align="right"><?php echo ($renglon['BancoCuentaMovimiento']['debe'] == 0  ? '' : number_format($renglon['BancoCuentaMovimiento']['debe'],2))?></td>
			<td align="right"><?php echo ($renglon['BancoCuentaMovimiento']['haber'] == 0 ? '' : number_format($renglon['BancoCuentaMovimiento']['haber'],2))?></td>
			<td align="right"><?php echo number_format($saldo,2)?></td>
			<?php if($renglon['BancoCuentaMovimiento']['tipo'] == 1 && $conciliacion == 0): ?>
				<td align="center"><?php echo ($renglon['BancoCuentaMovimiento']['anulado'] == 0 ? $controles->botonGenerico('reemplazar_cheque/'. $renglon['BancoCuentaMovimiento']['id'],'controles/12-em-cross.png', '', null, 'ESTA POR REEMPLAZAR EL CHEQUE\n NUMERO: ' . $renglon['BancoCuentaMovimiento']['numero_operacion']) : '');?></td>
			<?php else: ?>
				<td></td>
			<?php endif; ?>
		</tr>
	<?php endforeach; ?>	
</table>
</div>
