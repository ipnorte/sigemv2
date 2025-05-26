<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: REPORTE CONTROL VENCIMIENTOS'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('reporte_control_vtos').disable();
	<?php endif;?>
});
</script>
<div class="areaDatoForm">

	<?php echo $frm->create(null,array('action' => 'reporte_control_vtos','id' => 'reporte_control_vtos'))?>
	
		<table class="tbl_form">
			<tr>
				<td>ORGANISMO</td>
				<td>
				<?php echo $this->renderElement('global_datos/combo_global',array(
																				'plugin'=>'config',
																				'label' => " ",
																				'model' => 'Liquidacion.codigo_organismo',
																				'prefijo' => 'MUTUCORG',
																				'disabled' => false,
																				'empty' => true,
																				'metodo' => "get_organismos",
																				'selected' => (isset($this->data['Liquidacion']['codigo_organismo']) ? $this->data['Liquidacion']['codigo_organismo'] : "")	
				))?>				
				</td>
			</tr>		
			<tr>
				<td>PROVEEDOR</td>
				<td>
				<?php echo $this->renderElement('proveedor/combo_general',array(
																				'plugin'=>'proveedores',
																				'metodo' => "proveedores_list/1",
																				'model' => 'Liquidacion.proveedor_id',
																				'empty' => true,
																				'selected' => (isset($this->data['Liquidacion']['proveedor_id']) ? $this->data['Liquidacion']['proveedor_id'] : "")
				))?>			
				</td>				
			</tr>
		<tr>
			<td>PRODUCTO</td>
			<td>
		<?php echo $this->renderElement('global_datos/combo_global',array(
																		'plugin'=>'config',
																		'label' => " ",
																		'model' => 'Liquidacion.tipo_producto',
																		'prefijo' => 'MUTUPROD',
																		'disabled' => false,
																		'empty' => true,
																		'metodo' => "get_tipo_productos_consumos",
																		'selected' => (isset($this->data['Liquidacion']['tipo_producto']) ? $this->data['Liquidacion']['tipo_producto'] : "")	
		))?>				
			</td>
		</tr>
        <tr>
            <td>CUOTA</td>
            <td>
		<?php echo $this->renderElement('global_datos/combo_global',array(
																		'plugin'=>'config',
																		'label' => " ",
																		'model' => 'Liquidacion.tipo_cuota',
																		'prefijo' => 'MUTUTCUO',
																		'disabled' => false,
																		'empty' => true,
																		'metodo' => "get_tipo_cuotas",
																		'selected' => (isset($this->data['Liquidacion']['tipo_cuota']) ? $this->data['Liquidacion']['tipo_cuota'] : "")	
		))?>                
            </td>
        </tr>
			<tr>
				<td>PERIODO CONTROL</td><td><?php echo $frm->periodo('Liquidacion.periodo_control','',$periodo_ctrl,date('Y') - 10,date('Y') + 1)?></td>
			</tr>
			<tr>
				<td>A VENCER</td>
				<td><input type="checkbox" name="data[Liquidacion][a_vencer]" value="1" <?php echo (isset($this->data['Liquidacion']['a_vencer']) ? "checked = 'ckecked'" : "")?> /></td>
			</tr>
			<tr><td colspan="2"><?php echo $frm->submit("GENERAR LISTADO XLS")?></td></tr>		
		</table>
	
	<?php echo $frm->end()?>

</div>
<?php if($show_asincrono == 1):?>
	
	
	<?php
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'reporte_control_vencimientos_xls',
											'accion' => '.mutual.liquidaciones.reporte_control_vtos',
											'target' => '_blank',
											'btn_label' => 'Ver Listado',
											'titulo' => "REPORTE CONTROL VENCIMIENTOS",
											'subtitulo' => $util->periodo($periodo_ctrl) ." | ". $tipo_informe_desc,
											'p1' => $periodo_ctrl,
											'p2' => $codigo_organismo,
											'p3' => $proveedor_id,
											'p4' => $a_vencer,
                                            'p5' => $tipoProducto,
                                            'p6' => $tipoCuota,    
	));
	
	?>
<?php endif?>