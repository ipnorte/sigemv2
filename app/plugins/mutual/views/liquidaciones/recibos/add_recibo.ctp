<?php echo $this->renderElement('head',array('title' => 'INGRESO DE RECIBO DE COBRANZA'))?>
<div class="areaDatoForm">
<h2>Recibo de Ingreso</h2>
LIQUIDACION NRO.: <strong><?php echo $LqdInterCambio['LiquidacionIntercambio']['liquidacion_id']?></strong>
&nbsp;
ID.ARCHIVO: <strong><?php echo $LqdInterCambio['LiquidacionIntercambio']['id']?></strong>
<br/>
ORGANISMO: <strong><?php echo $util->globalDato($LqdInterCambio['LiquidacionIntercambio']['codigo_organismo'])?></strong>
&nbsp;
PERIODO: <strong><?php echo $util->periodo($LqdInterCambio['LiquidacionIntercambio']['periodo'],true)?></strong>
<br/>
TOTAL REGISTROS: <strong><?php echo $LqdInterCambio['LiquidacionIntercambio']['total_registros'] ?></strong>
&nbsp;
REG.COBRADOS: <strong><?php echo $LqdInterCambio['LiquidacionIntercambio']['registros_cobrados'] ?></strong>
&nbsp;
IMPORTE COBRADO: <strong><?php echo $LqdInterCambio['LiquidacionIntercambio']['importe_cobrado'] ?></strong>
<br/>
ENTIDAD RECAUDADORA: <strong><?php echo $util->banco($LqdInterCambio['LiquidacionIntercambio']['banco_id'])?></strong>

<script language="Javascript" type="text/javascript">
	Event.observe(window, 'load', function() {
		
		$('btn_submit').disable();
		document.getElementById("ReciboImporte").value = <?php echo $LqdInterCambio['LiquidacionIntercambio']['importe_cobrado'] ?>;
		ocultarOptionFCobro();
	
	});
</script>


<div class="areaDatoForm">
<?php echo $frm->create(null,array('name'=>'formReciboCobro','id'=>'formReciboCobro','onsubmit' => "return ctrlCobro()", 'action' => "addRecibo/" . $LqdInterCambio['LiquidacionIntercambio']['id'] . '/' . $LqdInterCambio['LiquidacionIntercambio']['liquidacion_id'] ));?>

	<table class="tbl_form">
		<tr>
			<td>FECHA INGRESO:</td>
			<td><?php echo $frm->calendar('Recibo.fecha_comprobante',null,null,date('Y')-1,date('Y')+1)?></td>
		</tr>
		<tr>
			<td>CONCEPTO:</td>
			<td><?php echo $frm->input('Recibo.observacion', array('label'=>'','size'=>60,'maxlength'=>50, 'value' => $util->periodo($LqdInterCambio['LiquidacionIntercambio']['periodo'],true) . ' - ' . $util->globalDato($LqdInterCambio['LiquidacionIntercambio']['codigo_organismo']))) ?></td>
		</tr>
		
		<?php echo $this->renderElement('recibos/forma_cobro',array(
										'plugin'=>'clientes'))?>
	</table>
	<?php echo $frm->hidden('Recibo.tipo_documento', array('value' => 'REC')); ?>
	<?php echo $frm->hidden('Recibo.concepto', array('value' => $util->periodo($LqdInterCambio['LiquidacionIntercambio']['periodo'],true) . ' - ' . $util->globalDato($LqdInterCambio['LiquidacionIntercambio']['codigo_organismo']))); ?>
	<?php echo $frm->hidden('Recibo.liquidacion_intercambio_id', array('value' => $LqdInterCambio['LiquidacionIntercambio']['id'])); ?>
	<?php echo $frm->hidden('Recibo.cabecera_banco_id', array('value' => $LqdInterCambio['LiquidacionIntercambio']['banco_id'])); ?>
	<?php echo $frm->hidden('Recibo.destinatario', array('value' => $util->globalDato($LqdInterCambio['LiquidacionIntercambio']['codigo_organismo']))); ?>
	<?php echo $frm->hidden('Recibo.forma_cobro_desc') ?>
	<?php echo $frm->hidden('Recibo.importe_cobro', array('value' => $LqdInterCambio['LiquidacionIntercambio']['importe_cobrado'])) ?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR E IMPRIMIR','URL' => '/mutual/liquidaciones/importar/' . $LqdInterCambio['LiquidacionIntercambio']['liquidacion_id']))?>

	<?php // echo $frm->hidden('Recibo.liquidacion_id', array('value' => $LqdInterCambio['LiquidacionIntercambio']['liquidacion_id'])); ?>
	<?php // echo $frm->hidden('Recibo.codigo_organismo', array('value' => $LqdInterCambio['LiquidacionIntercambio']['codigo_organismo'])); ?>
	<?php // echo $frm->hidden('Recibo.forma_cobro', array('value' => 'DB')); ?>
</div>
<div style="clear: both;"></div>

