<?php echo $this->renderElement('head',array('title' => 'MODIFICAR DATOS LIQUIDACION DE UNA SOLICITUD','plugin' => 'config'))?>

<div id="FormSearch">
	<?php echo $form->create(null,array('action'=> 'editar_fpago'));?>
	<table>
		<tr>
			<td colspan="2"><strong>BUSCAR SOLICITUD DE CREDITO</strong></td>
		</tr>
		<tr>
			<td><?php echo $frm->number('Solicitud.nro_solicitud_aprox',array('label'=>'NRO DE SOLICITUD','size'=>11,'maxlength'=>10,'value'=>$solicitud_nro)); ?></td>
			<td><?php echo $frm->submit('APROXIMAR');?></td>
		</tr>
	</table>
	<?php echo $form->end();?> 
</div>

<div style="clear: both;"></div>
<?php if(!empty($solicitudes)):?>
	<?php echo $this->renderElement('solicitudes/grilla_liquidacion', array('solicitud' => $solicitudes[0], 'plugin' => 'v1'))?>
		<?php echo $form->create(null,array('onsubmit' => "",'action' => 'editar_fpago'));?>
		<div class="areaDatoForm">
			<table class="tbl_form">
				<tr>
					<td>NRO. CREDITO PROVEEDOR</td>
					<td><?php echo $frm->input('Solicitud.nro_credito_proveedor',array('size'=>50,'maxlenght'=>50)); ?></td>
				</tr>
				<tr>
					<td>FECHA PAGO</td>
					<td><?php echo $frm->input('Solicitud.fecha_operacion_pago',array('dateFormat' => 'DMY','minYear'=>date("Y") - 1, 'maxYear' => date("Y") + 1))?></td>
				</tr>
				<?php if($solicitudes[0]['Solicitud']['codigo_fpago'] != '0001'):?>
					<?php if($solicitudes[0]['Solicitud']['codigo_fpago'] == '0002'):?>
						<tr id="datoBanco">
							<td>BANCO</td>
							<td colspan="2">
							<?php echo $this->renderElement('banco/combo',array(
																				'plugin'=>'config',
																				'label' => " ",
																				'model' => 'Solicitud.banco_id',
																				'disable' => false,
																				'empty' => false,
																				'tipo' => 4
							))?>	
							</td>
						</tr>
					<?php endif;?>
					<tr id="datoBancoNroOpenBan">
						<td>NRO.COMPROBANTE</td>
						<td colspan="2"><?php echo $frm->input('Solicitud.nro_operacion_pago',array('size'=>50,'maxlenght'=>50)); ?></td>
					</tr>
				<?php endif;?>								
			</table>
		</div>
		<?php echo $frm->hidden('Solicitud.nro_solicitud',array('value'=>$solicitudes[0]['Solicitud']['nro_solicitud']))?>
		<?php echo $frm->hidden('Solicitud.nro_solicitud_aprox',array('value'=>$solicitudes[0]['Solicitud']['nro_solicitud']))?>
		<?php echo $frm->hidden('Solicitud.guardar_fpago',array('value'=>1))?>
		<?php echo $frm->hidden('Solicitud.carga_directa',array('value'=>$solicitudes[0]['Solicitud']['carga_directa']))?>
		<?php echo $frm->submit("GUARDAR")?>
		<?php echo $frm->end()?>	
<?php endif;?>