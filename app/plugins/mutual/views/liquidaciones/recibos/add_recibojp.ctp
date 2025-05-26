<?php echo $this->renderElement('head',array('title' => 'INGRESO DE RECIBO DE COBRANZA'))?>
<div class="areaDatoForm">
<h2>Recibo de Ingreso</h2>
LIQUIDACION NRO.: <strong><?php echo $Liquidacion['Liquidacion']['id']?></strong>
&nbsp;
<br/>
ORGANISMO: <strong><?php echo $util->globalDato($Liquidacion['Liquidacion']['codigo_organismo'])?></strong>
&nbsp;
PERIODO: <strong><?php echo $util->periodo($Liquidacion['Liquidacion']['periodo'],true)?></strong>
<br/>
REGISTROS ENVIADOS: <strong><?php echo $Liquidacion['Liquidacion']['registros_enviados'] ?></strong>
&nbsp;
REGISTROS RECIBIDOS: <strong><?php echo $Liquidacion['Liquidacion']['registros_recibidos'] ?></strong>
&nbsp;
IMPORTE COBRADO: <strong><?php echo $Liquidacion['Liquidacion']['importe_cobrado'] + $Liquidacion['Liquidacion']['importe_recibido']?></strong>
<br/>
ENTIDAD RECAUDADORA: <strong><?php echo $util->banco('99999')?></strong>

<script language="Javascript" type="text/javascript">
	Event.observe(window, 'load', function() {
		
		$('btn_submit').disable();
		document.getElementById("ReciboImporte").value = <?php echo $Liquidacion['Liquidacion']['importe_cobrado'] + $Liquidacion['Liquidacion']['importe_recibido'] ?>;
		ocultarOptionFCobro();
	
	});

</script>

	
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('name'=>'formReciboCobro','id'=>'formReciboCobro','onsubmit' => "return ctrlCobro()", 'action' => "addRecibojp/" . $Liquidacion['Liquidacion']['id'] ));?>

		<table class="tbl_form">
			<tr>
				<td>FECHA INGRESO:</td>
				<td><?php echo $frm->calendar('Recibo.fecha_comprobante',null,null,date('Y')-1,date('Y')+1)?></td>
			</tr>
			<tr>
				<td>CONCEPTO:</td>
				<td><?php echo $frm->input('Recibo.observacion', array('label'=>'','size'=>60,'maxlength'=>50, 'value' => $util->periodo($Liquidacion['Liquidacion']['periodo'],true) . ' - ' . $util->globalDato($Liquidacion['Liquidacion']['codigo_organismo']))) ?></td>
				<!-- <td><?php echo $frm->submit("SIGUIENTE")?></td>
				<td></td> -->
			</tr>

			<?php echo $this->renderElement('recibos/forma_cobro',array(
										'plugin'=>'clientes'))?>
		</table>
	<?php // echo $frm->hidden('Recibo.banco_id', array('value' => $Liquidacion['Liquidacion']['banco_id'])); ?>
	<?php echo $frm->hidden('Recibo.tipo_documento', array('value' => 'REC')); ?>
	<?php echo $frm->hidden('Recibo.concepto', array('value' => $util->periodo($Liquidacion['Liquidacion']['periodo'],true) . ' - ' . $util->globalDato($Liquidacion['Liquidacion']['codigo_organismo']))); ?>
	<?php echo $frm->hidden('Recibo.liquidacion_id', array('value' => $Liquidacion['Liquidacion']['id'])); ?>
	<?php echo $frm->hidden('Recibo.cabecera_codigo_organismo', array('value' => $Liquidacion['Liquidacion']['codigo_organismo'])); ?>
	<?php echo $frm->hidden('Recibo.destinatario', array('value' => $util->globalDato($Liquidacion['Liquidacion']['codigo_organismo']))); ?>
	<?php echo $frm->hidden('Recibo.forma_cobro_desc') ?>
	<?php echo $frm->hidden('Recibo.importe_cobro', array('value' => $Liquidacion['Liquidacion']['importe_cobrado'] + $Liquidacion['Liquidacion']['importe_recibido'])) ?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR E IMPRIMIR','URL' => '/mutual/liquidaciones/importar/' . $Liquidacion['Liquidacion']['id']))?>
</div>
<div style="clear: both;"></div>
