<table cellpadding="0" cellspacing="0">

	<tr>
		<th>FECHA COMP.</th>
		<th>NUMERO</th>
		<th>CLIENTE/SOCIO</th>
		<th>CUIT/T.DOC.NUMERO</th>
		<th>I.V.A.</th>
		<th align="right">IMPORTE</th>
		<th>COMENTARIO</th>
		<th></th>
	</tr>
	<?php
	$i = 0;
	foreach ($aReciboInforme as $aRecibo):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?> >
		<td><?php echo $util->armaFecha($aRecibo['Recibo']['fecha_comprobante']) ?></td>
		<td><?php echo $aRecibo['Recibo']['letra'] . '-' . $aRecibo['Recibo']['sucursal'] . '-' . $aRecibo['Recibo']['nro_recibo']?></td>
		<td><?php echo $aRecibo['Recibo']['razon_social'] ?></td>
		<td><?php echo $aRecibo['Recibo']['cuit'] ?></td>
		<td><?php echo $aRecibo['Recibo']['iva_concepto'] ?></td>
		<td align="right"><?php echo number_format($aRecibo['Recibo']['importe'],2,',','.') ?></td>
		<td><?php echo $aRecibo['Recibo']['comentarios'] ?></td>
		<?php if($aRecibo['Recibo']['anulado'] == 0): ?>
			<td align="center"><?php echo $controles->botonGenerico('imprimir_recibo_pdf/'. $aRecibo['Recibo']['id'],'controles/printer.png',null, array('target' => 'blank'));?></td>	
		<?php else: ?>
			<td></td>
		<?php endif; ?>		
	</tr>
	<?php endforeach; ?>	
</table>

