<?php 
$edit = (isset($edit) ? $edit : false);
if(!empty($solicitudes)):
?>
<table style="width: 0;">
	<tr>
		<?php echo ($edit ? "<th></th>" : "")?>
		<th>NUMERO</th>
		<th>REM</th>
		<th>ESTADO</th>
		<th>FECHA CARGA</th>
		<th>FECHA PAGO</th>
		<th>INICIA</th>
		<th>PROVEEDOR - PRODUCTO</th>
		<th>TOTAL</th>
		<th>CUOTAS</th>
		<th>IMPORTE</th>
		<th>PER</th>
		<th>SC</th>
		<th>BENEFICIO</th>
		<th>EMITIDA POR</th>
		<th></th>
		<th>IMP.SOL.</th>
		<th>CONTROL</th>
		
	</tr>

<?php

$i = 0;
foreach ($solicitudes as $sol):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
//	debug($sol);
	if(isset($sol['MutualProductoSolicitud'])):
?>	
	<tr class="<?php echo ($sol['MutualProductoSolicitud']['aprobada'] == 1 ? "activo_1" : "")?>">
		<?php if($edit && $sol['MutualProductoSolicitud']['permanente']==1 && $sol['MutualProductoSolicitud']['estado_orden_dto']==1):?>
			<td align="center"><?php echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/modificar_importe_orden_permanente/'.$sol['MutualProductoSolicitud']['id'],'controles/table_edit.png')?></td>
		<?php else:?>
			<td></td>
		<?php endif;?>
		<td align="center"><strong><?php echo $controles->linkModalBox($sol['MutualProductoSolicitud']['id'],array('title' => 'SOLICITUD DE CREDITO #' . $sol['MutualProductoSolicitud']['id'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$sol['MutualProductoSolicitud']['id'],'h' => 450, 'w' => 850))?></strong></td>
		<td align="center">
			<?php if(!empty($sol['MutualProductoSolicitud']['vendedor_remito_id'])) echo $controles->linkModalBox($sol['MutualProductoSolicitud']['vendedor_remito_id'],array('title' => 'CONSTANCIA DE PRESENTACION #' . $sol['MutualProductoSolicitud']['vendedor_remito_id'],'url' => '/ventas/vendedores/ficha_remito/'.$sol['MutualProductoSolicitud']['vendedor_remito_id'],'h' => 450, 'w' => 850))?>
		</td>		
		<td align="center"><?php echo $util->globalDato($sol['MutualProductoSolicitud']['estado'])?></td>
		<td nowrap="nowrap" align="center"><?php echo $util->armaFecha($sol['MutualProductoSolicitud']['fecha'])?></td>
		<td nowrap="nowrap" align="center"><?php if(!empty($sol['MutualProductoSolicitud']['fecha_pago']))echo $util->armaFecha($sol['MutualProductoSolicitud']['fecha_pago'])?></td>
		<td align="center"><?php echo $util->periodo($sol['MutualProductoSolicitud']['periodo_ini'])?></td>
		<td nowrap="nowrap"><?php echo $sol['MutualProductoSolicitud']['proveedor_producto']?> <?php if(!empty($sol['MutualProductoSolicitud']['proveedor_reasignada_a'])) { echo ' <span style="color: red; font-weight: bold;">('. $sol['MutualProductoSolicitud']['proveedor_reasignada_a'] . ')</span>';}?></td>
		<td align="right"><strong><?php echo number_format($sol['MutualProductoSolicitud']['importe_total'],2)?></strong></td>
		<td align="center"><strong><?php echo $sol['MutualProductoSolicitud']['cuotas']?></strong></td>
		<td align="right"><?php echo number_format($sol['MutualProductoSolicitud']['importe_cuota'],2);?></td>
		<td align="center"><?php echo $controles->OnOff2($sol['MutualProductoSolicitud']['permanente'],true)?></td>
		<td align="center"><?php echo $controles->OnOff($sol['MutualProductoSolicitud']['sin_cargo'],true)?></td>
		<td><?php echo $sol['MutualProductoSolicitud']['beneficio_str']?></td>
		<td align="center"><?php echo $sol['MutualProductoSolicitud']['user_created'] .' - '.$sol['MutualProductoSolicitud']['created']?></td>
		<td align="center">
			<?php if(!empty($sol['MutualProductoSolicitud']['aprobada_por'])):?>
			APROBADA POR <?php echo $sol['MutualProductoSolicitud']['aprobada_por']?> EL <?php echo $sol['MutualProductoSolicitud']['aprobada_el']?>
			<?php endif;?>
		</td>
		<td align="center">
			<?php 
				if($sol['MutualProductoSolicitud']['tipo_orden_dto'] == Configure::read('APLICACION.tipo_orden_dto_credito')){
					echo $controles->btnImprimir('','/mutual/mutual_producto_solicitudes/imprimir_credito_pdf/'.$sol['MutualProductoSolicitud']['id'],'blank');
				}else{
					echo $controles->btnImprimir('','/mutual/mutual_producto_solicitudes/imprimir_orden_pdf/'.$sol['MutualProductoSolicitud']['id'].'/'.$sol['MutualProductoSolicitud']['permanente'],'blank');
				}
			?>
		</td>
		<td align="center">
			<?php 
				if($sol['MutualProductoSolicitud']['aprobada'] == 1 && $sol['MutualProductoSolicitud']['orden_descuento_id'] != 0){
					echo $controles->btnImprimir('','/mutual/mutual_producto_solicitudes/imprimir_credito_pdf/'.$sol['MutualProductoSolicitud']['id']."/1",'blank');
				}
			?>
		</td>
		
	</tr>
	<?php endif;?>
	
<?php endforeach;?>	

</table>
<?php endif;?>