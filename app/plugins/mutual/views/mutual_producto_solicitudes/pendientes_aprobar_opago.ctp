<?php echo $this->renderElement('head',array('title' => 'APROBAR ORDENES DE CONSUMO / SERVICIO','plugin' => 'config'))?>
<?php echo $this->renderElement('mutual_producto_solicitudes/menuApro',array('plugin' => 'mutual','accion' => 'pendientes_aprobar_opago'))?>
    <?php // debug($solicitudes)?>

<h3>ORDENES DE CONSUMO / SERVICIOS PENDIENTES DE APROBACION</h3>
<table>

	<tr>
			<th></th>
			<th>#</th>
                        <th>FECHA</th>
			<th>BENEFICIARIO</th>
            <th>SOCIO</th>
			<!--<th>BENEFICIO</th>-->
			<th>INICIA</th>
			<th>PROVEEDOR - PRODUCTO/SERVICIO</th>
			<th>TOTAL</th>
			<th>CUOTAS</th>
			<th>IMPORTE</th>
			<th>PER</th>
			<th>SC</th>
			<th>EMITIDA POR</th>
			<th></th>
			<th></th>
	</tr>
	<?php $i =0;?>
	<?php foreach ($solicitudes as $sol):
            
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}            
        ?>
        
			<tr <?php echo $class?>>
				<td style="border-top: 1px solid #666666;">
					<?php 
						if($sol['solicitud']['tipo_orden_dto'] == 'OSERV') echo $frm->btnForm(array('URL'=>'/mutual/mutual_servicio_solicitudes/pendientes_aprobar/?ORD='.$sol['solicitud']['id'],'LABEL' => 'APROBAR'));
						else if($sol['solicitud']['tipo_orden_dto'] == Configure::read('APLICACION.tipo_orden_dto_credito')) echo $frm->btnForm(array('URL'=>'/mutual/mutual_producto_solicitudes/creditos_pendientes_aprobar_opago/?ORD='.$sol['solicitud']['id'],'LABEL' => 'APROBAR'));
						else echo $frm->btnForm(array('URL'=>'/mutual/mutual_producto_solicitudes/pendientes_aprobar_opago/?ORD='.$sol['solicitud']['id'],'LABEL' => 'APROBAR'));
					?>
				</td>
				<td style="border-top: 1px solid #666666;" align="center"><strong><?php echo $controles->linkModalBox($sol['solicitud']['id'],array('title' => 'SOLICITUD DE CREDITO #' . $sol['solicitud']['id'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$sol['solicitud']['id'],'h' => 450, 'w' => 850))?></strong></td>
				<td style="border-top: 1px solid #666666;" align="center"><?php echo $util->armaFecha($sol['solicitud']['fecha'])?></td>
                                <td style="border-top: 1px solid #666666; nowrap"><?php echo (!empty($sol['solicitud']['socio_id']) ? $this->renderElement('socios/link_to_estado_cuenta',array('texto' => $sol['solicitud']['beneficiario'],'socio_id' => $sol['solicitud']['socio_id'],'plugin' => 'pfyj')) : $controles->openWindow($sol['solicitud']['beneficiario'],'/pfyj/personas/view/'.$sol['solicitud']['persona_id']))?></td>
				<td style="border-top: 1px solid #666666;text-align: center;font-weight: bold;color: green;"><?php echo (!empty($sol['solicitud']['socio_id']) ? "#".$sol['solicitud']['socio_id'] : "")?></td>
                <!--<td style="border-top: 1px solid #666666;"><?php echo $sol['solicitud']['beneficio_str']?></td>-->
				<!--
				<td align="center"><?php // echo $util->armaFecha($sol['solicitud']['fecha'])?></td>
				<td align="center"><?php // echo $util->armaFecha($sol['solicitud']['fecha_pago'])?></td>
				-->
                                <td style="border-top: 1px solid #666666;" align="center"><strong><?php echo $util->periodo($sol['solicitud']['periodo_ini'])?></strong></td>
				<td style="border-top: 1px solid #666666;"><?php echo $sol['solicitud']['proveedor_producto']?></td>
				<td style="border-top: 1px solid #666666;" align="right"><?php echo number_format($sol['solicitud']['importe_total'],2)?></td>
				<td style="border-top: 1px solid #666666;" align="center"><?php echo $sol['solicitud']['cuotas']?></td>
				<td style="border-top: 1px solid #666666;" align="right"><?php echo number_format($sol['solicitud']['importe_cuota'],2);?></td>
				<td style="border-top: 1px solid #666666;" align="center"><?php echo $controles->OnOff($sol['solicitud']['permanente'],true)?></td>
				<td style="border-top: 1px solid #666666;" align="center"><?php echo $controles->OnOff($sol['solicitud']['sin_cargo'],true)?></td>
				<td style="border-top: 1px solid #666666;" align="center">
                                    <?php // echo $sol['solicitud']['emitida_por']?>
                                
					<?php 
					if(empty($sol['solicitud']['vendedor_nombre'])) echo $sol['solicitud']['user_created'];
					else echo $sol['solicitud']['vendedor_nombre'];
					?>                                    
                                
                                </td>
				<td style="border-top: 1px solid #666666;" align="center">
					<?php 
						if($sol['solicitud']['tipo_orden_dto'] == 'OSERV') echo $controles->botonGenerico('/mutual/mutual_servicio_solicitudes/del/'.$sol['solicitud']['id'],'controles/user-trash-full.png',null,null,"ANULAR LA SOLICITUD ".$sol['solicitud']['tipo_numero']."?");
						else echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/del/'.$sol['solicitud']['id'],'controles/user-trash-full.png',null,null,"ANULAR LA SOLICITUD ".$sol['solicitud']['tipo_numero']."?");
						?>
					<?php // echo $controles->getAcciones($sol['solicitud']['id'],false,false) ?>
				</td>
				<td style="border-top: 1px solid #666666;" align="center">
				<?php
					if($sol['solicitud']['tipo_orden_dto'] == 'OSERV'){
						echo $controles->btnImprimirPDF('','/mutual/mutual_servicio_solicitudes/imprimir_solicitud/'.$sol['solicitud']['id'],'blank');
					}else if($sol['solicitud']['tipo_orden_dto'] == Configure::read('APLICACION.tipo_orden_dto_credito')){
						echo $controles->btnImprimirPDF('','/mutual/mutual_producto_solicitudes/imprimir_credito_mutual_pdf/'.$sol['solicitud']['id'],'blank');	
					}else{
					 	echo $controles->btnImprimirPDF('','/mutual/mutual_producto_solicitudes/imprimir_orden_pdf/'.$sol['solicitud']['id'].'/'.$sol['solicitud']['permanente'],'blank');
					}
				?>
				</td>
			</tr>	
	
	<?php endforeach;?>

</table>



