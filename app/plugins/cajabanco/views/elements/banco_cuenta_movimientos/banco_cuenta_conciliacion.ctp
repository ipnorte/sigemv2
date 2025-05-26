<script language="Javascript" type="text/javascript">
	
	function chkOnclick(renglon, movimientoId, cuentaId)
	{
		var checkbox = $('BancoCuentaMovimientoCheck' + renglon);
		var checked = 0;
		
		if(checkbox.checked == true) checked = 1;
		
		grabarMovimiento(movimientoId, checked, cuentaId, renglon);

		return true;
	}	


	function grabarMovimiento(movimientoId, checked, cuentaId, renglon)
	{
		new Ajax.Updater
		(
			'div_conciliacion',
			'<?php echo $this->base?>/cajabanco/banco_cuenta_movimientos/grabar_movimiento/'+movimientoId+'/'+checked+'/'+cuentaId, 
			{
				asynchronous:true, 
				evalScripts:true,
				onLoading:function(request) 
						{
							$('msjAjax_' + renglon).show();
						},
				onComplete:function(request) 
						{
							$('msjAjax_' + renglon).hide();
						}, 
				requestHeaders:['X-Update', 'div_conciliacion']
			}
		);

		return true;

	}
</script>

<div class="areaDatoForm">
<table class="areaDatoForm">

	<tr border="0">
		<th>#</th>
		<th>FECHA OPER.</th>
		<th>FECHA VENC.</th>
                <th>DESCRIPCION</th>
		<th>CONCEPTO</th>
		<th>NRO. OPER.</th>
		<th></th>
		<th>DEBE</th>
		<th>HABER</th>
		<th></th>
	</tr>
	<?php
	$i = 1;
	foreach ($movimientos as $renglon):
		$class = null;
		$style = null;
		$checked = ' ';
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
		
		if($renglon['BancoCuentaMovimiento']['conciliado'] == 1) $checked = ' checked';
	?>
		<tr<?php echo $class;?>>
			<td align="right"><?php echo $controles->linkModalBox($renglon['BancoCuentaMovimiento']['id'],array('title' => 'MOVIMIENTO #' . $renglon['BancoCuentaMovimiento']['id'],'url' => '/cajabanco/banco_cuenta_movimientos/edit_comprobante/'.$renglon['BancoCuentaMovimiento']['id'],'h' => 450, 'w' => 750))?></td>
			<td align="center"><?php echo date('d/m/Y',strtotime($renglon['BancoCuentaMovimiento']['fecha_operacion']))?></td>
			<td align="center"><?php echo date('d/m/Y',strtotime($renglon['BancoCuentaMovimiento']['fecha_vencimiento']))?></td>
                        <td><strong><?php echo $renglon['BancoCuentaMovimiento']['destinatario'] . '-' . $renglon['BancoCuentaMovimiento']['descripcion']?></td>
			<td<?php echo $style;?>><strong><?php echo $renglon['BancoCuentaMovimiento']['concepto'] . ($renglon['BancoCuentaMovimiento']['anulado'] == 1 ? ' (ANULADO)' : '')?></strong></td>
			<td align="right"><?php echo $renglon['BancoCuentaMovimiento']['numero_operacion'] ?></td>
			<td><?php echo $controles->ajaxLoader('msjAjax_' . $i,'GRABANDO...')?></td>
			<td align="right"><?php echo ($renglon['BancoCuentaMovimiento']['debe'] == 0  ? '' : number_format($renglon['BancoCuentaMovimiento']['debe'],2))?></td>
			<td align="right"><?php echo ($renglon['BancoCuentaMovimiento']['haber'] == 0 ? '' : number_format($renglon['BancoCuentaMovimiento']['haber'],2))?></td>
			<td><input type="checkbox" <?php echo $checked?> name="data[BancoCuentaMovimiento][check][<?php echo $i ?>]"  id="BancoCuentaMovimientoCheck<?php echo $i?>" onclick="chkOnclick('<?php echo $i?>','<?php echo $renglon['BancoCuentaMovimiento']['id']?>','<?php echo $renglon['BancoCuentaMovimiento']['banco_cuenta_id']?>')"/></td>
		</tr>
	<?php endforeach; ?>	
</table>
</div>



