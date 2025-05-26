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

$sql = "SELECT Asiento.*
		FROM co_asientos Asiento
		WHERE Asiento.co_ejercicio_id = $ejercicio_id AND Asiento.fecha >= '$fecha_desde' AND Asiento.fecha <= '$fecha_hasta' AND Asiento.borrado = 0
		ORDER BY Asiento.fecha, Asiento.nro_asiento
		LIMIT 1
";

$asientos = $oAsientoRenglon->query($sql);

?>

<h1>LIBRO DIARIO</h1>
<h1>FECHA DESDE :: <?php echo $util->armaFecha($asientos[0]['Asiento']['fecha'])?></h1>
<h1>FECHA HASTA :: <?php echo $util->armaFecha($fecha_hasta)?></h1>

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
	$LIMIT = 500;
    $OFFSET = 0;
    $INCREMET = 500;
	
	while(true):
		$sql = "SELECT Asiento.*
				FROM co_asientos Asiento
				WHERE Asiento.co_ejercicio_id = $ejercicio_id AND Asiento.fecha >= '$fecha_desde' AND Asiento.fecha <= '$fecha_hasta' AND Asiento.borrado = 0
				ORDER BY Asiento.fecha, Asiento.nro_asiento
				LIMIT $OFFSET, $LIMIT
		";

		$asientos = $oAsientoRenglon->query($sql);

		if(empty($asientos)) break;
		
		$OFFSET += $INCREMET;
		
		foreach ($asientos as $asiento):?>
			<tr>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td colspan="2" align="center" style="border-right: 1px solid black;font-size: small;"><?php echo str_pad('  Nro. Asiento. ' . $asiento['Asiento']['nro_asiento'] . '  ', 90, '-', STR_PAD_BOTH)?></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
			</tr>
			
			<?php
				$fechaPrimera = true; 
				$haberPrimera = true;
				$nAsientoId = $asiento['Asiento']['id'];
				
				// Primero traigo lo que tiene el debe
				$sql = "SELECT	PlanCuenta.cuenta, PlanCuenta.descripcion, AsientoRenglon.*
						FROM	co_asiento_renglones AsientoRenglon
						INNER JOIN co_plan_cuentas PlanCuenta
						ON AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id
						WHERE	AsientoRenglon.co_asiento_id = '$nAsientoId' AND AsientoRenglon.debe > 0
				";
				
				$debe_renglones = $oAsientoRenglon->query($sql);

				foreach($debe_renglones as $renglon):?>
					<tr>
						<?php if($fechaPrimera):
							$fechaPrimera = false;?>
							<td align="center" style="border-right: 1px solid black;font-size: small;"><?php echo date('d/m/Y',strtotime($asiento['Asiento']['fecha']))?></td>
						<?php else:?>
							<td style="border-right: 1px solid black;font-size: small;"></td>
						<?php endif;?>
						
						<td colspan="2" style="border-right: 1px solid black;font-size: small;"><?php echo $renglon['PlanCuenta']['descripcion']?></td>
						<td style="border-right: 1px solid black;font-size: small;"><?php echo $oPlanCuenta->formato_cuenta($renglon['PlanCuenta']['cuenta'], $ejercicio)?></td>
						<td align="right" style="border-right: 1px solid black;font-size: small;"><?php echo number_format($renglon['AsientoRenglon']['debe'],2)?></td>
						<td align="right" style="border-right: 1px solid black;font-size: small;"></td>
					</tr>
			<?php endforeach;
				
			
			
				// Aca traigo lo que tiene el haber
				$sql = "SELECT	PlanCuenta.cuenta, PlanCuenta.descripcion, AsientoRenglon.*
						FROM	co_asiento_renglones AsientoRenglon
						INNER JOIN co_plan_cuentas PlanCuenta
						ON AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id
						WHERE	AsientoRenglon.co_asiento_id = '$nAsientoId' AND AsientoRenglon.haber > 0
				";
				
				$haber_renglones = $oAsientoRenglon->query($sql);

				foreach($haber_renglones as $renglon):?>
					<tr>
						<td style="border-right: 1px solid black;font-size: small;"></td>
						<td align="right" style="font-size: small;" size="50%"></td>
						
						<td style="border-right: 1px solid black;font-size: small;"><?php echo $renglon['PlanCuenta']['descripcion']?></td>
						<td style="border-right: 1px solid black;font-size: small;"><?php echo $oPlanCuenta->formato_cuenta($renglon['PlanCuenta']['cuenta'], $ejercicio)?></td>
						<td align="right" style="border-right: 1px solid black;font-size: small;"></td>
						<td align="right" style="border-right: 1px solid black;font-size: small;"><?php echo number_format($renglon['AsientoRenglon']['haber'],2)?></td>
					</tr>
			<?php endforeach; ?>
			
			
			
			
			<tr>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td colspan="2" style="border-right: 1px solid black;font-size: small;"><?php echo $asiento['Asiento']['referencia']?></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
			</tr>	
			
			<tr>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td colspan="2" style="border-right: 1px solid black;font-size: small;"><?php echo ltrim(rtrim($asiento['Asiento']['tipo_documento'] . ' ' . $asiento['Asiento']['nro_documento']))?></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
			</tr>	
			
			<tr>
				<?php if($asiento['Asiento']['debe'] != $asiento['Asiento']['haber']):?>
					<td style="color: red;border-right: 1px solid black;border-bottom: 1px solid black;font-size: small;">ERROR ASIENTO</td>
				<?php else:?>
					<td style="border-right: 1px solid black;border-bottom: 1px solid black;font-size: small;"></td>
				<?php endif;?>
				<td style="border-bottom: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;border-bottom: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;border-bottom: 1px solid black;font-size: small;"></td>
				<td align="right" style="border: 1px solid black;font-size: small;"><?php echo number_format($asiento['Asiento']['debe'],2)?></td>
				<td align="right" style="border: 1px solid black;font-size: small;"><?php echo number_format($asiento['Asiento']['haber'],2)?></td>
			</tr>	
		<?php endforeach; 
	endwhile;?>
</table>
