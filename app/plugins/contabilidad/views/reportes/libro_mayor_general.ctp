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
	<h3> LIBRO MAYOR (<?php echo $ejercicio['descripcion']?>)</h3>

	<?php echo $frm->create(null,array('action' => 'libro_mayor_general/' . $ejercicio['id'], 'id' => 'form_libro_mayor'))?>
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
	
	
	<?php if(!empty($libroMayorGeneral)): ?>

			<div class="areaDatoForm">
				<?php 
				echo $controles->botonGenerico('/contabilidad/reportes/libro_mayor_general_xls/' . $ejercicio['id'] . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/ms_excel.png', null, array('target' => '_blank'));
				echo $controles->botonGenerico('/contabilidad/reportes/libro_mayor_general_pdf/' . $ejercicio['id'] . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
				?>
			
				<table align="center" width="100%">
				
					<tr border="0">
						<th style="font-size: small;">CUENTA</th>
						<th style="font-size: small;">DESCRIPCION</th>
						<th style="font-size: small;">FECHA</th>
						<th style="font-size: small;">NRO.ASIENTO</th>
						<th style="font-size: small;">REFERENCIA</th>
						<th style="font-size: small;">DEBE</th>
						<th style="font-size: small;">HABER</th>
						<th style="font-size: small;">SALDO</th>
					</tr>
				
					<?php
					$saldo = 0;
					foreach ($libroMayorGeneral as $libroMayor):
						$saldo += $libroMayor['AsientoRenglon']['debe'] - $libroMayor['AsientoRenglon']['haber'];  
					?>
						<tr>
							<td style="border-left: 1px solid black;font-size: small;"><?php echo $oPlanCuenta->formato_cuenta($libroMayor['PlanCuenta']['cuenta'], $ejercicio)?></td>
							<td style="border-left: 1px solid black;font-size: small;"><?php echo $libroMayor['PlanCuenta']['descripcion']?></td>
							<td align="center" style="border-left: 1px solid black;font-size: small;"><?php echo date('d/m/Y',strtotime($libroMayor['Asiento']['fecha']))?></td>
							<td align="center" style="border-left: 1px solid black;font-size: small;"><?php echo $libroMayor['Asiento']['nro_asiento']?></td>
							<td style="border-left: 1px solid black;font-size: small;"><?php echo $libroMayor['Asiento']['referencia']?></td>
							<td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo ($libroMayor['AsientoRenglon']['debe'] > 0 ? number_format($libroMayor['AsientoRenglon']['debe'],2) : '')?></td>
							<td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo ($libroMayor['AsientoRenglon']['haber'] > 0 ? number_format($libroMayor['AsientoRenglon']['haber'],2) : '')?></td>
							<td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo number_format($saldo,2)?></td>
						</tr>
						
					<?php endforeach; ?>
				</table>
			
				<?php 
				echo $controles->botonGenerico('/contabilidad/reportes/libro_mayor_general_xls/' . $ejercicio['id'] . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/ms_excel.png', null, array('target' => '_blank'));
				echo $controles->botonGenerico('/contabilidad/reportes/libro_mayor_general_pdf/' . $ejercicio['id'] . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
				?>
			
			</div>

	<?php endif;?>

<?php endif;?>

