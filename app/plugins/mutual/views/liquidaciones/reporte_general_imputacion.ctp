<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: REPORTE GENERAL IMPUTACION'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('reporte_general_imputacion').disable();
	<?php endif;?>
});
</script>
<div class="areaDatoForm">

	<?php echo $frm->create(null,array('action' => 'reporte_general_imputacion','id' => 'reporte_general_imputacion'))?>
	
		<table class="tbl_form">
			<tr>
				<td>ORGANISMO</td>
				<td>
				<?php echo $this->renderElement('global_datos/grilla_checks',array(
																				'plugin'=>'config',
																				'label' => " ",
																				'model' => 'Liquidacion.codigo_organismo',
																				'prefijo' => 'MUTUCORG',
																				'disabled' => false,
																				'header' => true,
																				'metodo' => "get_organismos",
																				'selected' => (isset($this->data['Liquidacion']['codigo_organismo']) ? $this->data['Liquidacion']['codigo_organismo'] : "")	
				))?>				
				</td>
			</tr>		
			<tr>
				<td>PROVEEDOR</td>
				<td>
				<?php echo $this->renderElement('proveedor/grilla_checks',array(
																				'plugin'=>'proveedores',
																				'metodo' => "proveedores_list/1",
																				'model' => 'Liquidacion.proveedor_id',
																				'empty' => true,
																				'selected' => (isset($this->data['Proveedor']['proveedor_id']) ? $this->data['Proveedor']['proveedor_id'] : "0")
				))?>			
				</td>				
			</tr>		
			<tr>
				<td>PERIODO DESDE</td><td><?php echo $frm->input('periodo_desde',array('type' => 'select', 'options' => $periodos_desde))?></td>
			</tr>
			<tr>
				<td>PERIODO HASTA</td><td><?php echo $frm->input('periodo_hasta',array('type' => 'select', 'options' => $periodos_hasta))?></td>
			</tr>
			<tr>
				<td>TIPO DE INFORME</td><td><?php echo $frm->input('tipo_informe',array('type' => 'select', 'options' => $optionsTipoInforme));?></td>
			</tr>
			<tr><td colspan="2"><?php echo $frm->submit("GENERAR LISTADO XLS")?></td></tr>		
		</table>
	
	<?php echo $frm->end()?>

</div>
<?php if($show_asincrono == 1):?>
	
	
	<?php
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'reporte_acumulado_proveedor_control_imputacion_xls',
											'accion' => '.mutual.liquidaciones.reporte_general_imputacion.'.$excel_params['file_name'],
											'target' => '_blank',
											'btn_label' => 'Ver Listado',
											'titulo' => "REPORTE GENERAL DE LIQUIDACION",
											'subtitulo' => $util->periodo($periodo_desde) . ' hasta ' . $util->periodo($periodo_hasta) . " - " . $tipo_informe_desc,
											'p1' => $periodo_desde,
											'p2' => $periodo_hasta,
											
											'p4' => $proveedor_id,
											'p5' => $tipo_informe,
                                            'p6' => $excel_params['file_name'],
                                            'txt1' => base64_encode(serialize($excel_params)),
                                            'txt2' => $codigo_organismo,
	));
//	debug($codigo_organismo);
	?>
<?php endif?>