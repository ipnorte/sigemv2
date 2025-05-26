<?php 
	$cModulo = '';
	if($asientos[0]['MutualAsiento']['modulo'] == 'RECIPERS') $cModulo = 'RECIBO A PERSONAS';
	if($asientos[0]['MutualAsiento']['modulo'] == 'RECISOCI') $cModulo = 'RECIBO A SOCIOS';
	if($asientos[0]['MutualAsiento']['modulo'] == 'RECICLIE') $cModulo = 'RECIBO A CLIENTES';
	if($asientos[0]['MutualAsiento']['modulo'] == 'RECIORGA') $cModulo = 'RECIBO A ORGANISMO';
	if($asientos[0]['MutualAsiento']['modulo'] == 'RECIADELCRED') $cModulo = 'RECIBO POR ADELANTO CREDITO';
	if($asientos[0]['MutualAsiento']['modulo'] == 'PAGOPROV') $cModulo = 'PAGO A PROVEEDORES';
	if($asientos[0]['MutualAsiento']['modulo'] == 'PAGOPROD') $cModulo = 'PAGO CONSUMO MUTUAL';
	if($asientos[0]['MutualAsiento']['modulo'] == 'PAGOREIN') $cModulo = 'PAGO REINTREGO A SOCIOS';
	if($asientos[0]['MutualAsiento']['modulo'] == 'PAGOADELCRED') $cModulo = 'PAGO ADELANTO CREDITO';
	if($asientos[0]['MutualAsiento']['modulo'] == 'LIQUCOBR') $cModulo = 'COBRO POR ORGANISMO';
	if($asientos[0]['MutualAsiento']['modulo'] == 'LIQUREVE') $cModulo = 'REVERSO DE SOCIOS';
	if($asientos[0]['MutualAsiento']['modulo'] == 'REVEREIN') $cModulo = 'REVERSO DE REINTEGROS DE SOCIOS';
	if($asientos[0]['MutualAsiento']['modulo'] == 'REVEBANC') $cModulo = 'REVERSO POR BANCO';
	if($asientos[0]['MutualAsiento']['modulo'] == 'COBRCAJA') $cModulo = 'COBRO POR CAJA';
	if($asientos[0]['MutualAsiento']['modulo'] == 'CANCRECI') $cModulo = 'COBRO DE CANCELACIONES';
	if($asientos[0]['MutualAsiento']['modulo'] == 'CANCSOCI') $cModulo = 'CANCELACIONES DE SOCIOS';
	if($asientos[0]['MutualAsiento']['modulo'] == 'SOLISOCI') $cModulo = 'CONSUMO MUTUAL';
	if($asientos[0]['MutualAsiento']['modulo'] == 'CUOTSOCI') $cModulo = 'CUOTA SOCIAL DE LA MUTUAL';
	if($asientos[0]['MutualAsiento']['modulo'] == 'REINSOCI') $cModulo = 'REINTEGRO A SOCIOS';
	if($asientos[0]['MutualAsiento']['modulo'] == 'PROVFACT') $cModulo = 'FACTURA DE PROVEEDORES';
	if($asientos[0]['MutualAsiento']['modulo'] == 'CLIEFACT') $cModulo = 'FACTURA A COMERCIOS';
	if($asientos[0]['MutualAsiento']['modulo'] == 'CABAINDI') $cModulo = 'MOVIMIENTO DE CAJA Y BANCO';
	if($asientos[0]['MutualAsiento']['modulo'] == 'CABARELA') $cModulo = 'MOVIMIENTO DE CAJA Y BANCO';
	if($asientos[0]['MutualAsiento']['modulo'] == 'CABAREEM') $cModulo = 'REEMPLAZO DE CHEQUES';
	if($asientos[0]['MutualAsiento']['modulo'] == '') $cModulo = '';
	
?>
<h1><?php echo $cModulo?></h1>
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
			<td colspan="2" align="center" style="border-left: 1px solid black;font-size: small;"><?php echo str_pad('  Nro.Int. ' . $asiento['MutualAsiento']['id'] . '  ', 50, '-', STR_PAD_BOTH)?></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
			<td style="border-left: 1px solid black;font-size: small;"></td>
		</tr>
		
		<?php
			$fechaPrimera = true; 
			$haberPrimera = true;
			foreach($asiento['MutualAsiento']['renglon'] as $renglon):?>
				<tr>
					<?php if($fechaPrimera):
						$fechaPrimera = false;
					?>
						<td align="center" style="border-left: 1px solid black;font-size: small;"><?php echo date('d/m/Y',strtotime($asiento['MutualAsiento']['fecha']))?></td>
					<?php else:?>
						<td style="border-left: 1px solid black;font-size: small;"></td>
					<?php endif;?>
					
					<?php if($renglon['importe'] > 0):?>
						<td colspan="2" style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['descripcion']?></td>
						<td style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['cuenta']?></td>
						<td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo number_format($renglon['importe'],2)?></td>
						<td align="right" style="border-left: 1px solid black;font-size: small;"></td>
						<td style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['referencia']?></td>
					<?php else:?>
						<?php if($haberPrimera):?>
							<?php $haberPrimera = false;?>
							<td align="right" style="border-left: 1px solid black;font-size: small;" size="50%">a)</td>
						<?php else:?>
							<td align="right" style="border-left: 1px solid black;font-size: small;" size="50%"></td>
						<?php endif;?>	
						<td style=";font-size: small;"><?php echo $renglon['descripcion']?></td>
						<td style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['cuenta']?></td>
						<td align="right" style="border-left: 1px solid black;font-size: small;"></td>
						<td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo number_format($renglon['importe'] * (-1),2)?></td>
						<td style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['referencia']?></td>
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
