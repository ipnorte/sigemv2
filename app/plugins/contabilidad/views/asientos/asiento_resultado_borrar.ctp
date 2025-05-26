<?php 
App::import('Model', 'contabilidad.AsientoRenglon');
App::import('Model', 'contabilidad.PlanCuenta');

$oAsientoRenglon = new AsientoRenglon();
$oPlanCuenta = new PlanCuenta();


			
?>

<div class="areaDatoForm">
<?php echo $form->create(null,array('name'=>'formAsiento','id'=>'formAsiento', 'action' => 'asiento_resultado_borrar/' . $asiento['Asiento']['co_ejercicio_id'] ));?>
<?php echo $frm->hidden('Asiento.co_ejercicio_id', array('value' => $asiento['Asiento']['co_ejercicio_id'])) ?>
<?php echo $frm->hidden('Asiento.id', array('value' => $asiento['Asiento']['id'])) ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'BORRAR ASIENTO RESULTADO','URL' => '/contabilidad/asientos/cierre_ejercicio/'.$asiento['Asiento']['co_ejercicio_id']))?>
    
	<table align="center" width="100%">
				
		<col width="100" />
		<col width="50" />
		<col width="450" />
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
				
		<tr>
			<td style="border-top: 1px solid black;border-left: 1px solid black;font-size: small;"></td>
			<td colspan="2" align="center" style="border-left: 1px solid black;font-size: small;"><?php echo str_pad('  Nro. Asiento ' . $asiento['Asiento']['nro_asiento'] . '  ', 70, '-', STR_PAD_BOTH)?></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
		</tr>
						
		<?php
			$fechaPrimera = true; 
			$haberPrimera = true;
							
			foreach($asiento['renglones'] as $renglon):?>
				<tr>
					<?php if($fechaPrimera):
						$fechaPrimera = false;?>
						
						<td align="center" style="border-left: 1px solid black;font-size: small;"><?php echo date('d/m/Y',strtotime($asiento['Asiento']['fecha']))?></td>
					<?php else:?>
						<td style="border-left: 1px solid black;font-size: small;"></td>
					<?php endif;?>
									
					<?php if($renglon['AsientoRenglon']['debe'] > 0):?>
						<td colspan="2" style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['AsientoRenglon']['descripcion_cuenta']?></td>
						<td style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['AsientoRenglon']['codigo_cuenta']?></td>
						<td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo number_format($renglon['AsientoRenglon']['debe'],2)?></td>
						<td align="right" style="border-left: 1px solid black;font-size: small;"></td>
					<?php else:?>
						<?php if($haberPrimera):?>
							<?php $haberPrimera = false;?>
							<td align="right" style="border-left: 1px solid black;font-size: small;" size="50%"></td>
						<?php else:?>
							<td align="right" style="border-left: 1px solid black;font-size: small;" size="50%"></td>
						<?php endif;?>	
						<td style=";font-size: small;"><?php echo $renglon['AsientoRenglon']['descripcion_cuenta']?></td>
						<td style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['AsientoRenglon']['codigo_cuenta']?></td>
						<td align="right" style="border-left: 1px solid black;font-size: small;"></td>
						<td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo number_format($renglon['AsientoRenglon']['haber'],2)?></td>
					<?php endif;?>
				</tr>
		<?php endforeach; ?>
			
		<tr>
			<td style="border-left: 1px solid black;font-size: small;"></td>
			<td colspan="2" style="border-left: 1px solid black;font-size: small;"><?php echo $asiento['Asiento']['referencia']?></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
		</tr>	
		<tr>
			<td style="border-left: 1px solid black;font-size: small;"></td>
			<td colspan="2" style="border-left: 1px solid black;font-size: small;"><?php echo $asiento['Asiento']['tipo_documento'] . ' ' . $asiento['Asiento']['nro_documento']?></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
		</tr>	
		<tr>
			<td style="border-left: 1px solid black;font-size: small;"></td>
			<td colspan="2" style="border-left: 1px solid black;border-bottom: 1px solid black;font-size: small;"></td>
			<td style="border-left: 1px solid black;border-bottom: 1px solid black;font-size: small;"></td>
			<td align="right" style="border: 1px solid black;font-size: small;"><?php echo number_format($asiento['Asiento']['debe'],2)?></td>
			<td align="right" style="border: 1px solid black;font-size: small;"><?php echo number_format($asiento['Asiento']['haber'],2)?></td>
		</tr>	
	</table>
			
</div>





