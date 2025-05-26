<?php echo $this->renderElement('banco_cuenta_movimientos/banco_cuenta_movimiento_header',array('cuenta' => $cuenta));
?>
<div class="areaDatoForm">
<table class="areaDatoForm">

	<tr>
		<th></th>
		<th></th>
		<th colspan=4 align="center" style="border: 1px solid white">PLANILLA DE CAJA</th>
		<th colspan=4 align="center" style="border: 1px solid white">C H E Q U E S</th>
		<th></th>
	</tr>


	<tr>
		<th>NRO.PLANILLA</th>
		<th>FECHA CIERRE</th>
		<th style="border: 1px solid white">SALDO ANTERIOR</th>
		<th style="border: 1px solid white">I N G R E S O</th>
		<th style="border: 1px solid white">E G R E S O</th>
		<th style="border: 1px solid white">T O T A L</th>
		<th style="border: 1px solid white">SALDO AL INICIO</th>
		<th style="border: 1px solid white">I N G R E S O</th>
		<th style="border: 1px solid white">E G R E S O</th>
		<th style="border: 1px solid white">SALDO AL FINAL</th>
		<th></th>
	</tr>
	<?php
        $i = 0;
	foreach ($planillas as $planilla):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr <?php echo $class;?>>
		<?php 
			if($cuenta['BancoCuenta']['banco_cuenta_saldo_alta_id'] == $planilla['BancoCuentaSaldo']['id']):
		?>
		<td>PLANILLA INICIAL</td>
		<?php 
			else:
		?>
<!--		<td><?php //echo $controles->botonGenerico('/cajabanco/banco_cuenta_saldos/view_planilla_caja/'.$planilla['BancoCuentaSaldo']['id'].'/0','controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));?></td>-->
		<td><strong><? echo $html->link('Planilla Nro.: ' . $planilla['BancoCuentaSaldo']['numero'],'/cajabanco/banco_cuenta_saldos/view_planilla_caja/'.$planilla['BancoCuentaSaldo']['id'].'/0', array('target' => '_blank', 'id' => 'pdf'))?></strong>
<!--		<td><?php // echo $controles->linkModalBox($planilla['BancoCuentaSaldo']['numero'],array('title' => 'PLANILLA NRO.' . $planilla['BancoCuentaSaldo']['numero'],'url' => '/cajabanco/banco_cuenta_saldos/view_planilla_caja/'.$planilla['BancoCuentaSaldo']['id'].'/0','h' => 450, 'w' => 950))?></td>-->
		<?php 
			endif;
		?>
		<td align="center"><?php echo date('d/m/Y',strtotime($planilla['BancoCuentaSaldo']['fecha_cierre']))?></td>
		<td align="right"><?php echo number_format($planilla['BancoCuentaSaldo']['saldo_anterior'],2)?></td>
		<td align="right"><?php echo number_format($planilla['BancoCuentaSaldo']['saldo_referencia_1'],2)?></td>
		<td align="right"><?php echo number_format($planilla['BancoCuentaSaldo']['saldo_referencia_2'],2)?></td>
		<td align="right"><?php echo number_format($planilla['BancoCuentaSaldo']['saldo_conciliacion'],2)?></td>
		<td align="right"><?php echo number_format($planilla['BancoCuentaSaldo']['saldo_extracto'],2)?></td>
		<td align="right"><?php echo number_format($planilla['BancoCuentaSaldo']['debe'],2)?></td>
		<td align="right"><?php echo number_format($planilla['BancoCuentaSaldo']['haber'],2)?></td>
		<td align="right"><?php echo number_format($planilla['BancoCuentaSaldo']['saldo_extracto'] + $planilla['BancoCuentaSaldo']['debe'] - $planilla['BancoCuentaSaldo']['haber'],2)?></td>
		<?php 
			if($cuenta['BancoCuenta']['banco_cuenta_saldo_alta_id'] != $planilla['BancoCuentaSaldo']['id']):
		?>
				<td align="center"><?echo $controles->botonGenerico('abrir_planilla/'. $planilla['BancoCuentaSaldo']['id'] . '/' . $cuenta['BancoCuenta']['id'],'controles/book_open.png', '', null, 'ESTA POR ABRIR LA PLANILLA DE CAJA\nNRO.' . $planilla['BancoCuentaSaldo']['numero'] . ' Y POSTERIORES\nESTA SEGURO? ');?></td>
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
