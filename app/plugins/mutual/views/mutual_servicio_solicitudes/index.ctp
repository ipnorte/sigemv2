<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona,'plugin' => 'pfyj'))?>

<h3>ORDENES DE SERVICIOS</h3>

<div class="actions"><?php if($persona['Persona']['fallecida'] == 0) echo $controles->botonGenerico('add/'.$persona['Persona']['id'],'controles/cart_add.png','Nueva Orden de Servicio')?></div>

<?php if(!empty($solicitudes)):?>

	<table>
	
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th>#</th>
			<th>ESTADO</th>
			<th>COBERTURA (D | H)</th>
			<th>APROBADA EL</th>
			<th>SERVICIO</th>
			<th>DESDE</th>
			<th>HASTA</th>
			<th>TOTAL/MES</th>
			<th>CUOTAS</th>
			<th>IMP.CUOTA</th>			
			<th>BENEFICIO</th>
			<th>OBSERVACIONES</th>	
			<th></th>	
		</tr>
		<?php foreach($solicitudes as $solicitud):?>
		
			<tr class="<?php echo (!empty($solicitud['MutualServicioSolicitud']['periodo_hasta']) ? "activo_0" : "altrow")?> strong">
				<td style="border-top: 1px solid #666666;"><?php if(empty($solicitud['MutualServicioSolicitud']['periodo_hasta']) && $solicitud['MutualServicioSolicitud']['aprobada'] == 1 && empty($solicitud['MutualServicioSolicitud']['cuotas'])) echo $controles->botonGenerico('baja_solicitud/'.$solicitud['MutualServicioSolicitud']['id'],'controles/stop1.png')?></td>
				<td style="border-top: 1px solid #666666;">
					<?php if(empty($solicitud['MutualServicioSolicitud']['periodo_hasta'])):?>
					<?php echo $controles->botonGenerico('imprimir_solicitud/'.$solicitud['MutualServicioSolicitud']['id'],'controles/pdf.png',null,array('target' => 'blank'))?>
					<?php else:?>
					<?php echo $controles->botonGenerico('imprimir_solicitud/'.$solicitud['MutualServicioSolicitud']['id']."/1",'controles/pdf.png',null,array('target' => 'blank'))?>
					<?php endif;?>
				</td>
				<td style="border-top: 1px solid #666666;"><?php if(empty($solicitud['MutualServicioSolicitud']['periodo_hasta']) && empty($solicitud['MutualServicioSolicitud']['cuotas'])) echo $controles->botonGenerico('agregar_adicional/'.$solicitud['MutualServicioSolicitud']['id'],'controles/user_add.png')?></td>
				<td style="border-top: 1px solid #666666;"><?php echo $solicitud['MutualServicioSolicitud']['id']?></td>
				<td style="border-top: 1px solid #666666;" align="center"><?php echo $solicitud['MutualServicioSolicitud']['estado_actual']?></td>
				<td style="border-top: 1px solid #666666;" nowrap="nowrap" align="left">
					<span style="color:green;"><?php echo $util->armaFecha($solicitud['MutualServicioSolicitud']['fecha_alta_servicio'])?></span>
					<?php if(!empty($solicitud['MutualServicioSolicitud']['fecha_baja_servicio'])):?>
					|
					<span style="color:red;"><?php echo $util->armaFecha($solicitud['MutualServicioSolicitud']['fecha_baja_servicio'])?></span>
					<?php endif;?>
				</td>
				<td style="border-top: 1px solid #666666;" align="center"><?php echo $util->armaFecha($solicitud['MutualServicioSolicitud']['fecha_aprobacion'])?></td>
				<td style="border-top: 1px solid #666666;"><?php echo $solicitud['MutualServicioSolicitud']['mutual_proveedor_servicio_ref']?></td>
				<td style="border-top: 1px solid #666666;" align="center"><?php echo $util->periodo($solicitud['MutualServicioSolicitud']['periodo_desde'])?></td>
				<td style="border-top: 1px solid #666666;color: red;" align="center"><?php echo $util->periodo($solicitud['MutualServicioSolicitud']['periodo_hasta'])?></td>
				<td style="border-top: 1px solid #666666;" align="right"><?php echo $util->nf($solicitud['MutualServicioSolicitud']['importe_mensual_total'])?></td>
				<td style="border-top: 1px solid #666666;" align="center"><?php echo $solicitud['MutualServicioSolicitud']['cuotas']?></td>
				<td style="border-top: 1px solid #666666;" align="right"><?php echo ($solicitud['MutualServicioSolicitud']['cuotas'] != 0 ? $util->nf($solicitud['MutualServicioSolicitud']['importe_cuota']) : $util->nf($solicitud['MutualServicioSolicitud']['importe_mensual_total']))?></td>
				<td style="border-top: 1px solid #666666;"><?php echo $solicitud['MutualServicioSolicitud']['beneficio']?></td>
				<td style="border-top: 1px solid #666666;font-weight: normal;"><?php echo $solicitud['MutualServicioSolicitud']['observaciones']?></td>
				<td style="border-top: 1px solid #666666;" align="center"><?php echo $solicitud['MutualServicioSolicitud']['user_created'].' - '.$solicitud['MutualServicioSolicitud']['created']?></td>			
			</tr>
			<?php //   $solicitud['MutualServicioSolicitudAdicional'] = null;?>
			<?php if(!empty($solicitud['MutualServicioSolicitudAdicional'])):?>
			
				<?php foreach($solicitud['MutualServicioSolicitudAdicional'] as $adicional):?>
				
					<tr class="<?php echo (!empty($adicional['periodo_hasta']) ? "activo_0" : "")?> italic">
					
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td>
						<?php 
						if(empty($adicional['periodo_hasta']) && $solicitud['MutualServicioSolicitud']['aprobada'] == 1 && empty($solicitud['MutualServicioSolicitud']['cuotas']) ): 
							echo $controles->botonGenerico('baja_adicional/'.$solicitud['MutualServicioSolicitud']['id'].'/'.$adicional['socio_adicional_id'].'/'.$adicional['id'],'controles/stop1.png');
						elseif(empty($solicitud['MutualServicioSolicitud']['periodo_hasta']) && $solicitud['MutualServicioSolicitud']['aprobada'] == 1 && empty($solicitud['MutualServicioSolicitud']['cuotas'])):
							echo $controles->botonGenerico('imprimir_solicitud_baja_adicional/'.$solicitud['MutualServicioSolicitud']['id']."/".$adicional['socio_adicional_id'],'controles/pdf.png',null,array('target' => 'blank'));
						endif;	
						?>
						</td>
						<td>
						<span style="color:green;"><?php echo $util->armaFecha($adicional['fecha_alta'])?></span>
						<?php if(!empty($adicional['fecha_baja'])):?>
						|
						<span style="color:red;"><?php echo $util->armaFecha($adicional['fecha_baja'])?></span>
						<?php endif;?>
						</td>
						<td colspan="2"><?php echo $adicional['adicional_tdoc_ndoc_apenom']?> (<?php echo $adicional['adicional_edad']?> a&ntilde;o/s) (<?php echo $adicional['adicional_vinculo']?>)</td>
						<td align="center"><?php echo $util->periodo($adicional['periodo_desde'])?></td>
						<td align="center" style="color: red;"><?php echo $util->periodo($adicional['periodo_hasta'])?></td>
						<td align="right"><?php echo $util->nf($adicional['importe_mensual'])?></td>
						<td colspan="3"></td>
						<td><?php echo $adicional['observaciones']?></td>
						<td></td>
						
					</tr>

				<?php //   debug($adicional)?>
				
				<?php endforeach;?>
			
			<?php endif;?>
		
		<?php endforeach;?>
	
	</table>

<?php //   debug($solicitudes)?>


<?php endif;?>