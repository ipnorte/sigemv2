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

	<h3> BALANCE GENERAL (<?php echo $ejercicio['descripcion']?>)</h3>

	<?php echo $frm->create(null,array('action' => 'balance_general/' . $ejercicio['id'], 'id' => 'form_balance_general'))?>
	<div class="areaDatoForm">
	
		<table class="tbl_form">
		
			<tr>
				<td>FECHA DESDE:</td>
				<td colspan="2"><?php echo $frm->calendar('Balance.fecha_desde', '', $fecha_desde, date("Y", strtotime($fecha_desde)), date("Y", strtotime($fecha_hasta)))?></td>
			</tr>
			
			<tr>
				<td>FECHA HASTA:</td>
				<td colspan="2"><?php echo $frm->calendar('Balance.fecha_hasta', '', $fecha_hasta, date("Y", strtotime($fecha_desde)), date("Y", strtotime($fecha_hasta)))?></td>
			</tr>
			
			<tr>
				<td>NIVEL DEL BALANCE:</td>
				<td colspan="2"><?php echo $frm->input('Balance.nivel',array('type'=>'select','options'=>$aNivel));?></td>				
			</tr>
			
			<tr>
				<td colspan="3"><?php echo $frm->submit("ACEPTAR")?></td>
			</tr>
		
		</table>
		
	</div>
	<?php echo $frm->end()?>
	
	<?php if(!empty($aBalanceGeneral)): ?>

		<div class="areaDatoForm">
		
			<?php 
			echo $controles->botonGenerico('/contabilidad/reportes/balance_general_xls/' . $ejercicio_id . '/' . $fecha_desde . '/' . $fecha_hasta . '/' . $nivel, 'controles/ms_excel.png', null, array('id' => 'xls'));
			echo $controles->botonGenerico('/contabilidad/reportes/balance_general_pdf/' . $ejercicio_id . '/' . $fecha_desde . '/' . $fecha_hasta . '/' . $nivel, 'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
			?>
		
			<table align="center" width="100%">
			
					<col width="100" />
					<col width="450" />
					<col width="120" />
					<col width="120" />
					<col width="120" />
					<col width="120" />
					<col width="120" />
					
				<tr>
					<th style="border-left: 1px solid black;font-size: medium;">CUENTA</th>
					<th style="border-left: 1px solid black;font-size: medium;">DESCRIPCION</th>
					<th align="center" style="border-bottom: 1px solid black; font-size: medium;"></th>
					<th align="center" style="border-bottom: 1px solid black; font-size: medium;"></th>
					<th align="center" style="border-bottom: 1px solid black; font-size: medium;"></th>
					<th align="center" style="border-bottom: 1px solid black; font-size: medium;"></th>
					<th align="center" style="border-bottom: 1px solid black; font-size: medium;"></th>
					<th align="center" style="border-bottom: 1px solid black; font-size: medium;"></th>
				</tr>
			
				<?php
				$debe = 0;
				$haber = 0;
			
				$total_saldo_debe = 0;
				$total_saldo_haber = 0;
				
				foreach ($aBalanceGeneral as $balanceGeneral):
					
					$saldo = $balanceGeneral['PlanCuenta']['acumulado_debe'] - $balanceGeneral['PlanCuenta']['acumulado_haber'];
					$colSpan = 7 - $balanceGeneral['PlanCuenta']['nivel'];
					$colDif = 6 - $colSpan;
				?>
					<tr>
						<td style="border-left: 1px solid black;font-size: medium;"><?php echo $oPlanCuenta->formato_cuenta($balanceGeneral['PlanCuenta']['cuenta'], $ejercicio)?></td>
						<td style="border-left: 1px solid black;font-size: medium;"><?php echo $balanceGeneral['PlanCuenta']['descripcion']?></td>
						<td colspan = "<?php echo $colSpan ?>" align="right" style="border-left: 1px solid black;font-size: medium;"><?php echo number_format($saldo,2)?></td>
						<?php if($colDif > 0):?>
							<td colspan = "<?php echo $colDif ?>" align="right"></td>
						<?php endif;?>
						
					</tr>
				<?php endforeach; ?>
			</table>
		
			<?php 
			echo $controles->botonGenerico('/contabilidad/reportes/balance_sumas_saldos_xls/' . $ejercicio_id . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/ms_excel.png', null, array('id' => 'xls'));
			echo $controles->botonGenerico('/contabilidad/reportes/balance_sumas_saldos_pdf/' . $ejercicio_id . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
			?>
		
		</div>	
	
	<?php endif;?>
	
<?php endif;?>

