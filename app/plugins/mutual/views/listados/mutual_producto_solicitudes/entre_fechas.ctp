<?php echo $this->renderElement('head',array('title' => 'LISTADOS','plugin' => 'config'))?>
<?php echo $this->renderElement('listados/menu_listados',array('plugin' => 'mutual'))?>
<h3>LISTADO DE ORDENES CONSUMO ENTRE FECHAS</h3>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('form_consumos_por_fecha').disable();
	<?php endif;?>
});
</script>
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' => 'consumos_por_fecha','id' => 'form_consumos_por_fecha'))?>
	<table class="tbl_form">
		<tr>
			<td>PRODUCTO</td><td><?php echo $this->renderElement("mutual_productos/combo_productos",array('plugin' => 'mutual','empty' => true,'selected' => $optionList,'model' => "ListadoService"))?></td>
		</tr>
		<tr>
			<td>ORGANISMO</td>
			<td>
			<?php echo $this->renderElement('global_datos/combo_global',array(
																			'plugin'=>'config',
																			'metodo' => "get_organismos",
																			'model' => 'ListadoService.codigo_organismo',
																			'empty' => true,
                                                                            'selected' => $this->data['ListadoService']['codigo_organismo'],
			))?>			
			</td>			
		</tr>         
		<tr>
			<td>PROVEEDOR</td>
			<td>
			<?php echo $this->renderElement('proveedor/combo_general',array(
																			'plugin'=>'proveedores',
																			'metodo' => "proveedores_list/1",
																			'model' => 'ListadoService.proveedor_id',
																			'empty' => true,
                                                                            'selected' => $this->data['ListadoService']['proveedor_id'],
			))?>			
			</td>
		</tr>        
		<tr>
			<td>DESDE FECHA</td><td><?php echo $frm->calendar('ListadoService.fecha_desde','',$fecha_desde,'1990',date("Y"))?></td>
		</tr>
		<tr>
			<td>HASTA FECHA</td><td><?php echo $frm->calendar('ListadoService.fecha_hasta','',$fecha_hasta,'1990',date("Y"))?></td>
		</tr>
		<tr>
			<td>PERIODO DE CORTE A</td><td><?php echo $frm->periodo('MutualProductoSolicitud.periodo_corte','',(isset($fecha_corte) ? $fecha_corte :  null),date('Y')-1,date('Y')+5,false)?></td>
		</tr>                  
		<tr>
			<td>FORMATO</td><td><?php echo $frm->tipoReporte($this->data['ListadoService']['tipo_reporte'])?></td>
		</tr>		
		<tr><td colspan="2"><?php echo $frm->submit("GENERAR LISTADO")?></td></tr>
	</table>
	<?php echo $frm->end()?>
</div>
<?php if($show_asincrono == 1):?>
	<?php 
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'listado_consumos_entre_fechas',
											'accion' => '.mutual.listados.consumos_por_fecha.'. $tipoReporte,
											'target' => '_blank',
											'btn_label' => 'Ver Listado',
											'titulo' => "LISTADO DE ORDENES DE CONSUMOS / SERVICIOS EMITIDAS ($tipoReporte)",
											'subtitulo' => $selectedStr . ' | Entre el '.$util->armaFecha($fecha_desde).' y el '.$util->armaFecha($fecha_hasta),
											'p1' => $fecha_desde,
											'p2' => $fecha_hasta,
											'p3' => $optionList,
                                            'p4' => $proveedorId,
                                            'p5' => $codigoOrganismo,
                                            'p6' => $periodo_corte,
	));
	
	?>
<?php endif?>