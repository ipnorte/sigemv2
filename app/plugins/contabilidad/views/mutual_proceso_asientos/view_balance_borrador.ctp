<h1>BALANCE DE SUMAS Y SALDOS</h1>
<h1>FECHA DESDE :: <?php echo $util->armaFecha($fecha_desde)?></h1>
<h1>FECHA HASTA :: <?php echo $util->armaFecha($aMutualProcesoAsiento['MutualProcesoAsiento']['fecha_hasta'])?></h1>
<hr>



<div class="areaDatoForm">

	<?php 
	echo $controles->botonGenerico('/contabilidad/reportes/balance_sumas_saldos_borrador_xls/' . $procesoId . '/' . $consolidado,'controles/ms_excel.png', null, array('id' => 'xls'));
	echo $controles->botonGenerico('/contabilidad/reportes/balance_sumas_saldos_borrador_pdf/' . $procesoId . '/' . $consolidado,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
	?>

<table align="center" width="100%">

		
	<tr>
		<th style="border-left: 1px solid black;font-size: medium;">CUENTA</th>
		<th style="border-left: 1px solid black;font-size: medium;">DESCRIPCION</th>
		<th align="center" colspan = "2" style="border-left: 1px solid black;border-bottom: 1px solid black; font-size: medium;">SUMAS</th>
		<th align="center" colspan = "2" style="border-left: 1px solid black;border-bottom: 1px solid black; font-size: medium;">SALDOS</th>
		<th style="border-left: 1px solid black;font-size: medium;"></th>
	</tr>

	<tr>
		<th style="border-left: 1px solid black;font-size: medium;"></th>
		<th style="border-left: 1px solid black;font-size: medium;"></th>
		<th style="border-left: 1px solid black;font-size: medium;">DEBE</th>
		<th style="border-left: 1px solid black;font-size: medium;">HABER</th>
		<th style="border-left: 1px solid black;font-size: medium;">DEBE</th>
		<th style="border-left: 1px solid black;font-size: medium;">HABER</th>
		<th style="border-left: 1px solid black;font-size: medium;"></th>
	</tr>
	<?php
	$debe = 0;
	$haber = 0;

	$total_saldo_debe = 0;
	$total_saldo_haber = 0;
	
	foreach ($aMayor as $mayor):
		$debe += $mayor[0]['debe_mayor'];
		$haber += $mayor[0]['haber_mayor'];
		
		$saldo_debe = 0;
		$saldo_haber = 0;
		
		if($mayor[0]['debe_mayor'] > $mayor[0]['haber_mayor']) $saldo_debe = $mayor[0]['debe_mayor'] - $mayor[0]['haber_mayor'];
		else $saldo_haber = $mayor[0]['haber_mayor'] - $mayor[0]['debe_mayor']; 
		
		$total_saldo_debe  += $saldo_debe;
		$total_saldo_haber += $saldo_haber;
		
	?>
		<tr>
			<td style="border-left: 1px solid black;font-size: medium;"><?php echo $controles->linkModalBox($mayor['MutualAsientoRenglon']['cuenta'],array('title' => 'CUENTA: ' . $mayor['MutualAsientoRenglon']['cuenta'] . ' - ' . $mayor['MutualAsientoRenglon']['descripcion'],'url' => '/contabilidad/mutual_proceso_asientos/view_mayor_borrador/'.$procesoId.'/'.$mayor['MutualAsientoRenglon']['co_plan_cuenta_id'] . '/' . $consolidado,'h' => 450, 'w' => 950))?></td>
<!--			<td style="border-left: 1px solid black;font-size: medium;"><?php // echo $mayor['MutualAsientoRenglon']['cuenta']?></td>-->
			<td style="border-left: 1px solid black;font-size: medium;"><?php echo $mayor['MutualAsientoRenglon']['descripcion']?></td>
			<td align="right" style="border-left: 1px solid black;font-size: medium;"><?php echo number_format($mayor[0]['debe_mayor'],2)?></td>
			<td align="right" style="border-left: 1px solid black;font-size: medium;"><?php echo number_format($mayor[0]['haber_mayor'],2)?></td>
			<td align="right" style="border-left: 1px solid black;font-size: medium;"><?php echo ($saldo_debe  > 0 ? number_format($saldo_debe,2)  : '')?></td>
			<td align="right" style="border-left: 1px solid black;font-size: medium;"><?php echo ($saldo_haber > 0 ? number_format($saldo_haber,2) : '')?></td>
			<td>
				<?php 
				echo $controles->botonGenerico('/contabilidad/reportes/libro_mayor_borrador_xls/' . $procesoId . '/' . $mayor['MutualAsientoRenglon']['co_plan_cuenta_id'] . '/' . $consolidado,'controles/ms_excel.png', null, array('id' => 'xls'));
				echo $controles->botonGenerico('/contabilidad/reportes/libro_mayor_borrador_pdf/' . $procesoId . '/' . $mayor['MutualAsientoRenglon']['co_plan_cuenta_id'] . '/' . $consolidado,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
				?>
			</td>
		</tr>
	<?php endforeach; ?>
		<tr>
			<td align="right" colspan="2" style="border-top: 1px solid black;border-left: 1px solid black;font-size: medium;">TOTAL GENERAL:</td>
			<td align="right" style="border-top: 1px solid black;border-left: 1px solid black;font-size: medium;"><?php echo number_format($debe,2)?></td>
			<td align="right" style="border-top: 1px solid black;border-left: 1px solid black;font-size: medium;"><?php echo number_format($haber,2)?></td>
			<td align="right" style="border-top: 1px solid black;border-left: 1px solid black;font-size: medium;"><?php echo number_format($total_saldo_debe,2)?></td>
			<td align="right" style="border-top: 1px solid black;border-left: 1px solid black;font-size: medium;"><?php echo number_format($total_saldo_haber,2)?></td>
			<td style="border-top: 1px solid black;border-left: 1px solid black;font-size: medium;"></td>
		</tr>
</table>

	<?php 
	echo $controles->botonGenerico('/contabilidad/reportes/balance_sumas_saldos_borrador_xls/' . $procesoId . '/' . $consolidado,'controles/ms_excel.png', null, array('id' => 'xls'));
	echo $controles->botonGenerico('/contabilidad/reportes/balance_sumas_saldos_borrador_pdf/' . $procesoId . '/' . $consolidado,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
	?>

</div>	
