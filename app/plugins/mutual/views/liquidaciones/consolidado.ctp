<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: POSICION CONSOLIDADA'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('consolidado').disable();
	<?php endif;?>
//	$('cmbEmpresa').hide();
	getComboEmpresas();
	getComboPeriodos();

	$('LiquidacionCodigoOrganismo').observe('change',function(){
		getComboEmpresas();
		getComboPeriodos();		
	});
	
});
function getComboEmpresas(){
	organismo = $('LiquidacionCodigoOrganismo').getValue();
	selected = '<?php echo (isset($this->data['Liquidacion']['codigo_empresa']) ? $this->data['Liquidacion']['codigo_empresa'] : "0")?>';
	new Ajax.Updater('LiquidacionCodigoEmpresa','<?php echo $this->base?>/config/global_datos/combo_empresas_ajax/'+ organismo + '/' + selected + '/1', {asynchronous:true, evalScripts:true, onComplete:function(request, json) {$('spinner').hide();$('btn_submit').enable();},onLoading:function(request) {Element.show('spinner');$('btn_submit').disable();}, requestHeaders:['X-Update', 'LiquidacionCodigoEmpresa']});
}
function getComboPeriodos(){
	organismo = $('LiquidacionCodigoOrganismo').getValue();
	selected = '<?php echo (isset($this->data['Liquidacion']['periodo_control']) ? $this->data['Liquidacion']['periodo_control'] : "0")?>';
	new Ajax.Updater('LiquidacionPeriodoControl','<?php echo $this->base?>/mutual/liquidaciones/combo_periodos_ajax/'+ organismo + '/' + selected + '/0', {asynchronous:true, evalScripts:true, onComplete:function(request, json) {$('spinner').hide();$('btn_submit').enable();},onLoading:function(request) {Element.show('spinner');$('btn_submit').disable();}, requestHeaders:['X-Update', 'LiquidacionPeriodoControl']});
}

</script>
<div class="areaDatoForm">

	<?php echo $frm->create(null,array('action' => 'consolidado','id' => 'consolidado'))?>
	
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
																				'empty' => FALSE,
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
				<td>PERIODO IMPUTADO</td>
				<td>
					<select name="data[Liquidacion][periodo_control]" id="LiquidacionPeriodoControl" >
					</select>
					<div id="spinner" style="display: none; float: left;color:red;font-size:xx-small;">
					<?php echo $html->image('controles/ajax-loader.gif'); ?>
					</div>				
					<?php //   echo $frm->input('Liquidacion.periodo_control',array('type' => 'select', 'options' => $periodosCorte))?>
				</td>
			</tr>

			<tr id="cmbEmpresa">
				<td>EMPRESA</td><td>
					<select name="data[Liquidacion][codigo_empresa]" id="LiquidacionCodigoEmpresa" >
					</select>
					<div id="spinner" style="display: none; float: left;color:red;font-size:xx-small;">
					<?php echo $html->image('controles/ajax-loader.gif'); ?>
					</div>				
				</td>
			</tr>
			<tr>
				<td>DETALLADO</td><td><input type="checkbox" name="data[Liquidacion][detallado]" value="1" <?php echo ($detallado == 1 ? "checked" : "")?>/></td>
			</tr>			
			<tr><td colspan="2"><?php echo $frm->submit("GENERAR LISTADO XLS")?></td></tr>		
		</table>
	
	<?php echo $frm->end()?>

</div>
<?php if($show_asincrono == 1):?>
	
	
	<?php
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'listado_posicion_consolidada',
											'accion' => '.mutual.liquidaciones.consolidado',
											'target' => '_blank',
											'btn_label' => 'Ver Listado',
											'titulo' => "POSICION CONSOLIDADA",
											'subtitulo' => $util->periodo($periodo_ctrl) ." | ". $tipo_informe_desc,
											'p1' => $periodo_ctrl,
											'p2' => $codigo_organismo,
											'p3' => $proveedor_id,
											'p4' => $codigo_empresa,
											'p5' => $detallado,
	));
	
	?>
<?php endif?>