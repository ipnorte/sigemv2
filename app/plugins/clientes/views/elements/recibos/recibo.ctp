<script language="Javascript" type="text/javascript">
	Event.observe(window, 'load', function() {
		
//		$('btn_submit').disable();
	
	});


	function habilitarBoton(){
		$('btn_submit').enable();
	}

</script>

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
<div class="areaDatoForm">
<?php echo $frm->create(null,array('name'=>'formRecibo','id'=>'formRecibo', 'action' => $aRecibo['Recibo']['action'] ));?>
<?php 
	echo $frm->hidden('Recibo.id', array('value' => $aRecibo['Recibo']['id']));
	if(isset($aRecibo['Recibo']['liquidacion_intercambio_id']) && $aRecibo['Recibo']['liquidacion_intercambio_id'] > 0):
		echo $frm->hidden('Recibo.liquidacion_intercambio_id', array('value' => $aRecibo['Recibo']['liquidacion_intercambio_id']));
	endif; 
	if(isset($aRecibo['Recibo']['liquidacion_id']) && $aRecibo['Recibo']['liquidacion_id'] > 0):
		echo $frm->hidden('Recibo.liquidacion_id', array('value' => $aRecibo['Recibo']['liquidacion_id']));
	endif; 
	echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'ANULAR RECIBO', 'TXT_CANCELAR' => 'REGRESAR', 'URL' => $aRecibo['Recibo']['url']))
//	echo $frm->btnForm(array('URL'=>$aRecibo['Recibo']['url'],'LABEL' => 'REGRESAR'));
//	echo $frm->btnForm(array('URL'=>$aRecibo['Recibo']['url'],'LABEL' => 'ANULAR'));
?>	
<!--<div class="submit">	<input type="button" name="btn_697" value="IMPRIMIR" onclick="javascript:window.location='<?php // echo $this->base ?>/Clientes/recibos/imprimir_recibo_pdf/404';" target="_blank"/></div>-->
<!--//	<table>
//		<tr>
//			<td>ACCION A REALIZAR</td>
//			<td></td>
//		</tr>
//		<tr>
//			<td></td>
//			<td><input type="radio" name="data[Recibo][accion]" id="ReciboAccion_a" value="1" onchange= "habilitarBoton()"/>ANULAR RECIBO</td>
//		</tr>
//		<tr>
//			<td></td>
//			<td><input type="radio" name="data[Recibo][accion]" id="ReciboAccion_b" value="2" onchange= "habilitarBoton()"/>IMPRIMIR RECIBO</td>
//		</tr>
//	</table>-->

