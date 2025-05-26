<?php echo $this->renderElement('proveedor/proveedor_header',array('proveedor' => $proveedores))?>

<h1>ORDEN DE PAGO NRO.: <?php echo str_pad($aOrdenDePago['OrdenPago']['nro_orden_pago'],8,0,STR_PAD_LEFT) ?></h1>

FECHA: <strong><?php echo $util->armaFecha($aOrdenDePago['OrdenPago']['fecha_pago'])?></strong>
<br/>
RAZON SOCIAL: <strong><?php echo $aOrdenDePago['Proveedor']['razon_social']?></strong>
<br/>
<?php echo $aOrdenDePago['Proveedor']['cuit'] ?>
<br />
IMPORTE: <strong><?php echo $aOrdenDePago['OrdenPago']['importe'] ?>
<hr/>
<h2>L I Q U I D A C I O N</h2>
<div class="areaDatoForm">
<table>

	<tr border="0">
		<th>CONCEPTO</th>
		<th>IMPORTE</th>
	</tr>
	<?php
	$i = 0;
	foreach($aOrdenDePago['detalle'] as $liquidacion):
			$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
		<tr<?php echo $class;?> >
			<td><strong><?php echo $liquidacion['tipo_comprobante_desc']?></strong></td>
			<td align="right"><?php echo $liquidacion['importe']?></td>
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
		<th>IMPORTE</th>
	</tr>
	<?php
	$i = 0;
	foreach($aOrdenDePago['forma'] as $forma):
			$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
		<tr<?php echo $class;?> >
			<td><strong><?php echo $forma['concepto'] . ' ' . $forma['forma_pago_desc']?></strong></td>
			<td align="right"><?php echo $forma['importe']?></td>
		</tr>
	<?php endforeach; ?>	
</table>
</div>

<div class="areaDatoForm">
<?php echo $frm->create(null,array('name'=>'formRecibo','id'=>'formRecibo', 'action' => $aRecibo['Recibo']['action'] ));
//	echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'CONFIRMAR','URL' => $aRecibo['Recibo']['url']))
?>
<div class="submit">
	<input type="button" value="REGRESAR" id="btn_cancel" onclick="javascript:window.location='<?php echo $this->base?>/Proveedores/movimientos/cta_cte/<?php echo $aOrdenDePago['OrdenPago']['proveedor_id'] ?>';"/>
</div>
<?php echo $frm->end(); ?>