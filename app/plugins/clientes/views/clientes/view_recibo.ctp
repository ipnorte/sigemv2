<h1>RECIBO NRO.: <?php echo $aRecibo['Recibo']['letra'] . ' - ' . $aRecibo['Recibo']['sucursal'] . ' - ' . $aRecibo['Recibo']['nro_recibo'] ?></h1>

FECHA: <strong><?php echo $util->armaFecha($aRecibo['Recibo']['fecha_comprobante'])?></strong>
<br/>
RAZON SOCIAL: <strong><?php echo $aRecibo['Recibo']['razon_social']?></strong>
<br/>
<?php echo $aRecibo['Recibo']['cuit'] ?>
<br />
IMPORTE: <strong><?php echo $aRecibo['Recibo']['importe'] ?>
<hr/>

<h2>D E T A L L E</h2>
<div class="areaDatoForm">
<table>

	<tr border="0">
		<th>CONCEPTO</th>
		<th>IMPORTE</th>
	</tr>
	<?php
	$i = 0;
	foreach ($aRecibo['Recibo']['detalle'] as $renglon):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
		<tr<?php echo $class;?> >
			<td><strong><?php echo $renglon['concepto']?></strong></td>
			<td align="right"><?php echo $renglon['importe']?></td>
		</tr>
	<?php endforeach; ?>	
</table>
</div>
<hr/>
<h2>V A L O R E S</h2>
<div class="areaDatoForm">
<table>

	<tr border="0">
		<th>CONCEPTO</th>
		<th>NRO. OPERACION</th>
		<th>IMPORTE</th>
	</tr>
	<?php
	$i = 0;
	foreach ($aRecibo['Recibo']['forma'] as $renglon):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
		<tr<?php echo $class;?> >
			<td><strong><?php echo $renglon['concepto'] . ' ' . $renglon['descripcion_cobro']?></strong></td>
			<td><?php echo $renglon['numero_operacion'] ?></td>
			<td align="right"><?php echo $renglon['importe']?></td>
		</tr>
	<?php endforeach; ?>	
</table>
</div>
<?php
	echo $controles->btnImprimirPDF('IMPRIMIR RECIBO','/Clientes/recibos/imprimir_recibo_pdf/'.$aRecibo['Recibo']['id'],'blank');
?>
