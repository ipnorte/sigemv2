<?php 
App::import('Model', 'contabilidad.PlanCuenta');

$oPlanCuenta = new PlanCuenta();


			
?>

<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'REPORTES'))?>
<div id="FormSearch">
<?php echo $form->create(null,array('name'=>'frmReportes','id'=>'frmReportes', 'action' => 'index'));?>
	<table>
		<tr>
			<td><?php echo $this->renderElement('combo_ejercicio',array(
												'plugin'=>'contabilidad',
												'label' => " ",
												'model' => 'ejercicio.id',
												'disabled' => false,
												'empty' => false,
												'selected' => $ejercicio['id']))?>
			</td>			
			<td><?php echo $frm->submit('SELECCIONAR',array('class' => 'btn_consultar'));?></td>
		</tr>
	</table>
<?php echo $frm->end();?> 
</div>



<?php if(!empty($ejercicio)): ?>
	<?php echo $this->renderElement('opciones_reportes',array(
													'plugin'=>'contabilidad',
													'label' => " ",
													'model' => 'reporte.id',
													'disabled' => false,
													'empty' => false
	));?>

	<h3> LIBRO SUMAS Y SALDOS (<?php echo $ejercicio['descripcion']?>)</h3>

	<?php echo $frm->create(null,array('action' => 'libro_suma_saldos/' . $ejercicio['id'], 'id' => 'form_suma_saldos'))?>
	<div class="areaDatoForm">
	
		<table class="tbl_form">
		
			<tr>
				<td>FECHA DESDE:</td>
				<td colspan="2"><?php echo $frm->calendar('Reporte.fecha_desde', '', $fecha_desde, date("Y", strtotime($fecha_desde)), date("Y", strtotime($fecha_hasta)))?></td>
			</tr>
			
			<tr>
				<td>FECHA HASTA:</td>
				<td colspan="2"><?php echo $frm->calendar('Reporte.fecha_hasta', '', $fecha_hasta, date("Y", strtotime($fecha_desde)), date("Y", strtotime($fecha_hasta)))?></td>
			</tr>
			
			<tr>
				<td colspan="3"><?php echo $frm->submit("ACEPTAR")?></td>
			</tr>
		
		</table>
		
	</div>
	<?php echo $frm->end()?>
	
	<?php if(!empty($libroSumasSaldos)): ?>

		<div class="areaDatoForm">
		
			<?php 
			echo $controles->botonGenerico('/contabilidad/reportes/balance_sumas_saldos_xls/' . $ejercicio_id . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/ms_excel.png', null, array('id' => 'xls'));
			echo $controles->botonGenerico('/contabilidad/reportes/balance_sumas_saldos_pdf/' . $ejercicio_id . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
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
				
				foreach ($libroSumasSaldos as $sumasSaldos):
					$debe += $sumasSaldos[0]['debe'];
					$haber += $sumasSaldos[0]['haber'];
					
					$saldo_debe = 0;
					$saldo_haber = 0;
					
					if($sumasSaldos[0]['debe'] > $sumasSaldos[0]['haber']) $saldo_debe = $sumasSaldos[0]['debe'] - $sumasSaldos[0]['haber'];
					else $saldo_haber = $sumasSaldos[0]['haber'] - $sumasSaldos[0]['debe']; 
					
					$total_saldo_debe  += $saldo_debe;
					$total_saldo_haber += $saldo_haber;
					
				?>
					<tr>
						<td style="border-left: 1px solid black;font-size: medium;"><?php echo $controles->linkModalBox($oPlanCuenta->formato_cuenta($sumasSaldos['PlanCuenta']['cuenta'], $ejercicio),array('title' => 'CUENTA: ' . $oPlanCuenta->formato_cuenta($sumasSaldos['PlanCuenta']['cuenta'], $ejercicio) . ' - ' . $sumasSaldos['PlanCuenta']['descripcion'],'url' => '/contabilidad/reportes/view_mayor_general/'.$ejercicio_id.'/'.$sumasSaldos['PlanCuenta']['id'],'h' => 450, 'w' => 950))?></td>
						<td style="border-left: 1px solid black;font-size: medium;"><?php echo $sumasSaldos['PlanCuenta']['descripcion']?></td>
						<td align="right" style="border-left: 1px solid black;font-size: medium;"><?php echo number_format($sumasSaldos[0]['debe'],2)?></td>
						<td align="right" style="border-left: 1px solid black;font-size: medium;"><?php echo number_format($sumasSaldos[0]['haber'],2)?></td>
						<td align="right" style="border-left: 1px solid black;font-size: medium;"><?php echo ($saldo_debe  > 0 ? number_format($saldo_debe,2)  : '')?></td>
						<td align="right" style="border-left: 1px solid black;border-right: 1px solid black;font-size: medium;"><?php echo ($saldo_haber > 0 ? number_format($saldo_haber,2) : '')?></td>
						<td>
							<?php 
							echo $controles->botonGenerico('/contabilidad/reportes/libro_mayor_general_xls/' . $ejercicio_id . '/' . $fecha_desde . '/' . $fecha_hasta . '/' . $sumasSaldos['PlanCuenta']['id'],'controles/ms_excel.png', null, array('id' => 'xls'));
							echo $controles->botonGenerico('/contabilidad/reportes/libro_mayor_general_pdf/' . $ejercicio_id . '/' . $fecha_desde . '/' . $fecha_hasta . '/' . $sumasSaldos['PlanCuenta']['id'],'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
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
			echo $controles->botonGenerico('/contabilidad/reportes/balance_sumas_saldos_xls/' . $ejercicio_id . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/ms_excel.png', null, array('id' => 'xls'));
			echo $controles->botonGenerico('/contabilidad/reportes/balance_sumas_saldos_pdf/' . $ejercicio_id . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
			?>
		
		</div>	
	
	<?php endif;?>
	
<?php endif;?>

