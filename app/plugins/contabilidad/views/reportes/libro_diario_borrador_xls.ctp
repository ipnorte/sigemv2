<?php 
$contenido = "Content-Disposition: attachment; filename=Mayor(".$cuentaMayor[0]['MutualAsientoRenglon']['descripcion'].").xls";
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Libro-Diario-Borrador.xls");
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");
App::import('Model', 'contabilidad.MutualAsientoRenglon');

$oAsientoRenglon = new MutualAsientoRenglon();

$sql = "SELECT MutualAsiento.*
		FROM mutual_asientos MutualAsiento
		WHERE MutualAsiento.mutual_proceso_asiento_id = $procesoId
		ORDER BY MutualAsiento.fecha, MutualAsiento.id
		LIMIT 1
";

$asientos = $oAsientoRenglon->query($sql);

?>

<h1>LIBRO DIARIO BORRADOR</h1>
<h1>FECHA DESDE :: <?php echo $util->armaFecha($asientos[0]['MutualAsiento']['fecha'])?></h1>
<h1>FECHA HASTA :: <?php echo $util->armaFecha($fecha_hasta)?></h1>

<table align="center" width="100%">

	<col width="100" />
	<col width="250" />
	<col width="250" />
	<col width="100" />
	<col width="100" />
	<col width="100" />
		
	<tr border="0">
		<th style="border: 1px solid black;font-size: small;">FECHA</th>
		<th colspan="2" style="border: 1px solid black;font-size: small;">DESCRIPCION</th>
		<th style="border: 1px solid black;font-size: small;">REFERENCIA</th>
		<th style="border: 1px solid black;font-size: small;">DEBE</th>
		<th style="border: 1px solid black;font-size: small;">HABER</th>
		<th style="border: 1px solid black;font-size: small;">COMENTARIO</th>
	</tr>

	<?php
	$LIMIT = 500;
    $OFFSET = 0;
    $INCREMET = 500;
	
	while(true):
		$sql = "SELECT MutualAsiento.*
				FROM mutual_asientos MutualAsiento
				WHERE MutualAsiento.mutual_proceso_asiento_id = $procesoId
				ORDER BY MutualAsiento.fecha, MutualAsiento.id
				LIMIT $OFFSET, $LIMIT
		";

		$asientos = $oAsientoRenglon->query($sql);
		if(empty($asientos)) break;
		
		$OFFSET += $INCREMET;
		
	foreach ($asientos as $asiento):?>
		<tr>
			<td style="border-right: 1px solid black;font-size: small;"></td>
			<td colspan="2" align="center" style="border-right: 1px solid black;font-size: small;"><?php echo str_pad('  Nro.Int. ' . $asiento['MutualAsiento']['id'] . '  ', 90, '-', STR_PAD_BOTH)?></td>
			<td style="border-right: 1px solid black;font-size: small;"></td>
			<td style="border-right: 1px solid black;font-size: small;"></td>
			<td style="border-right: 1px solid black;font-size: small;"></td>
			<td style="border-right: 1px solid black;font-size: small;"></td>
		</tr>
		
		<?php
			$fechaPrimera = true; 
			$haberPrimera = true;
			$nMutualAsientoId = $asiento['MutualAsiento']['id'];
			$sql = "SELECT	*
					FROM	mutual_asiento_renglones MutualAsientoRenglon
					WHERE	MutualAsientoRenglon.mutual_asiento_id = '$nMutualAsientoId'
			";
			
			$renglones = $oAsientoRenglon->query($sql);
			
			foreach($renglones as $renglon):?>
				<tr>
					<?php if($fechaPrimera):
						$fechaPrimera = false;
					?>
						<td align="center" style="border-right: 1px solid black;font-size: small;"><?php echo date('d/m/Y',strtotime($asiento['MutualAsiento']['fecha']))?></td>
					<?php else:?>
						<td style="border-right: 1px solid black;font-size: small;"></td>
					<?php endif;?>
					
					<?php if($renglon['MutualAsientoRenglon']['debe'] > 0):?>
						<td colspan="2" style="border-right: 1px solid black;font-size: small;"><?php echo $renglon['MutualAsientoRenglon']['descripcion']?></td>
						<td style="border-right: 1px solid black;font-size: small;"><?php echo $renglon['MutualAsientoRenglon']['cuenta']?></td>
						<td align="right" style="border-right: 1px solid black;font-size: small;"><?php echo number_format($renglon['MutualAsientoRenglon']['debe'],2)?></td>
						<td align="right" style="border-right: 1px solid black;font-size: small;"></td>
						<td style="border-right: 1px solid black;font-size: small;"><?php echo $renglon['MutualAsientoRenglon']['error_descripcion']?></td>
					<?php else:?>
						<?php if($haberPrimera):?>
							<?php $haberPrimera = false;?>
							<td align="right" style="font-size: small;" size="50%"></td>
						<?php else:?>
							<td align="right" style="font-size: small;" size="50%"></td>
						<?php endif;?>	
						<td style="border-right: 1px solid black;font-size: small;"><?php echo $renglon['MutualAsientoRenglon']['descripcion']?></td>
						<td style="border-right: 1px solid black;font-size: small;"><?php echo $renglon['MutualAsientoRenglon']['cuenta']?></td>
						<td align="right" style="border-right: 1px solid black;font-size: small;"></td>
						<td align="right" style="border-right: 1px solid black;font-size: small;"><?php echo number_format($renglon['MutualAsientoRenglon']['haber'],2)?></td>
						<td style="border-right: 1px solid black;font-size: small;"><?php echo $renglon['MutualAsientoRenglon']['error_descripcion']?></td>
					<?php endif;?>
				</tr>
		<?php endforeach; ?>
			<tr>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td colspan="2" style="border-right: 1px solid black;font-size: small;"><?php echo $asiento['MutualAsiento']['referencia']?></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
			</tr>	
			<tr>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td colspan="2" style="border-right: 1px solid black;font-size: small;"><?php echo ltrim(rtrim($asiento['MutualAsiento']['tipo_documento'] . ' ' . $asiento['MutualAsiento']['nro_documento']))?></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;font-size: small;"></td>
			</tr>	
			<tr>
				<?php if($asiento['MutualAsiento']['debe'] != $asiento['MutualAsiento']['haber']):?>
					<td style="color: red;border-right: 1px solid black;border-bottom: 1px solid black;font-size: small;">ERROR ASIENTO</td>
				<?php else:?>
					<td style="border-right: 1px solid black;border-bottom: 1px solid black;font-size: small;"></td>
				<?php endif;?>
				<td style="border-bottom: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;border-bottom: 1px solid black;font-size: small;"></td>
				<td style="border-right: 1px solid black;border-bottom: 1px solid black;font-size: small;"></td>
				<td align="right" style="border: 1px solid black;font-size: small;"><?php echo number_format($asiento['MutualAsiento']['debe'],2)?></td>
				<td align="right" style="border: 1px solid black;font-size: small;"><?php echo number_format($asiento['MutualAsiento']['haber'],2)?></td>
				<td style="border-right: 1px solid black;border-bottom: 1px solid black;font-size: small;"></td>
			</tr>	
	<?php endforeach; 
	endwhile;?>
</table>
