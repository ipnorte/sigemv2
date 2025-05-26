<?php if(count($cancelaciones)!=0):?>

	<table>
		<tr>
			<th></th>
			<th>#</th>
			<th>TIPO</th>
			<th>ORDEN</th>
			<th>TIPO / NUMERO</th>
			<th>PROVEEDOR / PRODUCTO</th>
			<th>A LA ORDEN DE</th>
			<th>DEUDA PROVEEDOR</th>
			<th>TOTAL CUOTAS</th>
			<th>VENCIMIENTO</th>
			<th></th>
			<th></th>
			
		</tr>
		<?php 
		$i=0;
		foreach($cancelaciones as $cancelacion):
			$i++;
		?>
			<tr>
				<td>
					<?php if(isset($cancelacion['CancelacionOrden']['forma_cancelacion'])):?>
						<input type="checkbox" name="data[<?php echo $modelo?>][cancelacion_orden_id][<?php echo $cancelacion['CancelacionOrden']['id']?>]" value="1" id="<?php echo $modelo?>CancelacionOrdenId_<?php echo $i?>">
						<input type="hidden" name="data[<?php echo $modelo?>][cancelacion_label][<?php echo $cancelacion['CancelacionOrden']['id']?>]" value="<?php echo "ORDEN DTO #".$cancelacion['CancelacionOrden']['orden_descuento_id']." | ".$cancelacion['CancelacionOrden']['tipo_nro_odto']." | ".$cancelacion['CancelacionOrden']['proveedor_producto_odto']?>" id="<?php echo $modelo?>CancelacionLabel_<?php echo $i?>">
					<?php endif;?>
				</td>
				<td><strong><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cancelacion['CancelacionOrden']['id'],'h' => 450, 'w' => 750))?></strong></td>
				<td align="center"><?php echo $cancelacion['CancelacionOrden']['tipo_cancelacion_desc']?></td>
				<td align="center"><?php echo $cancelacion['CancelacionOrden']['orden_descuento_id']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['tipo_nro_odto']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['proveedor_producto_odto']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['a_la_orden_de']?></td>
				<td align="right"><strong><?php echo number_format($cancelacion['CancelacionOrden']['importe_proveedor'],2)?></strong></td>
				<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['importe_seleccionado'],2)?></td>
				<td align="center"><strong><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto'])?></strong></td>
				<td><?php echo (isset($cancelacion['CancelacionOrden']['forma_cancelacion']) ? $this->renderElement('cancelacion_orden/info_pago',array('cancelacion' => $cancelacion,'plugin' => 'mutual')) : '')?></td>
				<td nowrap="nowrap"><?php echo $controles->botonGenerico('/mutual/cancelacion_ordenes/cargar_info_pago/'.$cancelacion['CancelacionOrden']['id'],'controles/edit.png','',array('target' => 'blank'))?></td>
		<?php endforeach;?>	
	</table>
	<input type="hidden" name="data[<?php echo $modelo?>][total_ordenes_cancelacion_emitidas]" id="<?php echo $modelo?>TotalOrdenesCancelacionEmitidas" value="<?php echo count($cancelaciones)?>"/>		
<?php endif;?>