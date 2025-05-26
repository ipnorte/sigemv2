<?php 
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>
<h3>ORDENES DE CANCELACION</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<!-- <div class="actions"><?php //   echo $controles->botonGenerico('generar/'.$socio['Persona']['id'],'controles/add.png','Nueva Orden de Cancelacion',array('target' => '_blank'))?></div> -->

	<div class="actions">
		<?php echo $controles->botonGenerico('generar/'.$socio['Persona']['id'],'controles/add.png','Nueva Orden de Cancelacion PROPIA',array('target' => '_blank'))?>
		&nbsp;|&nbsp;
		<?php echo $controles->botonGenerico('terceros_generar/'.$socio['Persona']['id'],'controles/add.png','Nueva Orden de Cancelacion de TERCEROS',array('target' => '_blank'))?>
	</div>
<hr/>
<h4>LISTADO DE ORDENES PROCESADAS</h4>
<?php if(!empty($cancelaciones)):?>
	<?php // debug($cancelaciones)?>
	<table>
		<tr>
			<th>#</th>
			<th>TIPO</th>
			<th>ORDEN</th>
			<th>TIPO / NUMERO</th>
			<th>PROVEEDOR / PRODUCTO</th>
			<th>CUOTAS</th>
			<th>A LA ORDEN DE</th>
			<th>CONCEPTO</th>
			<th>DEUDA PROVEEDOR</th>
			<th>SALDO ORDEN DTO</th>
			<th>IMPORTE SELECCIONADO</th>
			<th>DEBITO / CREDITO</th>
			<th>VENCIMIENTO</th>
			<th>TOTAL ORDEN</th>
			<th>OBSERVACIONES</th>
			<th>CANCELA CON</th>
			<th>FECHA IMPUTACION</th>
			<th></th>
			<th></th>
                        <th></th>
		</tr>
                <?php $importe_proveedor = $saldo_orden_dto = $importe_seleccionado = $importe_diferencia = $orden_dto_cancela_total = 0;?>
		<?php foreach($cancelaciones as $cancelacion):?>
                    <?php 
                    $importe_proveedor += $cancelacion['CancelacionOrden']['importe_proveedor'];
                    $saldo_orden_dto += $cancelacion['CancelacionOrden']['saldo_orden_dto'];
                    $importe_seleccionado += $cancelacion['CancelacionOrden']['importe_seleccionado'];
                    $importe_diferencia += $cancelacion['CancelacionOrden']['importe_diferencia'];
                    $orden_dto_cancela_total += $cancelacion['CancelacionOrden']['orden_dto_cancela_total'];
                    ?>                
			<tr>
				<td><strong><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cancelacion['CancelacionOrden']['id'],'h' => 450, 'w' => 750))?></strong></td>
				<td align="center"><?php echo $cancelacion['CancelacionOrden']['tipo_cancelacion_desc']." (".$cancelacion['CancelacionOrden']['origen'].")"?></td>
				<td align="center"><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['orden_dto_cancela_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cancelacion['CancelacionOrden']['orden_dto_cancela_id'],'url' => '/mutual/orden_descuentos/view/'.$cancelacion['CancelacionOrden']['orden_dto_cancela_id'].'/'.$cancelacion['CancelacionOrden']['socio_id'],'h' => 450, 'w' => 750))?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['orden_dto_cancela_tipo_nro']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['orden_dto_cancela_proveedor_producto']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['cuotas_str']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['a_la_orden_de']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['concepto']?></td>
				<td align="right"><strong><?php echo number_format($cancelacion['CancelacionOrden']['importe_proveedor'],2)?></strong></td>
				<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['saldo_orden_dto'],2)?></td>
				<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['importe_seleccionado'],2)?></td>
                                <td style="text-align: right;">
					<?php
						if(!empty($cancelacion['CancelacionOrden']['tipo_cuota_diferencia'])){
//							echo $cancelacion['CancelacionOrden']['tipo_cuota_diferencia_desc'];
//							echo "&nbsp;= \$";
							echo number_format($cancelacion['CancelacionOrden']['importe_diferencia'],2);
						}
					?>
				</td>
				<td align="center"><strong><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto'])?></strong></td>
				<td align="right"><strong><?php echo number_format($cancelacion['CancelacionOrden']['orden_dto_cancela_total'],2)?></strong></td>
				<td><?php echo $cancelacion['CancelacionOrden']['observaciones']?></td>
				<td align="center">
                                     <?php if(!empty($cancelacion['CancelacionOrden']['mutual_producto_solicitud_id'])):?>
                                        <?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['cancela_con_str'],array('title' => 'ORDEN DE CONSUMO #' . $cancelacion['CancelacionOrden']['mutual_producto_solicitud_id'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$cancelacion['CancelacionOrden']['mutual_producto_solicitud_id'].'/1','h' => 450, 'w' => 850))?>
                                    <?php elseif(isset($cancelacion['CancelacionOrden']['cobro_id']) && !empty($cancelacion['CancelacionOrden']['cobro_id'])):?>
                                        <?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['cancela_con_str'],array('title' => 'ORDEN DE COBRO #' . $cancelacion['CancelacionOrden']['cobro_id'],'url' => '/mutual/orden_descuento_cobros/view/'.$cancelacion['CancelacionOrden']['cobro_id'],'h' => 450, 'w' => 750))?>
                                    <?php elseif(isset($cancelacion['CancelacionOrden']['aman_nro_solicitud']) && !empty($cancelacion['CancelacionOrden']['aman_nro_solicitud'])):?>    
                                        <?php echo ($cancelacion['CancelacionOrden']['aman_codigo_estado'] == 14 || $cancelacion['CancelacionOrden']['aman_codigo_estado'] == 19 ? $controles->linkModalBox($cancelacion['CancelacionOrden']['cancela_con_str'],array('title' => 'CARATULA EXPEDIENTE #' . $cancelacion['CancelacionOrden']['aman_nro_solicitud'],'url' => '/v1/solicitudes/resumen_expediente/'.$cancelacion['CancelacionOrden']['aman_nro_solicitud'],'h' => 450, 'w' => 850)) : $controles->linkModalBox($cancelacion['CancelacionOrden']['cancela_con_str'],array('title' => 'CARATULA SOLICITUD #' . $cancelacion['CancelacionOrden']['aman_nro_solicitud'],'url' => '/v1/solicitudes/caratula/'.$cancelacion['CancelacionOrden']['aman_nro_solicitud'],'h' => 450, 'w' => 850))) ?>                                     
                                    <?php else:?>    
                                        <?php echo $cancelacion['CancelacionOrden']['cancela_con_str']?>                                    
                                    <?php endif;?>
				</td>
				<td align="center"><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_imputacion'])?></td>
				<td><?php echo $controles->btnImprimirPDF('','/mutual/cancelacion_ordenes/view/'.$cancelacion['CancelacionOrden']['id'].'/1','_blank')?></td>
				<td><?php //   echo $controles->botonGenerico('anular/'.$cancelacion['CancelacionOrden']['id'],'controles/stop1.png','',null,'ANULAR LA ORDEN #'.$cancelacion['CancelacionOrden']['id'])?></td>
					<td>
                                            <?php if($cancelacion['CancelacionOrden']['estado'] == 'E' && $cancelacion['CancelacionOrden']['saldo_cuotas_orden'] == 0):?>
                                            <!--<span style="color: red;">El saldo actual de las cuotas incluidas es MENOR al saldo original seleccionado al momento de generar esta orden.</span>-->                                           
                                            <?php endif;?>
                                        </td>                                        
                                
                                
		<?php endforeach;?>
                        <tr class="totales">
                            <th colspan="8">TOTALES</th>
                            <th><?php echo number_format($importe_proveedor,2)?></th>
                            <th><?php echo number_format($saldo_orden_dto,2)?></th>
                            <th><?php echo number_format($importe_seleccionado,2)?></th>
                            <th><?php echo number_format($importe_diferencia,2)?></th>
                            <th></th>
                            <th><?php echo number_format($orden_dto_cancela_total,2)?></th>
                            <th colspan="5"></th>
                        </tr>                                        
		</table>
