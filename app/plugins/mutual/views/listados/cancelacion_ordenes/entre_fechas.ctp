<?php echo $this->renderElement('head',array('title' => 'LISTADOS','plugin' => 'config'))?>
<?php echo $this->renderElement('listados/menu_listados',array('plugin' => 'mutual'))?>
<h3>LISTADO DE CANCELACIONES ENTRE FECHAS</h3>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('form_cancelaciones_por_fecha').disable();
	<?php endif;?>
});
</script>
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' => 'cancelaciones_por_fecha','id' => 'form_cancelaciones_por_fecha'))?>
	<table class="tbl_form">
		<tr>
			<tr>
				<td>PROVEEDOR</td>
				<td><?php echo $frm->input('ListadoService.proveedor_id',array('type' => 'select','options'=> $proveedores,'selected' => (isset($this->data['ListadoService']['proveedor_id']) ? $this->data['ListadoService']['proveedor_id'] : ""),'empty' => true))?></td>
			</tr>
			<td>TIPO CANCELACION</td>
			<td>
			<?php echo $this->renderElement('global_datos/combo',array(
																			'plugin'=>'config',
																			'label' => " ",
																			'model' => 'ListadoService.forma_cancelacion',
																			'prefijo' => 'MUTUTICA',
																			'disable' => false,
																			'empty' => true,
																			'selected' => (!empty($this->data['ListadoService']['forma_cancelacion']) ? $this->data['ListadoService']['forma_cancelacion'] : '0'),
																			'logico' => true,
			))?>			
			</td>
		</tr>	
		<tr>
			<td>CRITERIO</td><td><?php echo $frm->input('ListadoService.criterio_fecha',array('type' => 'select','options'=> $criterios,'selected' => $criterio_fecha))?></td>
		</tr>
		<tr>
			<td>DESDE FECHA</td><td><?php echo $frm->calendar('ListadoService.fecha_desde','',$fecha_desde,'1990',date("Y"))?></td>
		</tr>
		<tr>
			<td>HASTA FECHA</td><td><?php echo $frm->calendar('ListadoService.fecha_hasta','',$fecha_hasta,'1990',date("Y"))?></td>
		</tr>
		<tr>
			<td>FORMATO</td>
			<td><?php echo $frm->tipoReporte($this->data['ListadoService']['tipo_reporte'])?></td>
		</tr>
		
		<tr><td colspan="2"><?php echo $frm->submit("GENERAR LISTADO")?></td></tr>
	</table>
	<?php echo $frm->end()?>
</div>
<?php if($show_asincrono == 1):?>
	<?php 
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'listado_cancelaciones_entre_fechas',
											'accion' => '.mutual.listados.cancelaciones_por_fecha.'.$tipo_reporte,
											'target' => '_blank',
											'btn_label' => 'Ver Listado',
											'titulo' => "LISTADO DE ORDENES DE CANCELACION EMITIDAS",
											'subtitulo' => 'Entre el '.$util->armaFecha($fecha_desde).' y el '.$util->armaFecha($fecha_hasta) .' - '.$criterio_desc .' - ' . $forma_cancelacion_desc,
											'p1' => $fecha_desde,
											'p2' => $fecha_hasta,
											'p3' => $criterio_fecha,
											'p4' => $this->data['ListadoService']['forma_cancelacion'],
											'p5' => $this->data['ListadoService']['proveedor_id']		
	));
	
	?>
<?php endif?>