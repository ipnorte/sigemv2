<?php 
App::import('Model', 'contabilidad.MutualAsientoRenglon');

$oAsientoRenglon = new MutualAsientoRenglon();

$sql = "SELECT	MutualAsiento.*
		FROM	mutual_asientos MutualAsiento
		WHERE	MutualAsiento.mutual_proceso_asiento_id = '$procesoId'
		ORDER BY MutualAsiento.fecha
		LIMIT 1
";
			
$fecha_desde = $oAsientoRenglon->query($sql);
			
?>

<h1>LIBRO DIARIO BORRADOR</h1>
<h1>FECHA DESDE :: <?php echo $util->armaFecha($fecha_desde[0]['MutualAsiento']['fecha'])?></h1>
<h1>FEHCA HASTA :: <?php echo $util->armaFecha($aMutualProcesoAsiento['MutualProcesoAsiento']['fecha_hasta']) ?> </h1>
<hr>


<?php echo $this->renderElement('paginado');?>


<div class="areaDatoForm">
	<?php 
	echo $controles->botonGenerico('/contabilidad/reportes/libro_diario_borrador_xls/' . $procesoId,'controles/ms_excel.png', null, array('target' => '_blank'));
	echo $controles->botonGenerico('/contabilidad/reportes/libro_diario_borrador_pdf/' . $procesoId,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
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
		<th style="font-size: small;">COMENTARIO</th>
	</tr>

	<?php
	foreach ($asientos as $asiento):?>
		<tr>
			<td style="border-top: 1px solid black;border-left: 1px solid black;font-size: small;"></td>
<!--			<td colspan="2" align="center" style="border-left: 1px solid black;font-size: small;"><?php // echo str_pad('  Nro.Int. ' . $asiento['MutualAsiento']['id'] . '  ', 100, '-', STR_PAD_BOTH)?></td>-->
			<td colspan="2" align="center" style="border-left: 1px solid black;font-size: small;"><?php echo $controles->linkModalBox(str_pad('  Nro.Int. ' . $asiento['MutualAsiento']['id'] . '  ', 100, '-', STR_PAD_BOTH),array('title' => 'ASIENTO #' . $asiento['MutualAsiento']['id'],'url' => '/contabilidad/mutual_proceso_asientos/view/'.$asiento['MutualAsiento']['id'],'h' => 450, 'w' => 950))?></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
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
			
//			foreach($asiento['MutualAsiento']['renglon'] as $renglon):
			foreach($renglones as $renglon):?>
				<tr>
					<?php if($fechaPrimera):
						$fechaPrimera = false;
					?>
						<td align="center" style="border-left: 1px solid black;font-size: small;"><?php echo date('d/m/Y',strtotime($asiento['MutualAsiento']['fecha']))?></td>
					<?php else:?>
						<td style="border-left: 1px solid black;font-size: small;"></td>
					<?php endif;?>
					
					<?php if($renglon['MutualAsientoRenglon']['debe'] > 0):?>
						<td colspan="2" style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['MutualAsientoRenglon']['descripcion']?></td>
						<td style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['MutualAsientoRenglon']['cuenta']?></td>
						<td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo number_format($renglon['MutualAsientoRenglon']['debe'],2)?></td>
						<td align="right" style="border-left: 1px solid black;font-size: small;"></td>
						<td style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['MutualAsientoRenglon']['error_descripcion']?></td>
					<?php else:?>
						<?php if($haberPrimera):?>
							<?php $haberPrimera = false;?>
							<td align="right" style="border-left: 1px solid black;font-size: small;" size="50%"></td>
						<?php else:?>
							<td align="right" style="border-left: 1px solid black;font-size: small;" size="50%"></td>
						<?php endif;?>	
						<td style=";font-size: small;"><?php echo $renglon['MutualAsientoRenglon']['descripcion']?></td>
						<td style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['MutualAsientoRenglon']['cuenta']?></td>
						<td align="right" style="border-left: 1px solid black;font-size: small;"></td>
						<td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo number_format($renglon['MutualAsientoRenglon']['haber'],2)?></td>
						<td style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['MutualAsientoRenglon']['error_descripcion']?></td>
					<?php endif;?>
				</tr>
		<?php endforeach; ?>
			<tr>
				<td style="border-left: 1px solid black;font-size: small;"></td>
				<td style="border-left: 1px solid black;font-size: small;"></td>
				<td style="font-size: small;"><?php echo $asiento['MutualAsiento']['referencia']?></td>
				<td style="border-left: 1px solid black;font-size: small;"></td>
				<td style="border-left: 1px solid black;font-size: small;"></td>
				<td style="border-left: 1px solid black;font-size: small;"></td>
				<td style="border-left: 1px solid black;font-size: small;"></td>
			</tr>	
			<tr>
				<td style="border-left: 1px solid black;font-size: small;"></td>
				<td style="border-left: 1px solid black;font-size: small;"></td>
				<td style="font-size: small;"><?php echo $asiento['MutualAsiento']['tipo_documento'] . ' ' . $asiento['MutualAsiento']['nro_documento']?></td>
				<td style="border-left: 1px solid black;font-size: small;"></td>
				<td style="border-left: 1px solid black;font-size: small;"></td>
				<td style="border-left: 1px solid black;font-size: small;"></td>
				<td style="border-left: 1px solid black;font-size: small;"></td>
			</tr>	
			<tr>
				<?php if($asiento['MutualAsiento']['debe'] != $asiento['MutualAsiento']['haber']):?>
					<td style="color: red;border-left: 1px solid black;font-size: small;">ERROR ASIENTO</td>
				<?php else:?>
					<td style="border-left: 1px solid black;font-size: small;"></td>
				<?php endif;?>
				<td style="border-left: 1px solid black;border-bottom: 1px solid black;font-size: small;"></td>
				<td style="border-bottom: 1px solid black;font-size: small;"></td>
				<td style="border-left: 1px solid black;border-bottom: 1px solid black;font-size: small;"></td>
				<td align="right" style="border: 1px solid black;font-size: small;"><?php echo number_format($asiento['MutualAsiento']['debe'],2)?></td>
				<td align="right" style="border: 1px solid black;font-size: small;"><?php echo number_format($asiento['MutualAsiento']['haber'],2)?></td>
				<td style="border-left: 1px solid black;border-bottom: 1px solid black;font-size: small;"></td>
			</tr>	
	<?php endforeach; ?>
</table>

	<?php 
	echo $controles->botonGenerico('/contabilidad/reportes/libro_diario_borrador_xls/' . $procesoId,'controles/ms_excel.png', null, array('target' => '_blank'));
	echo $controles->botonGenerico('/contabilidad/reportes/libro_diario_borrador_pdf/' . $procesoId,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
	?>

</div>

<?php echo $this->renderElement('paginado');?>


