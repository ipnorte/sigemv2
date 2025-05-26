<?php echo $this->renderElement('banco_cuenta_movimientos/banco_cuenta_movimiento_header',array('cuenta' => $cuenta))?>
<h4>IMPRIMIR CHEQUES</h4>
<div class="areaDatoForm">
<table class="areaDatoForm">

	<tr border="0">
		<th>NRO. CHEQUE</th>
		<th>FECHA OPER.</th>
		<th>FECHA VENC.</th>
		<th>DESTINATARIO</th>
		<th>DESCRIPCION</th>
		<th>IMPORTE</th>
		<th></th>
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
			<td align="right"><?php echo $renglon['BancoCuentaMovimiento']['numero_operacion'] ?>
			<td align="center"><?php echo date('d/m/Y',strtotime($renglon['BancoCuentaMovimiento']['fecha_operacion']))?>
			<td align="center"><?php echo date('d/m/Y',strtotime($renglon['BancoCuentaMovimiento']['fecha_vencimiento']))?>
			<td><?php echo $renglon['BancoCuentaMovimiento']['destinatario'] ?></td>
			<td><?php echo $renglon['BancoCuentaMovimiento']['descripcion']?>
			<td align="right"><?php echo number_format($renglon['BancoCuentaMovimiento']['importe'],2)?></td>
                        <td><?php echo $controles->botonGenerico('/config/configurar_impresiones/imprimir_cheque_pdf/' . $renglon['BancoCuentaMovimiento']['id'],'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));?></td>
<!--			<td><input type="checkbox" <?php echo $checked?> name="data[BancoCuentaMovimiento][check][<?php echo $i ?>]"  id="BancoCuentaMovimientoCheck<?php echo $i?>" onclick="chkOnclick('<?php echo $i?>','<?php echo $renglon['BancoCuentaMovimiento']['id']?>','<?php echo $renglon['BancoCuentaMovimiento']['banco_cuenta_id']?>')"/></td> -->
		</tr>
	<?php endforeach; ?>	
</table>
</div>
