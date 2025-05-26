<?php echo $this->renderElement('head',array('title' => 'LISTADOS','plugin' => 'config'))?>
<?php echo $this->renderElement('listados/menu_listados',array('plugin' => 'mutual'))?>
<h3>LISTADO DE REINTEGROS</h3>

<script type="text/javascript">
Event.observe(window, 'load', function(){

//	$('rwConsolidaSocio').hide();
//	$('ListadoServiceConsolidado').disable();
	
	<?php if(substr($this->data['ListadoService']['codigo_organismo'],8,2) != 22):?>
	$('rwCodEmpre').hide();
	$('ListadoServiceCodigoEmpresa').disable();
	<?php endif;?>
	$('ListadoServiceCodigoOrganismo').observe('change',function(){
		organismo = $('ListadoServiceCodigoOrganismo').getValue();
		organismo = organismo.substr(8,2);
		if(organismo === 22){
			$('rwCodEmpre').show();
			$('ListadoServiceCodigoEmpresa').enable();
		}else{
			$('rwCodEmpre').hide();
			$('ListadoServiceCodigoEmpresa').disable();
		}
	});

//	$('ListadoServicePeriodoDesde').observe('change',function(){
//		desde = $('ListadoServicePeriodoDesde').getValue();
//		hasta = $('ListadoServicePeriodoHasta').getValue();
//
//		if(desde != hasta){
//			$('rwConsolidaSocio').show();
//			$('ListadoServiceConsolidado').enable();
//		}else{
//			$('rwConsolidaSocio').hide();
//			$('ListadoServiceConsolidado').disable();
//		}
//		
//	});

	
	<?php if($showAsincrono == 1):?>
	$('form_listado_reintegros').disable();
	<?php endif;?>
	
});
</script>

<div class="areaDatoForm">

	<?php echo $frm->create(null,array('action' => 'listado_reintegros','id' => 'form_listado_reintegros'))?>
	<table class="tbl_form">
		<tr>
			<td>PERIODO DESDE</td>
			<td><?php echo $frm->input('ListadoService.periodo_desde',array('type' => 'select','options'=> $periodos,'selected' => (isset($this->data['ListadoService']['periodo_desde']) ? $this->data['ListadoService']['periodo_desde'] : ""),'empty' => false))?></td>
			<td>PERIODO HASTA</td>
			<td><?php echo $frm->input('ListadoService.periodo_hasta',array('type' => 'select','options'=> $periodos,'selected' => (isset($this->data['ListadoService']['periodo_hasta']) ? $this->data['ListadoService']['periodo_hasta'] : ""),'empty' => false))?></td>
		</tr>
		<tr>
			<td>ORGANISMO</td>
			<td colspan="3">
			<?php echo $this->renderElement('global_datos/combo_global',array(
																			'plugin'=>'config',
																			'metodo' => "get_organismos",
																			'model' => 'ListadoService.codigo_organismo',
																			'empty' => true,
																			'selected' => (isset($this->data['ListadoService']['codigo_organismo']) ? $this->data['ListadoService']['codigo_organismo'] : "")	
			))?>						
			</td>
		</tr>
		<tr id="rwCodEmpre">
			<td>EMPRESA</td>
			<td colspan="3">
			<?php echo $this->renderElement('global_datos/combo_global',array(
																			'plugin'=>'config',
																			'metodo' => "get_empresas",
																			'model' => 'ListadoService.codigo_empresa',
																			'empty' => TRUE,
																			'label' => '',
																			'selected' => (isset($this->data['ListadoService']['codigo_empresa']) ? $this->data['ListadoService']['codigo_empresa'] : "")
			))?>			
			</td>		
		</tr>
		<tr id="rwConsolidaSocio">
			<td>CONSOLIDADO</td>
			<td colspan="3"><input type="checkbox" name="data[ListadoService][consolidado]" value="1" id="ListadoServiceConsolidado"  <?php echo ($consolidado == 1 ? "checked=\"checked\"" : "")?>/></td>
		</tr>		
		<tr><td colspan="4"><?php echo $frm->submit("GENERAR LISTADO")?></td></tr>	
	</table>
	<?php echo $frm->end()?>

</div>
<?php if($showAsincrono == 1):?>

	<?php 
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'listado_reintegros',
											'accion' => '.mutual.listados.listado_reintegros.'.$tipo_reporte,
											'target' => '_blank',
											'btn_label' => 'Ver Listado',
											'titulo' => "LISTADO DE REINTEGROS",
											'subtitulo' => 'Desde '.$util->periodo($periodoDesde).' Hasta '.$util->periodo($periodoHasta) . (!empty($codigoOrganismo) ? " | " . $util->globalDato($codigoOrganismo) : "") . (!empty($codigoEmpresa) ? " | " . $util->globalDato($codigoEmpresa) : "") . ($consolidado == 1 ? " | CONSOLIDADO" : ""),
											'p1' => $periodoDesde,
											'p2' => $periodoHasta,
											'p3' => $codigoOrganismo,
											'p4' => $codigoEmpresa,
											'p5' => $consolidado,
	));
	
	?>

<?php endif;?>