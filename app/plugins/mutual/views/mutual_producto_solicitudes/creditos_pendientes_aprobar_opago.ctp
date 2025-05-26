<?php echo $this->renderElement('head',array('title' => 'APROBAR SOLICITUDES DE CREDITOS','plugin' => 'config'))?>

<h3>SOLICITUDES DE CREDITO PENDIENTES DE APROBACION</h3>

<table>

	<tr>
			<th></th>
			<th>SOLICITUD</th>
			<th>FECHA</th>
			<th>BENEFICIARIO</th>
			<th>INICIA</th>
			<th>PROVEEDOR - PRODUCTO</th>
			<th>PERCIBIDO</th>
			<th>CUOTAS</th>
			<th>IMPORTE</th>
			<th>EMITIDA POR</th>
			<th></th>
			<th></th>
			<th></th>
	</tr>
	
	<?php
	$i = 0;
	foreach ($solicitudes as $sol):
		$class = null;
		if ( ($i++ % 2) == 0 ) {
			$class = ' class="altrow"';
		}	
	?>
			<tr<?php echo $class;?>>
				<td style="border-top: 1px solid #666666;">
					<?php 
						echo $frm->btnForm(array('URL'=>'/mutual/mutual_producto_solicitudes/creditos_pendientes_aprobar/?ORD='.$sol['MutualProductoSolicitud']['id'],'LABEL' => 'APROBAR'));
					?>
				</td>
				<td style="border-top: 1px solid #666666;" align="center"><strong><?php echo $controles->linkModalBox($sol['MutualProductoSolicitud']['nro_print'],array('title' => 'SOLICITUD DE CREDITO #' . $sol['MutualProductoSolicitud']['nro_print'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$sol['MutualProductoSolicitud']['id'],'h' => 450, 'w' => 850))?></td>
				<td style="border-top: 1px solid #666666;"><?php echo $util->armaFecha($sol['MutualProductoSolicitud']['fecha'])?></td>
				<td style="border-top: 1px solid #666666;" nowrap><?php echo $controles->openWindow($sol['MutualProductoSolicitud']['beneficiario'],'/pfyj/personas/view/'.$sol['MutualProductoSolicitud']['persona_id']) ?></td>
				<td style="border-top: 1px solid #666666;" align="center" nowrap><strong><?php echo $util->periodo($sol['MutualProductoSolicitud']['periodo_ini'])?></strong></td>
				<td style="border-top: 1px solid #666666;"><?php echo $sol['MutualProductoSolicitud']['proveedor_producto']?></td>
				<td style="border-top: 1px solid #666666;" align="right"><?php echo number_format($sol['MutualProductoSolicitud']['importe_percibido'],2)?></td>
				<td style="border-top: 1px solid #666666;" align="center"><?php echo $sol['MutualProductoSolicitud']['cuotas']?></td>
				<td style="border-top: 1px solid #666666;" align="right"><?php echo number_format($sol['MutualProductoSolicitud']['importe_cuota'],2);?></td>
				<td style="border-top: 1px solid #666666;" align="center">
					<?php 
					if(empty($sol['MutualProductoSolicitud']['vendedor_nombre'])) echo $sol['MutualProductoSolicitud']['user_created'];
					else echo $sol['MutualProductoSolicitud']['vendedor_nombre'];
					?>
				</td>
				<td style="border-top: 1px solid #666666;" align="center">
					<?php 
						echo $controles->botonGenerico('/mutual/mutual_producto_solicitudes/del/'.$sol['MutualProductoSolicitud']['id'],'controles/user-trash-full.png',null,null,"ANULAR LA SOLICITUD #" . $sol['MutualProductoSolicitud']['id'] . "?");
					?>
					<?php //   echo $controles->getAcciones($sol['id'],false,false) ?>
				</td>
				<td style="border-top: 1px solid #666666;" align="center">
				<?php
					echo $controles->btnImprimir('','/mutual/mutual_producto_solicitudes/imprimir_credito_pdf/'.$sol['MutualProductoSolicitud']['id'],'blank');
				?>
				</td>
				<td style="border-top: 1px solid #666666;" align="center"><strong><?php if(!empty($sol['MutualProductoSolicitud']['vendedor_id'])) echo $controles->linkModalBox("OBSERVAR",array('title' => 'SOLICITUD DE CREDITO #' . $sol['MutualProductoSolicitud']['nro_print'],'url' => '/mutual/mutual_producto_solicitudes/observar/'.$sol['MutualProductoSolicitud']['id'],'h' => 450, 'w' => 900))?></td>
			</tr>	
	
	<?php endforeach;?>

</table>
<?php //   DEBUG($solicitudes)?>