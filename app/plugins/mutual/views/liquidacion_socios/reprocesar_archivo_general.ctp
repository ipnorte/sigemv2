<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('head',array('plugin'=>'config','title' => 'LIQUIDACION DE DEUDA :: REPROCESAMIENTO DISKETTE BANCOS'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>
<?php //   echo $this->renderElement('liquidacion_intercambios/detalle',array('intercambio_id'=>$intercambioId,'plugin'=>'mutual'))?>

<script type="text/javascript">

Event.observe(window, 'load', function(){

	<?php //   if($disableForm == 1) echo "$('formReproArchivo').disable();";?>
	
});

</script>

<div class="areaDatoForm">
	
	<h3>CRITERIO DE FILTRADO DE INFORMACION</h3>
	<?php echo $frm->create(null,array('action'=>'reprocesar_archivo_general/'.$liquidacion['Liquidacion']['id'],'id' => 'formReproArchivo'))?>
	<table class="tbl_form">
		<tr>
			<td><strong>EMPRESAS / TURNOS INFORMADOS EN ARCHIVO NO COBRADOS</strong></td>
		</tr>
		<tr>
			<td>
				<?php //   echo $frm->input('LiquidacionSocio.filtro_empresa',array('type' => 'select', 'options' => $resumenSelect, 'selected' => (isset($this->data['LiquidacionSocio']['filtro_empresa']) ? $this->data['LiquidacionSocio']['filtro_empresa'] : "")))?>
				<?php echo $this->renderElement('liquidacion/turnos_diskette_form', array(
							'plugin' => 'mutual',
							'type' => 'grilla_ckeck',
							'model' => 'LiquidacionSocio.filtro_empresa',
							'empty' => false,
							'intercambioId' => $liquidacion['Liquidacion']['id'],
                            'method' => 'get_resumen_turnos_by_liquidacion_list',
							'selected' => (isset($this->data['LiquidacionSocio']['filtro_empresa']) ? $this->data['LiquidacionSocio']['filtro_empresa'] : "")		
				))?>
			</td>
		</tr>
		<tr><td><strong>INCLUIR CUOTA [Sin Seleccionar ninguna = incluir TODAS]</strong></td></tr>
		<tr>	
			<td>
			<?php echo $this->renderElement('global_datos/grilla_select_cuotas_puntuales',array(
																			'plugin'=>'config',
																			'label' => " ",
																			'model' => 'LiquidacionSocio.tipo_cuota',
																			'disabled' => false,
																			'empty' => true,
																			'selected' => $this->data['LiquidacionSocio']['tipo_cuota'],
			))?>			
			
			</td>
		</tr>
		
		<tr>
		
			<td>
			
				<table class="tbl_form">
				
					<tr>
						<td>CUOTAS NO COBRADAS DE</td>
						<td>
							<?php echo $this->renderElement('proveedor/combo_general',array(
																							'plugin'=>'proveedores',
																							'label' => '',		
																							'metodo' => "proveedores_liquidados_list/" . $liquidacion['Liquidacion']['id'].'/0',
																							'model' => 'LiquidacionSocio.proveedor_id',
																							'empty' => TRUE,
																							'selected' => (isset($this->data['LiquidacionSocio']['proveedor_id']) ? $this->data['LiquidacionSocio']['proveedor_id'] : "")
							))?>						
						</td>
					</tr>
					<tr>
						<td>CRITERIO DE DEUDA</td>
						<td><?php echo $frm->input('LiquidacionSocio.criterio_deuda', array('type' => 'select','options' => array(0 => 'TOTAL [ATRASO + PERIODO]', 1 => 'SOLO ' . $liquidacion['Liquidacion']['periodo_desc_amp'], 2 => 'SOLO PRIMER CUOTA'),'selected' => (isset($this->data['LiquidacionSocio']['criterio_deuda']) ? $this->data['LiquidacionSocio']['criterio_deuda'] : 0)))?></td>
					</tr>
					<tr>
						<td>IMPORTE INFERIOR A</td>
						<td><?php echo $frm->money('LiquidacionSocio.monto_corte','',(isset($this->data['LiquidacionSocio']['monto_corte']) ? $this->data['LiquidacionSocio']['monto_corte'] : 0))?>(0 = no aplica este filtro)</td>
					</tr>
					<tr>
						<td>GENERAR ARCHIVO PARA BANCO</td>
						<td>
							<?php echo $this->renderElement('banco/combo_global', array(
							
								'plugin' => 'config',
								'label' => 	'',
								'model' => 'LiquidacionSocio.banco_intercambio',
								'tipo' => 5,
								'empty' => false,
								'selected' => (isset($this->data['LiquidacionSocio']['banco_intercambio']) ? $this->data['LiquidacionSocio']['banco_intercambio'] : "")
							
							))?>						
						
						</td>
					</tr>
					<tr>
						<td>FECHA DEBITO</td>
						<td><?php echo $frm->input('LiquidacionSocio.fecha_debito',array('dateFormat' => 'DMY'))?></td>
					</tr>
					<tr>
						<td>FECHA PRESENTACION</td>
						<td><?php echo $frm->calendar('LiquidacionSocio.fecha_presentacion','',date('Y-m-d'),date("Y"),date("Y") + 1)?></td>
					</tr>                    
					<tr>
						<td>NUMERO DE ARCHIVO</td>
						<td><?php echo $frm->number('LiquidacionSocio.nro_archivo',array('maxlength' => 2,'size' => 2))?></td>
					</tr>					
				
				</table>
			
			</td>
		
		</tr>
		

	</table>
	<?php echo $frm->hidden('LiquidacionSocio.liquidacion_id', array('value' => $liquidacion['Liquidacion']['id']))?>
	<?php echo $frm->submit("PROCESAR DATOS PARA DISKETTE",array('id' => 'btn_genDiskette'))?>	

</div>
<?php if(!empty($datos) && !$resumenByTurno):?>

	<h3>RESULTADO DEL PROCESAMIENTO</h3>
	
	<table>
		<tr>
			<th>#</th>
			<th>SOCIO</th>
			<th>CALIFICACION</th>
			<th>REG</th>
			<th>SUCURSAL</th>
			<th>CUENTA</th>
			<th>CBU</th>
			<th>LIQUIDADO</th>
			<th>A DEBITAR</th>
			<th>STATUS</th>
			<th>OBSERVACIONES</th>
		</tr>
		<?php foreach($datos['info_procesada'] as $recno =>  $dato):?>
		
			<?php $apenom = $dato['LiquidacionSocio']['documento'] ." - <strong>" .$dato['LiquidacionSocio']['apenom']."</strong>";?>
		
			<tr class="<?php echo ($dato['LiquidacionSocio']['error'] == 0 ? "" : "grilla_error")?>">
				<td><?php echo $recno + 1?></td>
				<td><?php echo $this->renderElement('socios/link_to_liquidacion',array('plugin' => 'pfyj', 'texto' =>  $apenom,'socio_id' => $dato['LiquidacionSocio']['socio_id']))?></td>
				<td align="center"><?php echo $dato['LiquidacionSocio']['calificacion']?></td>
				<td align="center"><?php echo $dato['LiquidacionSocio']['registro']?></td>
				<td align="center"><?php echo $dato['LiquidacionSocio']['sucursal']?></td>
				<td align="right"><?php echo $dato['LiquidacionSocio']['nro_cta_bco']?></td>
				<td align="right"><?php echo $dato['LiquidacionSocio']['cbu']?></td>
				<td align="right"><?php echo $util->nf($dato['LiquidacionSocio']['importe_liquidado'])?></td>
				<td align="right"><?php echo $util->nf($dato['LiquidacionSocio']['importe_adebitar'])?></td>
				<td align="center"><?php echo $controles->errorIcon($dato['LiquidacionSocio']['error'])?></td>
				<td align="center"><?php echo $dato['LiquidacionSocio']['mensaje']?></td>
			</tr>
		
		<?php endforeach;?>
		<tr class="totales">
			<th colspan="7"><h5>TOTALES [ <span style="color: green;"> <?php echo $datos['totales']['registros_disk']?> REGISTROS OK </span>] <?php echo ($datos['totales']['errores'] != 0 ? " [ <span style='color:red;'>".$datos['totales']['errores']." REGISTROS CON ERROR ($ ". $util->nf($datos['totales']['importe_error']).") </span> ] ":"")?></h5></th>
			<th><h5><?php echo $util->nf($datos['totales']['liquidado'])?></h5></th>
			<th><h5><span style="color: green;"><?php echo $util->nf($datos['totales']['importe_disk'])?></span></h5></th>
			<th colspan="2"></th>
		</tr>
	</table>
	

	<?php echo $this->renderElement("banco/info_diskette",array('plugin' => 'config', 'uuid' => $diskette_uuid, 'listado' => '/mutual/liquidacion_socios/reprocesar_archivo_general/' . $liquidacion['Liquidacion']['id']))?>

<?php elseif(!empty($datos) && $resumenByTurno):?>

	<h3>DETALLE DE TURNOS PROCESADOS</h3>
	
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
		<?php foreach($datos['info_procesada_by_turno'] as $codigoTurno => $turno):?>
			<tr>
				<td colspan="8"><h3><?php echo $turno['descripcion']?></h3></td>
			</tr>		
			<?php $reg = $ACU_IMPO = $ACU_IMPODTO = $ACU_REGISTROS = 0;?>
			<?php foreach($turno['registros'] as $socio):?>
			
				<?php $apenom = $socio['LiquidacionSocio']['documento'] ." - <strong>" .$socio['LiquidacionSocio']['apenom']."</strong>";?>
				<?php $ACU_IMPO += $socio['LiquidacionSocio']['importe_adebitar'];?>
				<?php $ACU_IMPODTO += $socio['LiquidacionSocio']['importe_dto'];?>
				<?php $ACU_REGISTROS++;?>
				<?php $reg++;?>
				
				<tr class="<?php echo ($socio['LiquidacionSocio']['error_cbu'] == 1 ? "" : "")?>" id="TRL_<?php echo $i?>">
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
		<?php endforeach;?>
	</table>
	<?php echo $this->renderElement("banco/info_diskette",array('plugin' => 'config', 'uuid' => $diskette_uuid, 'listado' => '/mutual/liquidacion_socios/reprocesar_archivo_general/' . $liquidacion['Liquidacion']['id']))?>
	<?php //   debug($datos)?>

<?php elseif($disableForm == 1):?>	

	<div class="notices">*** NO EXISTEN CUOTAS SIN PRE-IMPUTAR PARA EL CRITERIO DE FILTRADO ELEGIDO ***</div>
	

<?php endif;?>


<?php //   debug($datos['totales'])?>
<?php //   debug($datos['diskette'])?>