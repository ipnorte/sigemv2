<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: EXPORTAR DATOS'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>

<?php if(!empty($sociosByTurno)):?>

	<?php echo $controles->btnRew('REGRESAR AL LISTADO DE TURNOS','/mutual/liquidaciones/exportar2/'.$liquidacion['Liquidacion']['id'])?>
	<br/>	

	<h3>DETALLE DE TURNOS PROCESADOS</h3>
	
	<table>
		<tr>
			<th>EMPRESA - TURNO</th>
			<th>REG TOTAL</th>
			<th>REG. ERRORES</th>
			<th>EN DISKETTE</th>
			<th>IMP.ERROR</th>
			<th>IMP. DISKETTE</th>
		</tr>
		
		<?php $ACU_IMPO = $ACU_REGISTROS = $ACU_OK = $ACU_ERRORES = $ACU_IMPO_ERROR = 0;?>
		
		<?php foreach($sociosByTurno['info_procesada_by_turno'] as $codigoTurno => $turno):?>
		
		
			<?php //   debug($turno)?>
			
			<?php $ACU_IMPO += $turno['importe_adebitar_ok'];?>
			<?php $ACU_REGISTROS += $turno['registros_liquidados'];?>
			<?php $ACU_OK += $turno['cantidad_ok'];?>
			<?php $ACU_ERRORES += $turno['cantidad_errores'];?>
			<?php $ACU_IMPO_ERROR += $turno['importe_error'];?>
			
			<tr>
				<td><?php echo $turno['descripcion']?></td>
				<td align="center"><?php echo $turno['registros_liquidados']?></td>
				<td align="center"><?php echo $turno['cantidad_errores']?></td>
				<td align="center"><?php echo $turno['cantidad_ok']?></td>
				<td align="right"><?php echo $util->nf($turno['importe_error'])?></td>
				<td align="right"><?php echo $util->nf($turno['importe_adebitar_ok'])?></td>
			</tr>
			
			
		
		<?php endforeach;?>
		
		
		<tr class="totales">
			<th>TOTAL GENERAL</th>
			<th style="text-align: center;"><?php echo $ACU_REGISTROS?></th>
			<th style="text-align: center;"><?php echo $ACU_ERRORES?></th>
			<th style="text-align: center;"><?php echo $ACU_OK?></th>
			<th><?php echo $util->nf($ACU_IMPO_ERROR)?></th>
			<th><?php echo $util->nf($ACU_IMPO)?></th>
			
		</tr>		
		
	</table>
	
	<?php if(!empty($errores)):?>
	
		<h3>DETALLE DE ERRORES DETECTADOS (NO INCLUIDOS EN DISKETTE)</h3>
		
		<table>
		
			<tr>
				<th>SOCIO</th>
				<th>CALIFICACION</th>
				<th>REG</th>
				<th>SUCURSAL - CUENTA</th>
				<th>CBU</th>
				<th>A DEBITAR</th>
				<th></th>
				<th>MOTIVO</th>
			</tr>	
			<?php $regT = $ACU_IMPOT = $ACU_IMPODTOT = $ACU_REGISTROST = 0;?>
			<?php foreach($sociosByTurno['info_procesada_by_turno'] as $codigoTurno => $turno):?>
			
				<?php if(!empty($turno['errores'])):?>
			
					<tr>
					
						<td colspan="8"><h5><?php echo $turno['descripcion']?></h5></td>
						
					</tr>
					<?php $reg = $ACU_IMPO = $ACU_IMPODTO = $ACU_REGISTROS = 0;?>
					<?php foreach($turno['errores'] as $socio):?>
				
				
						<?php $apenom = $socio['LiquidacionSocio']['documento'] ." - <strong>" .$socio['LiquidacionSocio']['apenom']."</strong>";?>
						<?php $ACU_IMPO += $socio['LiquidacionSocio']['importe_adebitar'];?>
						<?php $ACU_IMPODTO += $socio['LiquidacionSocio']['importe_dto'];?>
						<?php $ACU_REGISTROS++;?>
						<?php $reg++;?>
						
						<tr class="<?php echo ($socio['LiquidacionSocio']['error_cbu'] == 1 ? "" : "")?>">
							<td nowrap="nowrap"><?php echo $this->renderElement('socios/link_to_liquidacion',array('plugin' => 'pfyj', 'texto' =>  $apenom,'socio_id' => $socio['LiquidacionSocio']['socio_id']))?></td>
							<td align="center"><?php echo $util->globalDato($socio['LiquidacionSocio']['ultima_calificacion'])?></td>
							<td align="center"><?php echo $socio['LiquidacionSocio']['registro']?></td>
							<td align="center"><?php echo $socio['LiquidacionSocio']['sucursal']?> - <?php echo $socio['LiquidacionSocio']['nro_cta_bco']?></td>
							<td align="center"><?php echo $socio['LiquidacionSocio']['cbu']?></td>
							<td align="right"><?php echo $util->nf($socio['LiquidacionSocio']['importe_adebitar'])?></td>
							<td><?php echo $controles->btnModalBox(array('title' => 'LIQUIDACION '.$util->periodo($liquidacion['Liquidacion']['periodo'],true),'url' => '/mutual/liquidaciones/by_socio_periodo/'.$socio['LiquidacionSocio']['socio_id'].'/'.$liquidacion['Liquidacion']['periodo'],'h' => 450, 'w' => 950))?></td>
							<td align="center"><strong><?php echo $socio['LiquidacionSocio']['error_intercambio'] ?></strong></td>
						</tr>
				
				
					<?php endforeach;?>
					
					<tr class="totales">
						<th colspan="5">SUBTOTAL [<?php echo $reg?> REGISTROS]</th>
						<th><?php echo $util->nf($ACU_IMPO)?></th>
						<th></th>
						<th></th>
					</tr>			
					
					<?php $ACU_IMPOT += $ACU_IMPO;?>
					<?php $ACU_IMPODTOT += $ACU_IMPODTO;?>
					<?php $ACU_REGISTROST += $ACU_REGISTROS;?>
					<?php $regT += $reg;?>			
				
				<?php endif;?>
				
			<?php endforeach;?>
			
			<tr class="totales">
				<th colspan="5" style="color: red;">TOTAL GENERAL NO INCLUIDO EN DISKETTE [<?php echo $regT?> REGISTROS]</th>
				<th style="color: red;"><?php echo $util->nf($ACU_IMPOT)?></th>
				<th></th>
				<th></th>
			</tr>		
		
			
		
		</table>
	
	<?php endif;?>
	
	<?php echo $this->renderElement("banco/info_diskette",array('plugin' => 'config','toPDF' => TRUE, 'toXLS' => TRUE, 'uuid' => $DISKETTE_UUID, 'listado' => '/mutual/liquidaciones/diskette_cbu_pdf/' . $liquidacion['Liquidacion']['id'] . "/1"))?>
	
<?php else:?>

	<div class="notices_error">*** SIN DATOS GENERADOS ***</div>
		
<?php endif;?>	
	
<BR/>
<?php echo $controles->btnRew('REGRESAR AL LISTADO DE TURNOS','/mutual/liquidaciones/exportar2/'.$liquidacion['Liquidacion']['id'])?>	
			
	
	
