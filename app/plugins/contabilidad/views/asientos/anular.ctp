<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'ASIENTO :: ANULAR'))?>
<div class='row'>
	<table>
		<tr>
			<td>FECHA: <?php echo $asiento['fecha']?></td>
			<td>NRO.: <?php echo $asiento['nro_asiento']?></td>
		</tr>
		<tr>
			<td colspan=2>REFERENCIA: <?php echo $asiento['referencia']?></td>
		</tr>
		<tr>
			<td>TIPO DOCUMENTO: <?php echo $asiento['tipo_documento']?></td>
			<td>NRO. DOCUMENTO: <?php echo $asiento['nro_documento']?></td>
		</tr>
	</table>
</div>
<hr>
<table cellpadding="0" cellspacing="0">

	<tr>
		<th>CUENTA</th>
		<th>DESCRIPCION</th>
		<th>REFERENCIA</th>
		<th>DEBE</th>
		<th>HABER</th>
	</tr>
	<?php
	foreach ($renglon as $asiento_r):
	?>
		<tr>
			<td><?php echo $asiento_r['AsientoRenglon']['codigo_cuenta']?></td>
			<td><?php echo $asiento_r['AsientoRenglon']['descripcion_cuenta']?></td>
			<td><strong><?php echo $asiento_r['AsientoRenglon']['referencia']?></strong></td>
			<td align="right"><?php echo $asiento_r['AsientoRenglon']['debe']?></td>
			<td align="right"><?php echo $asiento_r['AsientoRenglon']['haber']?></td>
		</tr>
	<?php endforeach; ?>
	<tr class='totales'>
		<td colspan="3">TOTAL ASIENTO</td>
		<td align="right"><?php echo $asiento['debe']?></td>
		<td align="right"><?php echo $asiento['haber']?></td>
	</tr>
</table>
<h2>ASIENTO DE ANULACION</h2>
<?php echo $form->create(null,array('name'=>'formAsiento','id'=>'formAsiento','onsubmit' => "return validateForm();",'action' => 'anular/' . $asiento['id'] ));?>

<div class="areaDatoForm">
	 		<div class='row'>
				<?php echo $frm->input('Asiento.fecha',array('label' => 'Fecha Asiento:', 'dateFormat' => 'DMY','minYear'=>date("Y", strtotime($ejercicio['fecha_cierre'])), 'maxYear' => date("Y", strtotime($ejercicio['fecha_hasta']))))?>
	 		</div>
	 		<div class='row'>
				<?php echo $frm->input('Asiento.referencia',array('label'=>'REFERENCIA:', 'value' => $asientoAnular['referencia'], 'size'=>60, 'maxlenght'=>100)); ?>
	 		</div>	 		
	 		<div class='row'>
				<?php echo $frm->comboTipoDocumento($asiento['tipo_documento']); ?>
	 		</div>	 		
	 		<div class='row'>
				<?php echo $frm->input('Asiento.nro_documento',array('label'=>'NRO.DOCUMENTO:', 'value' => $asiento['nro_documento'],'size'=>12, 'maxlenght'=>12)); ?>
	 		</div>	 		
</div>
<h3>Renglones del Asiento</h3>

<div class="areaDatoForm">
<table cellpadding="0" cellspacing="0">

	<tr>
		<th>CUENTA</th>
		<th>DESCRIPCION</th>
		<th>REFERENCIA</th>
		<th>DEBE</th>
		<th>HABER</th>
	</tr>
	<?php
	foreach ($renglonAnular as $asiento_r):
	?>
		<tr>
			<td><?php echo $asiento_r['Asiento']['codigo_cuenta']?></td>
			<td><?php echo $asiento_r['Asiento']['descripcion_cuenta']?></td>
			<td><strong><?php echo $asiento_r['Asiento']['referencia_renglon']?></strong></td>
			<td align="right"><?php echo $asiento_r['Asiento']['debe']?></td>
			<td align="right"><?php echo $asiento_r['Asiento']['haber']?></td>
		</tr>
	<?php endforeach; ?>
	<tr class='totales'>
		<td colspan="3">TOTAL ASIENTO</td>
		<td align="right"><?php echo $asientoAnular['debe']?></td>
		<td align="right"><?php echo $asientoAnular['haber']?></td>
	</tr>
</table>
</div>
<?php echo $frm->hidden('Asiento.renglonesSerialize', array('value' => base64_encode(serialize($renglonAnular))))?>
<?php echo $frm->hidden('Asiento.co_ejercicio_id', array('value' => $ejercicio['id'])) ?>
<?php echo $frm->hidden('Asiento.co_asiento_id', array('value' => $asiento['id'])) ?>
<?php echo $frm->hidden('Asiento.fecha_control', array('value' => $ejercicio['fecha_control'])) ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GENERAR ASIENTO','URL' => '/contabilidad/asientos/index/'.$ejercicio['id']))?>