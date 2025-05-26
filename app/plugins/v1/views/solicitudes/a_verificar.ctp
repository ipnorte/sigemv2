<?php echo $this->renderElement('head',array('title' => 'APROBAR SOLICITUDES DE CREDITO :: GENERAR ORDEN DE DESCUENTO','plugin' => 'config'))?>

<div id="FormSearch">
	<?php echo $form->create(null,array('action'=> 'a_verificar'));?>
	<table>
		<tr>
			<td colspan="5"><strong>BUSCAR SOLICITUD DE CREDITO A VERIFICAR POR ADMINISTRACION</strong></td>
		</tr>
		<tr>
			<td colspan="2"><?php echo $this->requestAction('/v1/tglobal/combo/BENEFICIO/Solicitud.codigo_beneficio/XXTO/0/1/'.$beneficio)?></td>
			<td align="right"><?php echo $frm->input('Solicitud.fecha_d',array('label'=>'A VERIFICAR DESDE (DD/MM/AAAA)','size'=>11,'maxlength'=>10,'value'=>$fecha_desde)); ?></td>
			<td align="left"><?php echo $frm->input('Solicitud.fecha_h',array('label'=>'HASTA (DD/MM/AAAA)','size'=>11,'maxlength'=>10,'value'=>$fecha_hasta)); ?> </td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo $frm->input('Persona.apellido',array('label'=>'APELLIDO','size'=>20,'maxlength'=>100,'value'=>$apellido)); ?></td>
			<td><?php echo $frm->input('Persona.nombre',array('label'=>'NOMBRE','size'=>20,'maxlength'=>100,'value'=>$nombre)); ?></td>
			<td><?php echo $frm->number('Solicitud.nro_solicitud_aprox',array('label'=>'NRO DE SOLICITUD','size'=>11,'maxlength'=>10,'value'=>$solicitud)); ?></td>
			<td><?php echo $frm->submit('APROXIMAR');?></td>
			<td></td>
		</tr>
	</table>
	<?php echo $form->end();?>
</div>
<div style="clear: both;"></div>
<?php if(count($solicitudes)!=0):?>
	<div class="areaDatoForm2">Se muestran los primeros <strong><?php echo count($solicitudes)?></strong> registros</div>

	<table style='border-collapse:collapse;'>
		<tr>
			<th></th>
			<th>Beneficiario</th>
			<th>Beneficio</th>
			<th>Solicitud</th>
			<th>Fecha Estado</th>
			<th>Producto</th>
			<th>Solicitado</th>
			<th>Cuotas</th>
			<th>Recibo</th>
			<th>Orden Pago</th>
		</tr>
		<?php //debug($solicitudes);exit(); ?>
		<?php foreach($solicitudes as $solicitud):?>
			<tr>
				
				<td><?php echo $controles->botonGenerico('/v1/solicitudes/generar_expediente/'.$solicitud['Solicitud']['nro_solicitud'],'controles/folder-open.png')?></td>
				<td nowrap="nowrap"><strong><?php echo $controles->openWindow($solicitud['personas']['apellido'] .', '.$solicitud['personas']['nombre'],'/v1/solicitudes/persona_padron/'.$solicitud['personas']['id_persona'])?></strong></td>
				<td><?php echo $solicitud['personas_beneficios']['codigo_beneficio_desc']?></td>
				<td align="center"><strong><?php echo $solicitud['Solicitud']['nro_solicitud']?></strong></td>
				<td align="center"><?php echo $util->armaFecha($solicitud['Solicitud']['fecha_estado'])?></td>
				<td>#<?php echo $solicitud['proveedores_productos']['codigo_producto']?> - <strong><?php echo $solicitud['proveedores']['razon_social']?></strong> - <?php echo $solicitud['proveedores_productos']['producto']?></td>
				<td align="right"><?php echo number_format($solicitud['Solicitud']['en_mano'],2)?></td>
				<td align="center"><?php echo $solicitud['Solicitud']['cuotas']?></td>
				<?php if($solicitud['Solicitud']['recibo_id'] > 0):?><td><? echo $html->link($solicitud['Solicitud']['recibo_link'],'/v1/solicitudes/editRecibo/'.$solicitud['Solicitud']['recibo_id']. '/'.$solicitud['Solicitud']['nro_solicitud'], array('target' => 'blank'))?></td>
				<?php else:?><td><? echo $controles->botonGenerico('/v1/solicitudes/addRecibo/'.$solicitud['Solicitud']['nro_solicitud'],'controles/book_open.png','') ?></td>
				<?php endif; ?>

				<?php if($solicitud['Solicitud']['orden_pago_id'] > 0):?><td><? echo $html->link($solicitud['Solicitud']['orden_pago_link'],'/v1/solicitudes/editOrdenPago/'.$solicitud['Solicitud']['orden_pago_id']. '/'.$solicitud['Solicitud']['nro_solicitud'], array('target' => 'blank'))?></td>
				<?php else:?><td><? echo $controles->botonGenerico('/v1/solicitudes/addOrdenPago/'.$solicitud['Solicitud']['nro_solicitud'],'controles/zone_money.png','') ?></td>
				<?php endif; ?>
			</tr>
		<?php endforeach;?>
	</table>

	<?php //   debug($solicitudes)?>
<?php endif;?>
