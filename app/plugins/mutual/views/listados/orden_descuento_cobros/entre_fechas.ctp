<?php echo $this->renderElement('head',array('title' => 'LISTADOS','plugin' => 'config'))?>
<?php echo $this->renderElement('listados/menu_listados',array('plugin' => 'mutual'))?>
<h3>LISTADO DE COBROS EMITIDOS</h3>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('form_cobros_por_fecha').disable();
	<?php endif;?>
});
</script>
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' => 'cobros_por_fecha','id' => 'form_cobros_por_fecha'))?>
	<table class="tbl_form">
		<tr>
			<td>ORGANISMO</td>
			<td>
			<?php echo $this->renderElement('global_datos/combo_global',array(
                                'plugin'=>'config',
                                'metodo' => "get_organismos",
                                'model' => 'ListadoService.codigo_organismo',
                                'empty' => true,
                                'selected' => (isset($codigo_organismo) ? $codigo_organismo : "")
			))?>			
			</td>			
		</tr>            
		<tr>
		
			<td>TIPO COBRO</td>
			<td>
			<?php echo $this->renderElement('global_datos/combo_global',array(
                                'plugin'=>'config',
                                'label' => " ",
                                'model' => 'ListadoService.tipo_cobro',
                                'prefijo' => 'MUTUTCOB',
                                'disabled' => false,
                                'empty' => true,
                                'metodo' => "get_todos_tipos_cobro_caja",
                                'selected' => (isset($tipo_cobro) ? $tipo_cobro : "")	
			))?>							
			</td>
		</tr>
			<td>PROVEEDOR</td>
			<td>
			<?php echo $this->renderElement('proveedor/combo_general',array(
                                'plugin'=>'proveedores',
                                'metodo' => "proveedores_list",
                                'model' => 'ListadoService.proveedor_id',
                                'empty' => true,
                                'selected' => (isset($proveedor_id) ? $proveedor_id : "")
			))?>			
			</td>
		</tr>		

		<tr>
			<td>DESDE FECHA</td><td><?php echo $frm->calendar('ListadoService.fecha_desde','',$fecha_desde,'1990',date("Y"))?></td>
		</tr>
		<tr>
			<td>HASTA FECHA</td><td><?php echo $frm->calendar('ListadoService.fecha_hasta','',$fecha_hasta,'1990',date("Y"))?></td>
		</tr>
		<!--		
		<tr>
			<td>FORMATO</td><td><?php echo $frm->input('ListadoService.tipo_salida',array('type' => 'select', 'options' => array('PDF' => 'PDF','XLS' => 'XLS'), 'label' => null,'empty' => false, 'selected' => (isset($this->data['ListadoService']['tipo_salida']) ? $this->data['ListadoService']['tipo_salida'] : 'PDF')))?></td>
		</tr>		
		-->
		<tr><td colspan="2"><?php echo $frm->submit("GENERAR LISTADO")?></td></tr>
		
	</table>
	<?php echo $frm->end()?>
</div>
<?php if($show_asincrono == 1):?>
	
	
	<?php
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'listado_cobros',
											'accion' => '.mutual.listados.cobros_por_fecha.'.$tipo_salida,
											'target' => '_blank',
											'btn_label' => 'Ver Listado',
											'titulo' => "LISTADO DE COBROS",
											'subtitulo' => 'Listado de Cobros entre Fechas',
											'p1' => $tipo_cobro,
											'p2' => $fecha_desde,
											'p3' => $fecha_hasta,
											'p4' => $proveedor_id,	
                                                                                        'p5' => $codigo_organismo,    
	));
	
	?>
<?php endif?>