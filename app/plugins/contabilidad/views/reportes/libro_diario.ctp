<?php 
App::import('Model', 'contabilidad.AsientoRenglon');
App::import('Model', 'contabilidad.PlanCuenta');

$oAsientoRenglon = new AsientoRenglon();
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
	
	<h3> LIBRO DIARIO (<?php echo $ejercicio['descripcion']?>)</h3>

	<?php echo $frm->create(null,array('action' => 'libro_diario/' . $ejercicio['id'], 'id' => 'form_libro_diario'))?>
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
	
	
	<?php if(!empty($libroDiario)): ?>

		<?php echo $this->renderElement('paginado');?>
		
		
			<div class="areaDatoForm">
				<?php 
				echo $controles->botonGenerico('/contabilidad/reportes/libro_diario_xls/' . $ejercicio['id'] . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/ms_excel.png', null, array('target' => '_blank'));
				echo $controles->botonGenerico('/contabilidad/reportes/libro_diario_pdf/' . $ejercicio['id'] . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
				?>
			
				<table align="center" width="100%">
				
					<col width="100" />
					<col width="250" />
					<col width="250" />
					<col width="100" />
					<col width="100" />
					<col width="100" />
						
					<tr border="0">
						<th style="font-size: small;">FECHA</th>
						<th colspan="2" style="font-size: small;">DESCRIPCION</th>
						<th style="font-size: small;">REFERENCIA</th>
						<th style="font-size: small;">DEBE</th>
						<th style="font-size: small;">HABER</th>
					</tr>
				
					<?php
					foreach ($libroDiario as $asiento):?>
						<tr>
							<td style="border-top: 1px solid black;border-left: 1px solid black;font-size: small;"></td>
							<td colspan="2" align="center" style="border-left: 1px solid black;font-size: small;"><?php echo str_pad('  Nro. Asiento ' . $asiento['Asiento']['nro_asiento'] . '  ', 100, '-', STR_PAD_BOTH)?></td>
							<td style="border-left: 1px solid black;font-size: small;"></td>
							<td style="border-left: 1px solid black;font-size: small;"></td>
							<td style="border-left: 1px solid black;font-size: small;"></td>
						</tr>
						
						<?php
							$fechaPrimera = true; 
							$haberPrimera = true;
							$nAsientoId = $asiento['Asiento']['id'];
							$sql = "SELECT PlanCuenta.cuenta, PlanCuenta.descripcion, AsientoRenglon.*
									FROM co_asiento_renglones AsientoRenglon
									INNER JOIN co_plan_cuentas PlanCuenta
									ON AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id
									WHERE	AsientoRenglon.co_asiento_id = '$nAsientoId'
									ORDER BY AsientoRenglon.debe DESC, AsientoRenglon.id
							";
							
							$renglones = $oAsientoRenglon->query($sql);
							
							foreach($renglones as $renglon):?>
								<tr>
									<?php if($fechaPrimera):
										$fechaPrimera = false;
									?>
									<td align="center" style="border-left: 1px solid black;font-size: small;"><?php echo date('d/m/Y',strtotime($asiento['Asiento']['fecha']))?></td>
									<?php else:?>
										<td style="border-left: 1px solid black;font-size: small;"></td>
									<?php endif;?>
									
									<?php if($renglon['AsientoRenglon']['debe'] > 0):?>
										<td colspan="2" style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['PlanCuenta']['descripcion']?></td>
										<td style="border-left: 1px solid black;font-size: small;"><?php echo $oPlanCuenta->formato_cuenta($renglon['PlanCuenta']['cuenta'], $ejercicio)?></td>
										<td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo number_format($renglon['AsientoRenglon']['debe'],2)?></td>
										<td align="right" style="border-left: 1px solid black;font-size: small;"></td>
									<?php else:?>
										<?php if($haberPrimera):?>
											<?php $haberPrimera = false;?>
											<td align="right" style="border-left: 1px solid black;font-size: small;" size="50%"></td>
										<?php else:?>
											<td align="right" style="border-left: 1px solid black;font-size: small;" size="50%"></td>
										<?php endif;?>	
										<td style=";font-size: small;"><?php echo $renglon['PlanCuenta']['descripcion']?></td>
										<td style="border-left: 1px solid black;font-size: small;"><?php echo $oPlanCuenta->formato_cuenta($renglon['PlanCuenta']['cuenta'], $ejercicio)?></td>
										<td align="right" style="border-left: 1px solid black;font-size: small;"></td>
										<td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo number_format($renglon['AsientoRenglon']['haber'],2)?></td>
									<?php endif;?>
								</tr>
						<?php endforeach; ?>
							<tr>
								<td style="border-left: 1px solid black;font-size: small;"></td>
								<td style="border-left: 1px solid black;font-size: small;"></td>
								<td style="font-size: small;"><?php echo $asiento['Asiento']['referencia']?></td>
								<td style="border-left: 1px solid black;font-size: small;"></td>
								<td style="border-left: 1px solid black;font-size: small;"></td>
								<td style="border-left: 1px solid black;font-size: small;"></td>
							</tr>	
							<tr>
								<td style="border-left: 1px solid black;font-size: small;"></td>
								<td style="border-left: 1px solid black;font-size: small;"></td>
								<td style="font-size: small;"><?php echo $asiento['Asiento']['tipo_documento'] . ' ' . $asiento['Asiento']['nro_documento']?></td>
								<td style="border-left: 1px solid black;font-size: small;"></td>
								<td style="border-left: 1px solid black;font-size: small;"></td>
								<td style="border-left: 1px solid black;font-size: small;"></td>
							</tr>	
							<tr>
								<td style="border-left: 1px solid black;font-size: small;"></td>
								<td style="border-left: 1px solid black;border-bottom: 1px solid black;font-size: small;"></td>
								<td style="border-bottom: 1px solid black;font-size: small;"></td>
								<td style="border-left: 1px solid black;border-bottom: 1px solid black;font-size: small;"></td>
								<td align="right" style="border: 1px solid black;font-size: small;"><?php echo number_format($asiento['Asiento']['debe'],2)?></td>
								<td align="right" style="border: 1px solid black;font-size: small;"><?php echo number_format($asiento['Asiento']['haber'],2)?></td>
							</tr>	
					<?php endforeach; ?>
				</table>
			
				<?php 
				echo $controles->botonGenerico('/contabilidad/reportes/libro_diario_xls/' . $ejercicio['id'] . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/ms_excel.png', null, array('target' => '_blank'));
				echo $controles->botonGenerico('/contabilidad/reportes/libro_diario_pdf/' . $ejercicio['id'] . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
				?>
			
			</div>
		
		<?php echo $this->renderElement('paginado');?>





	<?php endif;?>
<?php endif;?>

	