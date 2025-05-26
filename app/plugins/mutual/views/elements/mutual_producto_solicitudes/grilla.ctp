<?php 
$edit = (isset($edit) ? $edit : false);
$anuladas = (isset($anuladas) ? $anuladas : false);
if(!empty($solicitudes)):
?>
<table>
	<tr>
		<?php echo ($edit ? "<th></th>" : "")?>
		<th>NUMERO</th>
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
		<th><?php echo (!$anuladas ? "EMITIDA POR" : "ANULADA POR")?></th>
		
                <?php if(!$anuladas):?>
                <th></th>
		<th>IMP.SOL.</th>
		<th>IMP.O.PAGO</th>
		<th></th>
                <th></th>
                <?php endif;?>
	
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
	<tr class="activo_<?php echo ($sol['MutualProductoSolicitud']['anulada'] == 0 ? $sol['MutualProductoSolicitud']['aprobada'] : '0')?>">
                <?php if($edit && $sol['MutualProductoSolicitud']['permanente']==1 && $sol['MutualProductoSolicitud']['estado_orden_dto']==1 && $sol['MutualProductoSolicitud']['anulada'] == 0):?>
			<td align="center"><?php echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/modificar_importe_orden_permanente/'.$sol['MutualProductoSolicitud']['id'],'controles/table_edit.png')?></td>
		<?php else:?>
			<td></td>
		<?php endif;?>
		<td align="center"><?php echo $controles->linkModalBox($sol['MutualProductoSolicitud']['id'],array('title' => 'ORDEN DE CONSUMO #' . $sol['MutualProductoSolicitud']['id'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$sol['MutualProductoSolicitud']['id'].'/1','h' => 450, 'w' => 850))?></td>
                <td align="center" <?php echo ($sol['MutualProductoSolicitud']['anulada'] == 1 ? "style='color:red;'" : "");?>><?php 
			//echo ($sol['MutualProductoSolicitud']['aprobada'] == 1 ? 'APROBADA' : (!empty($sol['MutualProductoSolicitud']['estado_desc']) ? $sol['MutualProductoSolicitud']['estado_desc'] : "EMITIDA"))
			echo $sol['MutualProductoSolicitud']['estado_desc'];
		?></td>
		<td nowrap="nowrap" align="center"><?php echo $util->armaFecha($sol['MutualProductoSolicitud']['fecha'])?></td>
		<td nowrap="nowrap" align="center"><?php echo $util->armaFecha($sol['MutualProductoSolicitud']['fecha_pago'])?></td>
		<td align="center"><?php echo $util->periodo($sol['MutualProductoSolicitud']['periodo_ini'])?></td>
		<td nowrap="nowrap"><?php echo $sol['MutualProductoSolicitud']['proveedor_producto']?></td>
		<td align="right"><strong><?php echo number_format($sol['MutualProductoSolicitud']['importe_total'],2)?></strong></td>
		<td align="center"><strong><?php echo $sol['MutualProductoSolicitud']['cuotas']?></strong></td>
		<td align="right"><?php echo number_format($sol['MutualProductoSolicitud']['importe_cuota'],2);?></td>
		<td align="center"><?php echo $controles->OnOff2($sol['MutualProductoSolicitud']['permanente'],true)?></td>
		<td align="center"><?php echo $controles->OnOff($sol['MutualProductoSolicitud']['sin_cargo'],true)?></td>
		<td><?php echo $sol['MutualProductoSolicitud']['beneficio_str']?></td>
		<td align="center">
                    <?php if($sol['MutualProductoSolicitud']['anulada'] == 0):?>
                    <?php echo $sol['MutualProductoSolicitud']['user_created'] .' - '.$sol['MutualProductoSolicitud']['created']?>
                    <?php else:?>
                    <?php echo $sol['MutualProductoSolicitud']['user_modified'] .' - '.$sol['MutualProductoSolicitud']['modified']?>
                    <?php endif;?>
                </td>
                <?php if(!$anuladas):?>
                    <td align="center">
                            <?php if(!empty($sol['MutualProductoSolicitud']['aprobada_por'])):?>
                            APROBADA POR <?php echo $sol['MutualProductoSolicitud']['aprobada_por']?> EL <?php echo $sol['MutualProductoSolicitud']['aprobada_el']?>
                            <?php endif;?>
                    </td>
                    <td align="center">
                    <?php 
                            //echo $controles->btnImprimir('','/mutual/mutual_producto_solicitudes/imprimir_orden_pdf/'.$sol['MutualProductoSolicitud']['id'].'/'.$sol['MutualProductoSolicitud']['permanente'],'blank')
                                    if($sol['MutualProductoSolicitud']['anulada'] == 0 && $sol['MutualProductoSolicitud']['tipo_orden_dto'] == Configure::read('APLICACION.tipo_orden_dto_credito')){
                                            echo $controles->btnImprimir('','/mutual/mutual_producto_solicitudes/imprimir_credito_mutual_pdf/'.$sol['MutualProductoSolicitud']['id'],'blank');
                                    }else if($sol['MutualProductoSolicitud']['anulada'] == 0){
                                            echo $controles->btnImprimir('','/mutual/mutual_producto_solicitudes/imprimir_orden_pdf/'.$sol['MutualProductoSolicitud']['id'].'/'.$sol['MutualProductoSolicitud']['permanente'],'blank');
                                    }
                    ?>
                    </td>
                    <?php if($sol['MutualProductoSolicitud']['orden_pago_id'] > 0 && $sol['MutualProductoSolicitud']['anulada'] == 0): ?>
    <!--		<td align="center"><?php // echo $controles->btnImprimir('','/mutual/mutual_producto_solicitudes/imprimir_orden_pago/'.$sol['MutualProductoSolicitud']['id'].'/'.$sol['MutualProductoSolicitud']['permanente'],'blank')?></td>-->
                    <td align="center"><?php echo $html->link($sol['MutualProductoSolicitud']['orden_pago_link'],'/mutual/mutual_producto_solicitudes/editOrdenPago/'.$sol['MutualProductoSolicitud']['orden_pago_id'].'/'.$sol['MutualProductoSolicitud']['id'].'/1',array('target' => 'blank'))?></td>
                    <?php else: ?>
                    <td></td>
                    <?php endif; ?>
                
		<td align="center"><?php if($sol['MutualProductoSolicitud']['aprobada'] == 1 && $sol['MutualProductoSolicitud']['orden_pago_id'] == 0 && $sol['MutualProductoSolicitud']['anulada'] == 0) echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/anular/'.$sol['MutualProductoSolicitud']['id'],'controles/stop1.png')?></td>
                <td><?php echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/adjuntar_documentacion/'.$sol['MutualProductoSolicitud']['id'].'/1','controles/attach.png')?></td>
                <?php endif; ?>    
	</tr>
	<?php endif;?>
<?php endforeach;?>	

</table>
<?php endif;?>