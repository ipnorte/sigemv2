		<table>
			<tr>
				<td colspan="<?php echo ($liquidacion['Liquidacion']['codigo_organismo'] != 'MUTUCORG7701' ? 12 : 11)?>">
                
                    <?php if($liquidacion['Liquidacion']['imputada'] == 0): ?>
                        <?php echo $controles->btnAjax('controles/arrow_refresh.png','/mutual/liquidaciones/importar/'.$liquidacion['Liquidacion']['id'],'grilla_archivos_importados')?>
                    <?php endif;?>                 
                </td>
				<td><?php //   echo $controles->botonGenerico('/mutual/liquidaciones/detalle_archivo_general/'.$liquidacion['Liquidacion']['id'],'controles/ms_excel.png','',array('target' => 'blank'))?></td>
			</tr>
			<tr>
				<th>#</th>
				<th>UPLOAD</th>
				<th>BANCO</th>
				<th>ARCHIVO</th>
				<th>TOTAL REG.</th>
				<?php if($liquidacion['Liquidacion']['codigo_organismo'] != 'MUTUCORG7701'): ?>
					
					<th>REG.COB.</th>
					<th>IMP.COB.</th>
					<th>RECIBO</th>
				<?php endif;?>
				<th>OBS</th>
				<th>FRAG.</th>
				<th>PRE-IMP</th>
				<th>IMPUTAR A</th>
				<th><?php //   if($liquidacion['Liquidacion']['imputada'] == 0) echo $controles->botonGenerico('/mutual/liquidaciones/importar/'.$liquidacion['Liquidacion']['id'].'/?action=dropAll','controles/user-trash-full.png',null,null,"ELIMINAR TODOS LOS ARCHIVOS SUBIDOS AL SERVIDOR?")?></th>
				<th></th>
				<th></th>
				<th></th>
			</tr>


			<?php $ACU_REGISTROS = $ACU_REGISTROS_COBRADOS = $ACU_IMPORTE_COBRADO = 0;?>	
			
		<?php foreach($archivos as $archivo):?>
		
			<?php 
			
			$ACU_REGISTROS += $archivo['LiquidacionIntercambio']['total_registros'];
			$ACU_REGISTROS_COBRADOS += $archivo['LiquidacionIntercambio']['registros_cobrados'];
			$ACU_IMPORTE_COBRADO += $archivo['LiquidacionIntercambio']['importe_cobrado'];
			?>		
		
			<tr class="<?php echo ($archivo['LiquidacionIntercambio']['proveedor_id'] != 0 ? "amarillo" : "")?>">
				<td><?php echo $archivo['LiquidacionIntercambio']['id']?></td>
				<td><?php echo $util->armaFecha($archivo['LiquidacionIntercambio']['created'])?></td>
				<td><?php echo $archivo['LiquidacionIntercambio']['banco_intercambio']?></td>
				<td><a href="<?php echo $this->base?>/<?php echo $archivo['LiquidacionIntercambio']['archivo_file']?>" target="_blank"><?php echo $archivo['LiquidacionIntercambio']['archivo_nombre']?></a></td>
				<td align='right'><?php echo $archivo['LiquidacionIntercambio']['total_registros'] ?></td>
				<?php if($liquidacion['Liquidacion']['codigo_organismo'] != 'MUTUCORG7701'): ?>
					<td align='right'><?php echo $archivo['LiquidacionIntercambio']['registros_cobrados'] ?></td>
					<td align='right'><?php echo $util->nf($archivo['LiquidacionIntercambio']['importe_cobrado']) ?></td>
					
					<?php if($archivo['LiquidacionIntercambio']['recibo_id'] > 0):?><td><?php echo $html->link($archivo['LiquidacionIntercambio']['recibo_link'],'/mutual/liquidaciones/editRecibo/'.$archivo['LiquidacionIntercambio']['id'].'/'.$archivo['LiquidacionIntercambio']['liquidacion_id'])?></td>  
					<?php elseif($archivo['LiquidacionIntercambio']['procesado'] == '1'):?><td><?php echo $controles->botonGenerico('/mutual/liquidaciones/addRecibo/'.$archivo['LiquidacionIntercambio']['id'].'/'.$archivo['LiquidacionIntercambio']['liquidacion_id'],'controles/book_open.png','') ?></td>  
					<?php else:?>  <td></td>
					<?php endif; ?>
				<?php endif;?>
				
				<td><?php echo $archivo['LiquidacionIntercambio']['observaciones']?></td>
				<td align="center"><?php echo $controles->onOff($archivo['LiquidacionIntercambio']['fragmentado'])?></td>
				<td align="center"><?php echo $controles->onOff($archivo['LiquidacionIntercambio']['procesado'])?></td>
				
				<td align="center" style="font-weight: bold;"><?php echo $archivo['LiquidacionIntercambio']['proveedor_razon_social_resumida']?></td>
				
				<td><?php if($archivo['LiquidacionIntercambio']['procesado'] == 0) echo $controles->botonGenerico('/mutual/liquidaciones/importar/'.$liquidacion['Liquidacion']['id'].'/?action=dropOne&file='.$archivo['LiquidacionIntercambio']['id'],'controles/user-trash-full.png',null,null,"ELIMINAR EL ARCHIVO ".$archivo['LiquidacionIntercambio']['archivo_nombre']."?")?></td>

				<td><?php if($archivo['LiquidacionIntercambio']['fragmentado'] == 1) echo $controles->botonGenerico('/mutual/liquidaciones/detalle_archivo/'.$liquidacion['Liquidacion']['id'] . '/'.$archivo['LiquidacionIntercambio']['id'],'controles/ms_excel.png','',array('target' => 'blank')) ?></td>
				<td><?php if($liquidacion['Liquidacion']['imputada'] == 0 && $archivo['LiquidacionIntercambio']['fragmentado'] == 1) echo $controles->botonGenerico('/mutual/liquidacion_socios/reprocesar_archivo/'.$archivo['LiquidacionIntercambio']['id'],'controles/disk.png','',array('target' => 'blank')) ?></td>
			</tr>
		<?php endforeach;?>
		<?php if($liquidacion['Liquidacion']['codigo_organismo'] != 'MUTUCORG7701'): ?>
			<tr class="totales">
				<th colspan="4">TOTAL</th>
				<th><?php echo $ACU_REGISTROS?></th>
				<th><?php echo $ACU_REGISTROS_COBRADOS?></th>
				<th><?php echo $util->nf($ACU_IMPORTE_COBRADO)?></th>
				<th colspan="9"></th>
			</tr>
		
		<?php endif;?>		
	
		</table>
