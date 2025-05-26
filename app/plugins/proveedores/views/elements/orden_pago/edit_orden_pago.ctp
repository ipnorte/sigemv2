<h1>ORDEN PAGO NRO.: <?php echo $aOrdenPago['OrdenPago']['sucursal'] . ' - ' . str_pad($aOrdenPago['OrdenPago']['nro_orden_pago'],8,0,STR_PAD_LEFT)?></h1>

FECHA: <strong><?php echo $util->armaFecha($aOrdenPago['OrdenPago']['fecha_pago'])?></strong>
<br/>
RAZON SOCIAL: <strong><?php echo $aOrdenPago['Proveedor']['razon_social']?></strong>
<br/>
C.U.I.T./DNI: <strong><?php echo $aOrdenPago['Proveedor']['formato_cuit'] ?></strong>
<br />
IMPORTE: <strong><?php echo $aOrdenPago['OrdenPago']['importe'] ?>
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
	foreach ($aOrdenPago['detalle'] as $renglon):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
		<tr<?php echo $class;?> >
			<td><strong><?php echo $renglon['tipo_comprobante_desc']?></strong></td>
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
	foreach ($aOrdenPago['forma'] as $renglon):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
		<tr<?php echo $class;?> >
			<td><strong><?php echo $renglon['concepto'] ?></strong></td>
			<td><?php echo $renglon['forma_pago_desc'] ?></td>
			<td align="right"><?php echo $renglon['importe']?></td>
		</tr>
	<?php endforeach; ?>	
</table>
</div>

<?php
	echo $controles->btnImprimir('IMPRIMIR ORDEN PAGO','/proveedores/orden_pagos/imprimir_orden_pago_pdf/'.$aOrdenPago['OrdenPago']['id'],'blank');
?>
<div class="areaDatoForm">
<?php echo $frm->create(null,array('name'=>'formOrdenPago','id'=>'formOrdenPago', 'action' => $aOrdenPago['OrdenPago']['action'] ));?>
<?php 
	echo $frm->hidden('OrdenPago.id', array('value' => $aOrdenPago['OrdenPago']['id']));
	echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'ANULAR O.PAGO','CONFIRM' => 'ANULAR ORDEN DE PAGO Nro ' . $aOrdenPago['OrdenPago']['sucursal'] . ' - ' . str_pad($aOrdenPago['OrdenPago']['nro_orden_pago'],8,0,STR_PAD_LEFT) . '?', 'TXT_CANCELAR' => 'REGRESAR', 'URL' => $aOrdenPago['OrdenPago']['url']))
?>