<?php else:?>
	NO EXISTEN ORDENES DE CANCELACION PROCESADAS
<?php endif;?>
<br/>
<br/>
<br/>
<?php if(count($cancelacionesEmi) != 0):?>
	
<h4>DETALLE DE ORDENES DE CANCELACION EMITIDAS</h4>
	<table>
		<tr>
			<th>#</th>
			<th>TIPO</th>
			<th>ORDEN</th>
			<th>TIPO / NUMERO</th>
			<th>PROVEEDOR / PRODUCTO</th>
			<th>CUOTAS</th>
			<th>A LA ORDEN DE</th>
			<th>CONCEPTO</th>
			<th>DEUDA PROVEEDOR</th>
			<th>SALDO ORDEN DTO</th>
			<th>IMPORTE SELECCIONADO</th>
			<th>DEBITO / CREDITO</th>
			<th>VENCIMIENTO</th>
			<th>TOTAL ORDEN</th>
			<th>OBSERVACIONES</th>
			<th>CANCELA CON</th>
			<th>FECHA IMPUTACION</th>
			<th></th>
			<th></th>
                        <th></th>
		</tr>
                <?php $importe_proveedor = $saldo_orden_dto = $importe_seleccionado = $importe_diferencia = $orden_dto_cancela_total = 0;?>
		<?php foreach($cancelacionesEmi as $cancelacion):?>
                    <?php 
                    $importe_proveedor += $cancelacion['CancelacionOrden']['importe_proveedor'];
                    $saldo_orden_dto += $cancelacion['CancelacionOrden']['saldo_orden_dto'];
                    $importe_seleccionado += $cancelacion['CancelacionOrden']['importe_seleccionado'];
                    $importe_diferencia += $cancelacion['CancelacionOrden']['importe_diferencia'];
                    $orden_dto_cancela_total += $cancelacion['CancelacionOrden']['orden_dto_cancela_total'];
                    ?>                    
			<tr>
				<td><strong><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cancelacion['CancelacionOrden']['id'],'h' => 450, 'w' => 750))?></strong></td>
				<td align="center"><?php echo $cancelacion['CancelacionOrden']['tipo_cancelacion_desc']?></td>
				<td align="center"><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['orden_dto_cancela_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cancelacion['CancelacionOrden']['orden_dto_cancela_id'],'url' => '/mutual/orden_descuentos/view/'.$cancelacion['CancelacionOrden']['orden_dto_cancela_id'].'/'.$cancelacion['CancelacionOrden']['socio_id'],'h' => 450, 'w' => 750))?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['orden_dto_cancela_tipo_nro']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['orden_dto_cancela_proveedor_producto']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['cuotas_str']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['a_la_orden_de']?></td>
				<td><?php echo $cancelacion['CancelacionOrden']['concepto']?></td>
				<td align="right"><strong><?php echo number_format($cancelacion['CancelacionOrden']['importe_proveedor'],2)?></strong></td>
				<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['saldo_orden_dto'],2)?></td>
				<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['importe_seleccionado'],2)?></td>
				<td align="right">
					<?php
						if(!empty($cancelacion['CancelacionOrden']['tipo_cuota_diferencia'])){
//							echo $cancelacion['CancelacionOrden']['tipo_cuota_diferencia_desc'];
//							echo "&nbsp;= \$";
							echo number_format($cancelacion['CancelacionOrden']['importe_diferencia'],2);
						}
					?>
				</td>
				<td align="center"><strong><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto'])?></strong></td>
				<td align="right"><strong><?php echo number_format($cancelacion['CancelacionOrden']['orden_dto_cancela_total'],2)?></strong></td>
				<td><?php echo $cancelacion['CancelacionOrden']['observaciones']?></td>
				<td align="center">
                                    <?php if(!empty($cancelacion['CancelacionOrden']['mutual_producto_solicitud_id'])):?>
                                        <?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['cancela_con_str'],array('title' => 'ORDEN DE CONSUMO #' . $cancelacion['CancelacionOrden']['mutual_producto_solicitud_id'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$cancelacion['CancelacionOrden']['mutual_producto_solicitud_id'].'/1','h' => 450, 'w' => 850))?>
                                    <?php elseif(isset($cancelacion['CancelacionOrden']['cobro_id']) && !empty($cancelacion['CancelacionOrden']['cobro_id'])):?>
                                        <?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['cancela_con_str'],array('title' => 'ORDEN DE COBRO #' . $cancelacion['CancelacionOrden']['cobro_id'],'url' => '/mutual/orden_descuento_cobros/view/'.$cancelacion['CancelacionOrden']['cobro_id'],'h' => 450, 'w' => 750))?>
                                    <?php elseif(isset($cancelacion['CancelacionOrden']['aman_nro_solicitud']) && !empty($cancelacion['CancelacionOrden']['aman_nro_solicitud'])):?>    
                                        <?php echo ($cancelacion['CancelacionOrden']['aman_codigo_estado'] == 14 || $cancelacion['CancelacionOrden']['aman_codigo_estado'] == 19 ? $controles->linkModalBox($cancelacion['CancelacionOrden']['cancela_con_str'],array('title' => 'CARATULA EXPEDIENTE #' . $cancelacion['CancelacionOrden']['aman_nro_solicitud'],'url' => '/v1/solicitudes/resumen_expediente/'.$cancelacion['CancelacionOrden']['aman_nro_solicitud'],'h' => 450, 'w' => 850)) : $controles->linkModalBox($cancelacion['CancelacionOrden']['cancela_con_str'],array('title' => 'CARATULA SOLICITUD #' . $cancelacion['CancelacionOrden']['aman_nro_solicitud'],'url' => '/v1/solicitudes/caratula/'.$cancelacion['CancelacionOrden']['aman_nro_solicitud'],'h' => 450, 'w' => 850))) ?>                                     
                                    <?php else:?>    
                                        <?php echo $cancelacion['CancelacionOrden']['cancela_con_str']?>                                    
                                    <?php endif;?>
				</td>
				<td align="center"><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_imputacion'])?></td>
				<td><?php echo $controles->btnImprimirPDF('','/mutual/cancelacion_ordenes/view/'.$cancelacion['CancelacionOrden']['id'].'/1','_blank')?></td>
				<td><?php echo $controles->botonGenerico('/mutual/cancelacion_ordenes/borrar_desde_padron/'.$cancelacion['CancelacionOrden']['id'].'/'.$cancelacion['CancelacionOrden']['socio_id'],'controles/user-trash.png','',null,'Borrar la Orden de Cancelacion #'.$cancelacion['CancelacionOrden']['id'])?></td>
                                <td>
                                    <?php if($cancelacion['CancelacionOrden']['estado'] == 'E' && $cancelacion['CancelacionOrden']['saldo_cuotas_orden'] == 0):?>
                                    <!--<span style="color: red;">El saldo actual de las cuotas incluidas es MENOR al saldo original seleccionado al momento de generar esta orden.</span>-->                                           
                                    <?php endif;?>
                                </td>                                  
		<?php endforeach;?>
                        <tr class="totales">
                            <th colspan="8">TOTALES</th>
                            <th><?php echo number_format($importe_proveedor,2)?></th>
                            <th><?php echo number_format($saldo_orden_dto,2)?></th>
                            <th><?php echo number_format($importe_seleccionado,2)?></th>
                            <th><?php echo number_format($importe_diferencia,2)?></th>
                            <th></th>
                            <th><?php echo number_format($orden_dto_cancela_total,2)?></th>
                            <th colspan="5"></th>
                        </tr>                                
		</table>

<div style="clear: both;"><br/></div>
<hr/>
	<?php echo $frm->btnForm(array('LABEL' => 'GENERAR ORDEN DE COBRO POR CAJA','URL' => '/mutual/cancelacion_ordenes/generar_orden_cobro_caja_recibo/'.$socio['Persona']['id']))?>
	<?php // echo $frm->btnForm(array('LABEL' => 'ORDEN DE COBRO CAJA A�OS ANTER.','URL' => '/mutual/cancelacion_ordenes/generar_orden_cobro_caja/'.$socio['Persona']['id']))?>
<?php endif;?>

<?php //   debug($cancelaciones_emitidas)?>

<?php

//debug($cancelaciones);
//debug($cancelacionesEmi);

//exit;

?>