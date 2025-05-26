<h1>RECAUDACION POR CAJA</h1>
<hr>
	<?php echo $frm->create(null,array('action' => "add/$orden_cobro_xcaja_id"))?>
	<?php echo $this->requestAction('/mutual/orden_caja_cobros/view/'.$orden_cobro_xcaja_id)?>
<?php if($procesada==0):?>	
<div class="areaDatoForm">
	<h3>DATOS DEL COBRO</h3>
	<table class="tbl_form">
		<tr>
			<td>TIPO COBRO</td>
			<td>
			<?php echo $this->renderElement('global_datos/combo_global',array(
																			'plugin'=>'config',
																			'label' => " ",
																			'model' => 'OrdenDescuentoCobro.tipo_cobro',
																			'prefijo' => 'MUTUTCOB',
																			'disabled' => false,
																			'empty' => false,
																			'metodo' => "get_tipos_cobro_caja",
																			'selected' => "MUTUTCOBCAJA"	
			))?>				
			</td>
		</tr>	
		<tr>
			<td>FECHA DE IMPUTACION</td>
			<td><?php echo $frm->input('OrdenDescuentoCobro.fecha',array('dateFormat' => 'DMY','minYear'=>date("Y") - 1, 'maxYear' => date("Y") + 1))?></td>
		</tr>
		<tr>
			<td>NRO. RECIBO TESORERIA</td>
			<td><?php echo $frm->input('nro_recibo',array('label' => '', 'size' => 25))?></td>
		</tr>
	</table>
	<?php echo $frm->hidden('orden_caja_cobro_id',array('value' => $orden_cobro_xcaja_id))?>
	<?php echo $frm->hidden('socio_id',array('value' => $ocaja['OrdenCajaCobro']['socio_id']))?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'COBRAR','URL' => ( empty($fwrd) ? "/mutual/orden_caja_cobros/": $fwrd) ))?>
</div>
<?php else:?>
	<div class="notices_error">
		LA ORDEN DE COBRO POR CAJA #<?php echo $orden_cobro_xcaja_id?> ESTA PROCESADA
	</div>
<?php endif;?>