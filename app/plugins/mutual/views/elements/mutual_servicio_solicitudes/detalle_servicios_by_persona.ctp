<?php 
$solicitudes = $this->requestAction('mutual/mutual_servicio_solicitudes/get_solicitudes/' . $persona_id);
$operativa = (isset($operativa) ? $operativa : false);
?>
<?php if(!empty($solicitudes)):?>

	<div style="margin-bottom: 5px;font-size: 10px; color: gray;">E = EMITIDA | V = VIGENTE | B = BAJA</div>

	<table>
	
		<tr>
			<th></th>
			<th>OSERV</th>
			<th></th>
			<th>COBERTURA (D | H)</th>
			<th>APROBADA EL</th>
			<th>SERVICIO</th>
			<th>BENEFICIO</th>
			<th>CUOTAS</th>
			<th>IMP. MENSUAL</th>
		</tr>
		<?php $ACU_IMP_CUOTA = 0;?>
		<?php foreach($solicitudes as $solicitud):?>
		
			<?php 
				$importe_cuota = ($solicitud['MutualServicioSolicitud']['cuotas'] != 0 ? $solicitud['MutualServicioSolicitud']['importe_cuota'] : $solicitud['MutualServicioSolicitud']['importe_mensual_total']);
				$ACU_IMP_CUOTA += $importe_cuota; 
			?>
		
			<tr class="<?php echo (!empty($solicitud['MutualServicioSolicitud']['periodo_hasta']) ? "activo_0" : ($solicitud['MutualServicioSolicitud']['aprobada'] == 1 ? "alt" : "amarillo"))?>">
				<td style="border-top: 1px solid #666666;">
					<?php if(empty($solicitud['MutualServicioSolicitud']['periodo_hasta'])):?>
					<?php echo $controles->botonGenerico('/mutual/mutual_servicio_solicitudes/imprimir_solicitud/'.$solicitud['MutualServicioSolicitud']['id'],'controles/printer.png',null,array('target' => 'blank'))?>
					<?php else:?>
					<?php echo $controles->botonGenerico('/mutual/mutual_servicio_solicitudes/imprimir_solicitud/'.$solicitud['MutualServicioSolicitud']['id']."/1",'controles/printer.png',null,array('target' => 'blank'))?>
					<?php endif;?>
				</td>			
				<td style="border-top: 1px solid #666666;">#<?php echo $solicitud['MutualServicioSolicitud']['id']?></td>
				<td style="border-top: 1px solid #666666;font-weight: bold;color:<?php echo ($solicitud['MutualServicioSolicitud']['estado_actual_min'] == 'B' ? 'red' : ($solicitud['MutualServicioSolicitud']['estado_actual_min'] == 'V' ? 'green' : 'black'))?>;" align="left"><?php echo $solicitud['MutualServicioSolicitud']['estado_actual_min']?></td>
				<td style="border-top: 1px solid #666666;font-weight: bold;text-align: center;" nowrap="nowrap" align="left">
					<span style="color:green;"><?php echo $util->armaFecha($solicitud['MutualServicioSolicitud']['fecha_alta_servicio'])?></span>
					<?php if(!empty($solicitud['MutualServicioSolicitud']['fecha_baja_servicio'])):?>
					|
					<span style="color:red;"><?php echo $util->armaFecha($solicitud['MutualServicioSolicitud']['fecha_baja_servicio'])?></span>
					<?php endif;?>
				</td>
				<td style="border-top: 1px solid #666666;" align="center"><?php echo $util->armaFecha($solicitud['MutualServicioSolicitud']['fecha_aprobacion'])?></td>
				<td style="border-top: 1px solid #666666;font-weight: bold;"><?php echo $solicitud['MutualServicioSolicitud']['mutual_proveedor_servicio_ref']?></td>
				<td style="border-top: 1px solid #666666;"><?php echo $solicitud['MutualServicioSolicitud']['beneficio']?></td>
				<td style="border-top: 1px solid #666666;text-align: center;"><?php echo $solicitud['MutualServicioSolicitud']['cuotas']?></td>
				<td style="border-top: 1px solid #666666;text-align: right;"><?php echo $util->nf($importe_cuota)?></td>
			</tr>
			<?php //   $solicitud['MutualServicioSolicitudAdicional'] = null;?>
			<?php if(!empty($solicitud['MutualServicioSolicitudAdicional'])):?>
			
				<?php foreach($solicitud['MutualServicioSolicitudAdicional'] as $adicional):?>
				
					<tr class="<?php echo (!empty($adicional['periodo_hasta']) ? "activo_0" : "")?> italic">
						<td colspan="3"></td>
						<td style="text-align: center;">
						<span style="color:green;"><?php echo $util->armaFecha($adicional['fecha_alta'])?></span>
						<?php if(!empty($adicional['fecha_baja'])):?>
						|
						<span style="color:red;"><?php echo $util->armaFecha($adicional['fecha_baja'])?></span>
						<?php endif;?>
						</td>
						<td colspan="5"><?php echo $adicional['adicional_tdoc_ndoc_apenom']?> (<?php echo $adicional['adicional_edad']?> a&ntilde;o/s) (<?php echo $adicional['adicional_vinculo']?>)</td>
						
					</tr>

				<?php //   debug($adicional)?>
				
				<?php endforeach;?>
			
			<?php endif;?>
		
		<?php endforeach;?>
		<tr class='subtotales'>
			<th colspan="8" style="text-align: right;">TOTAL MENSUAL</th>
			<th style="text-align: right;"><?php echo $util->nf($ACU_IMP_CUOTA)?></th>
		</tr>
	
	</table>

<?php else: ?>

	<h4>*** SIN SERVICIOS VIGENTES ***</h4>

<?php endif;?>

<?php //   debug($solicitudes)?>