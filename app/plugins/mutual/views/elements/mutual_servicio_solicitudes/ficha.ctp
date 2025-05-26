<?php $solicitud = $this->requestAction('/mutual/mutual_servicio_solicitudes/get_solicitud/' . $id);?>

<div class="areaDatoForm3">

	<h4>ORDEN DE SERVICIO #<?php echo $solicitud['MutualServicioSolicitud']['id']?></h4>	
	<div classs="row">
		SERVICIO: <strong><?php echo $solicitud['MutualServicioSolicitud']['mutual_proveedor_servicio']?></strong>
		&nbsp;&nbsp;REF:(<?php echo $solicitud['MutualServicioSolicitud']['nro_referencia_proveedor']?>)
	</div>
	<div classs="row">
		TITULAR: <strong><?php echo $solicitud['MutualServicioSolicitud']['titular_tdocndoc_apenom']?></strong>
	</div>
	<div classs="row">
		FECHA ALTA SERVICIO: <strong><?php echo $util->armaFecha($solicitud['MutualServicioSolicitud']['fecha_alta_servicio'])?></strong>
		&nbsp;&nbsp;LIQUIDAR A PARTIR DE: <strong><?php echo $util->periodo($solicitud['MutualServicioSolicitud']['periodo_desde'],true)?></strong>
	</div>
	<div classs="row">
		BENEFICIO: <strong><?php echo $solicitud['MutualServicioSolicitud']['beneficio']?></strong>
	</div>
	<br/>
	<div classs="row">
		ESTADO ACTUAL: <strong><?php echo $solicitud['MutualServicioSolicitud']['estado_actual']?></strong>
	</div>
	
	<?php if($solicitud['MutualServicioSolicitud']['permanente'] == 1):?>
	
		<br/>
		<div classs="row">
			IMPORTE MENSUAL TITULAR: <strong><?php echo $util->nf($solicitud['MutualServicioSolicitud']['importe_mensual'])?></strong>
		</div>	
	
		<?php if(!empty($solicitud['MutualServicioSolicitudAdicional'])):?>
			<br/>
			<strong>ADICIONALES</strong>
			<br/>
			<table>
				<tr>
					<th></th>
					<th></th>
					<th>VINCULO</th>
					<th>DOMICILIO</th>
					<th>PERIODO DESDE</th>
					<th>PERIODO HASTA</th>
					<th>IMPORTE MENSUAL</th>
				</tr>
				<?php $acu = 0;?>
				<?php foreach($solicitud['MutualServicioSolicitudAdicional'] as $adicional):?>
					<?php $acu += $adicional['importe_mensual'];?>
					<tr class="<?php echo (!empty($adicional['periodo_hasta']) ? "activo_0" : "activo_1")?>">
						<td><?php echo $adicional['adicional_tdoc_ndoc_apenom']?></td>
						<td><?php echo $adicional['edad']?></td>
						<td><?php echo $adicional['adicional_vinculo']?></td>
						<td><?php echo $adicional['adicional_domicilio']?></td>
						<td align="center"><?php echo $util->periodo($adicional['periodo_desde'])?></td>
						<td align="center"><?php if(!empty($adicional['periodo_hasta'])) echo $util->periodo($adicional['periodo_hasta'])?></td>
						<td align="right"><?php echo $util->nf($adicional['importe_mensual'])?></td>
					</tr>
				<?php endforeach;?>
				<tr class="totales">
					<th colspan="6">TOTAL ADICIONALES</th>
					<th align="right"><?php echo $util->nf($acu)?></th>
				</tr>
			</table>
	
		<?php endif;?>
		
		<br/>
		<div classs="row">
			IMPORTE MENSUAL TOTAL: <strong><?php echo $util->nf($solicitud['MutualServicioSolicitud']['importe_mensual_total'])?></strong>
		</div>
	
	<?php else:?>
	
		<?php if(!empty($solicitud['MutualServicioSolicitudAdicional'])):?>
			<br/>
			<strong>ADICIONALES</strong>
			<br/>
			<table>
				<tr>
					<th></th>
					<th></th>
					<th>VINCULO</th>
					<th>DOMICILIO</th>
					<th>COBERTURA DESDE</th>
					<th>COBERTURA HASTA</th>
				</tr>
				<?php $acu = 0;?>
				<?php foreach($solicitud['MutualServicioSolicitudAdicional'] as $adicional):?>
					<?php $acu += $adicional['importe_mensual'];?>
					<tr class="<?php echo (!empty($adicional['periodo_hasta']) ? "activo_0" : "activo_1")?>">
						<td><?php echo $adicional['adicional_tdoc_ndoc_apenom']?></td>
						<td><?php echo $adicional['edad']?></td>
						<td><?php echo $adicional['adicional_vinculo']?></td>
						<td><?php echo $adicional['adicional_domicilio']?></td>
						<td align="center"><?php echo $util->armaFecha($adicional['fecha_alta'])?></td>
						<td align="center"><?php if(!empty($adicional['fecha_baja'])) echo $util->armaFecha($adicional['fecha_baja'])?></td>
					</tr>
				<?php endforeach;?>
			</table>
	
		<?php endif;?>	
		<br/>
		<div classs="row">
			IMPORTE TOTAL: <strong><?php echo $util->nf($solicitud['MutualServicioSolicitud']['importe_mensual_total'])?></strong>
			&nbsp;CUOTAS: <strong><?php echo $solicitud['MutualServicioSolicitud']['cuotas']?></strong>
			&nbsp;IMPORTE CUOTA: <strong><?php echo $solicitud['MutualServicioSolicitud']['importe_cuota']?></strong>
		</div>			
		
	<?php endif;?>	
</div>

<?php //   debug($solicitud)?>