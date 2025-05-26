<?php echo $this->renderElement('head',array('title' => 'APROBAR ORDENES DE CONSUMO / SERVICIO','plugin' => 'config'))?>

<?php //   debug($aAprobar)?>

<h3>ORDENES DE CONSUMO / SERVICIOS PENDIENTES DE APROBACION</h3>
<table>

	<tr>
			<th></th>
			<th>#</th>
			<th>BENEFICIARIO</th>
			<th>BENEFICIO</th>
<!--			<th>EMITIDA</th>-->
<!--			<th>PAGO/ALTA</th>-->
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
	
	<?php foreach ($aAprobar as $sol):?>
			<tr class="<?php echo ($sol['tipo_orden_dto'] == 'OSERV' ? "altrow" : "amarillo")?>">
				<td style="border-top: 1px solid #666666;">
					<?php 
						if($sol['tipo_orden_dto'] == 'OSERV') echo $frm->btnForm(array('URL'=>'/mutual/mutual_servicio_solicitudes/pendientes_aprobar/?ORD='.$sol['id'],'LABEL' => 'APRO'));
						else echo $frm->btnForm(array('URL'=>'/mutual/mutual_producto_solicitudes/pendientes_aprobar/?ORD='.$sol['id'],'LABEL' => 'APRO'));
					?>
				</td>
				<td style="border-top: 1px solid #666666;" align="center"><strong><?php echo $sol['tipo_numero']?></strong></td>
				<td style="border-top: 1px solid #666666;"><strong><?php echo $this->renderElement('socios/link_to_estado_cuenta',array('texto' => $sol['beneficiario'],'socio_id' => $sol['socio_id'],'plugin' => 'pfyj'))?></strong></td>
				<td style="border-top: 1px solid #666666;"><?php echo $sol['beneficio_str']?></td>
				<!--
				<td align="center"><?php echo $util->armaFecha($sol['fecha'])?></td>
				<td align="center"><?php echo $util->armaFecha($sol['fecha_pago'])?></td>
				-->
				<td style="border-top: 1px solid #666666;" align="center"><?php echo $util->periodo($sol['periodo_ini'])?></td>
				<td style="border-top: 1px solid #666666;"><?php echo $sol['proveedor_producto']?></td>
				<td style="border-top: 1px solid #666666;" align="right"><?php echo number_format($sol['importe_total'],2)?></td>
				<td style="border-top: 1px solid #666666;" align="center"><?php echo $sol['cuotas']?></td>
				<td style="border-top: 1px solid #666666;" align="right"><?php echo number_format($sol['importe_cuota'],2);?></td>
				<td style="border-top: 1px solid #666666;" align="center"><?php echo $controles->OnOff($sol['permanente'],true)?></td>
				<td style="border-top: 1px solid #666666;" align="center"><?php echo $controles->OnOff($sol['sin_cargo'],true)?></td>
				<td style="border-top: 1px solid #666666;" align="center"><?php echo $sol['emitida_por']?></td>
				<td style="border-top: 1px solid #666666;" align="center">
					<?php 
						if($sol['tipo_orden_dto'] == 'OSERV') echo $controles->botonGenerico('/mutual/mutual_servicio_solicitudes/del/'.$sol['id'],'controles/user-trash-full.png');
						else echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/del/'.$sol['id'],'controles/user-trash-full.png');
						?>
					<?php //   echo $controles->getAcciones($sol['id'],false,false) ?>
				</td>
				<td style="border-top: 1px solid #666666;" align="center">
				<?php
					if($sol['tipo_orden_dto'] == 'OSERV') echo $controles->btnImprimir('','/mutual/mutual_servicio_solicitudes/imprimir_solicitud/'.$sol['id'],'blank');
					else echo $controles->btnImprimir('','/mutual/mutual_producto_solicitudes/imprimir_orden_pdf/'.$sol['id'].'/'.$sol['permanente'],'blank');
				?>
				</td>
			</tr>	
	
	<?php endforeach;?>

</table>



