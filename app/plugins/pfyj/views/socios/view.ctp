<div class="areaDatoForm" style="width:100%;">
	<h3>SOCIO #<?php echo $socio['Socio']['id']?></h3>
	<div class="row">
		FECHA DE ALTA:&nbsp;<strong><?php echo $util->armaFecha($socio['Socio']['fecha_alta'])?></strong>
		&nbsp;&nbsp;
		ESTADO:&nbsp;<strong><?php echo ($socio['Socio']['activo'] == 1 ? '<span style="color:green;">VIGENTE</span>' : '<span style="color:red;">NO VIGENTE</span>')?></strong>
		<?php if($socio['Socio']['activo'] == 0):?>
			&nbsp;|&nbsp;
			FECHA BAJA: &nbsp;<strong><?php echo $util->armaFecha($socio['Socio']['fecha_baja'])?></strong>
			&nbsp;|&nbsp;
			MOTIVO: <strong><?php echo $util->globalDato($socio['Socio']['codigo_baja'])?></strong>
		<?php endif;?>		
	</div>
	<div class="row">
	
		<?php if(!empty($resumen_calificaciones)):?>
			<table>
				<tr><th>RESUMEN DE CALIFICACIONES</th></tr>
				<tr>
					<td>
						<?php foreach($resumen_calificaciones as $calificacion):?>
							&nbsp;
							<span class="<?php echo $calificacion['SocioCalificacion']['calificacion']?>">
							<?php echo $calificacion['SocioCalificacion']['calificacion_desc']?>
							(<?php echo $calificacion['SocioCalificacion']['cantidad']?>)&nbsp;&nbsp;
							</span>
						
						<?php endforeach;?>
					</td>
				</tr>
			</table>				
		<?php endif;?>
	</div>
</div>
<div style="clear: both;"></div>
