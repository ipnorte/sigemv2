<?php 
$contenido = "Content-Disposition: attachment; filename=Libro-Diario.xls";
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Libro-Diario.xls");
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");
App::import('Model', 'contabilidad.AsientoRenglon');
App::import('Model', 'contabilidad.PlanCuenta');

$oPlanCuenta = new PlanCuenta();
$oAsientoRenglon = new AsientoRenglon();


?>

<h1>LIBRO DIARIO</h1>
<h1>FECHA DESDE :: <?php echo $util->armaFecha($ejercicio['fecha_desde'])?></h1>
<h1>FECHA HASTA :: <?php echo $util->armaFecha($ejercicio['fecha_hasta'])?></h1>

<table align="center" width="100%">

	<col width="100" />
	<col width="420" />
	<col width="420" />
	<col width="100" />
	<col width="100" />
		
	<tr border="0">
		<th style="border: 1px solid black;font-size: small;">FECHA</th>
		<th colspan="2" style="border: 1px solid black;font-size: small;">DESCRIPCION</th>
		<th style="border: 1px solid black;font-size: small;">REFERENCIA</th>
		<th style="border: 1px solid black;font-size: small;">DEBE</th>
		<th style="border: 1px solid black;font-size: small;">HABER</th>
	</tr>

	<?php
		$nDebe = 0;
                $nHaber = 0;
		foreach ($aTemporal as $asiento):?>
			<tr>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td colspan="2" align="center" style="border-right: 1px solid black;font-size: small;"><?php echo str_pad('  Nro. Asiento. ' . $asiento['AsincronoTemporal']['entero_1'] . '  ', 90, '-', STR_PAD_BOTH)?></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
			</tr>
			
			<?php
				$fechaPrimera = true; 

				foreach($asiento['AsincronoTemporalDetalle'] as $renglon):
                                    
                                    if($renglon['decimal_1'] > 0):
                                        $nDebe += $renglon['decimal_1'];?>
				
					<tr>
						<?php if($fechaPrimera):
							$fechaPrimera = false;?>
							<td align="center" style="border-right: 1px solid black;font-size: small;"><?php echo date('d/m/Y',strtotime($asiento['AsincronoTemporal']['texto_1']))?></td>
						<?php else:?>
							<td style="border-right: 1px solid black;font-size: small;"></td>
						<?php endif;?>
						
						<td colspan="2" style="border-right: 1px solid black;font-size: small;"><?php echo $renglon['texto_2']?></td>
						<td style="border-right: 1px solid black;font-size: small;"><?php echo $oPlanCuenta->formato_cuenta($renglon['texto_1'], $ejercicio)?></td>
						<td align="right" style="border-right: 1px solid black;font-size: small;"><?php echo number_format($renglon['decimal_1'],2)?></td>
						<td align="right" style="border-right: 1px solid black;font-size: small;"></td>
					</tr>
                                    <?php endif;
                                endforeach;
				
			
			
				foreach($asiento['AsincronoTemporalDetalle'] as $renglon):
                                    
                                    if($renglon['decimal_2'] > 0):
                                        $nHaber += $renglon['decimal_2'];?>
				
					<tr>
						<td style="border-right: 1px solid black;font-size: small;"></td>
						<td colspan="2" style="border-right: 1px solid black;font-size: small;"><?php echo $renglon['texto_2']?></td>
						<td style="border-right: 1px solid black;font-size: small;"><?php echo $oPlanCuenta->formato_cuenta($renglon['texto_1'], $ejercicio)?></td>
						<td align="right" style="border-right: 1px solid black;font-size: small;"></td>
						<td align="right" style="border-right: 1px solid black;font-size: small;"><?php echo number_format($renglon['decimal_2'],2)?></td>
					</tr>
                                    <?php endif;
                                endforeach; 
			
		endforeach; ?>

                <tr>
                    <td style="border-right: 1px solid black;font-size: small;"></td>
                    <td colspan="2" style="border-right: 1px solid black;font-size: small;"></td>
                    <td style="border-right: 1px solid black;font-size: small;">TOTAL GENERAL:</td>
                    <td align="right" style="border-right: 1px solid black;font-size: small;"><?php echo number_format($nDebe,2)?></td>
                    <td align="right" style="border-right: 1px solid black;font-size: small;"><?php echo number_format($nHaber,2)?></td>
		</tr>
</table>
