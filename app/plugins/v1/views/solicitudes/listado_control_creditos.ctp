<?php echo $this->renderElement('solicitudes/menu_listados',array('plugin' => 'v1'))?>


<h3>CONTROL DE CREDITOS</h3>

<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($show_asincrono == 1):?>
		$('form_control_creditos').disable();
	<?php endif;?>

	getComboEmpresas();

	$('CreditoCodigoOrganismo').observe('change',function(){
		getComboEmpresas();
	});

	<?php echo (isset($this->data['Credito']['todo']) ? "$('trCuotasAdeudadas').hide();" : "")?>
		
});

function getComboEmpresas(){
	organismo = $('CreditoCodigoOrganismo').getValue();
	if(organismo != ""){
		selected = '<?php echo (isset($this->data['Credito']['codigo_empresa']) ? $this->data['Credito']['codigo_empresa'] : "0")?>';
		new Ajax.Updater('CreditoCodigoEmpresa','<?php echo $this->base?>/config/global_datos/combo_empresas_ajax/'+ organismo + '/' + selected + '/1/1', {asynchronous:true, evalScripts:true, onComplete:function(request, json) {$('spinner').hide();$('btn_submit').enable();},onLoading:function(request) {Element.show('spinner');$('btn_submit').disable();}, requestHeaders:['X-Update', 'CreditoCodigoEmpresa']});
	}
}

</script>

<?php echo $frm->create(null,array('action' => 'listados/3','id' => 'form_control_creditos'))?>
<div class="areaDatoForm">

	<table class="tbl_form">
		<tr>
			<td>ORGANISMO</td>
			<td>
			<?php echo $this->renderElement('global_datos/combo_global',array(
																			'plugin'=>'config',
																			'label' => " ",
																			'model' => 'Credito.codigo_organismo',
																			'prefijo' => 'MUTUCORG',
																			'disabled' => false,
																			'empty' => true,
																			'metodo' => "get_organismos",
																			'selected' => (isset($this->data['Credito']['codigo_organismo']) ? $this->data['Credito']['codigo_organismo'] : "")	
			))?>				
			</td>
		</tr>		
		<tr id="cmbEmpresa">
			<td>EMPRESA - TURNO</td><td>
				<select name="data[Credito][codigo_empresa]" id="CreditoCodigoEmpresa" >
				</select>
				<div id="spinner" style="display: none; float: left;color:red;font-size:xx-small;">
				<?php echo $html->image('controles/ajax-loader.gif'); ?>
				</div>				
			</td>
		</tr>	
		<tr>
			<td>PROVEEDOR</td>
			<td><?php echo $this->renderElement('proveedor/combo_general',array('plugin' => 'proveedores','model' => 'Credito.proveedor_id','metodo' => 'proveedores/0/1/1/1/1','empty' => false, 'selected' => $this->data['Credito']['proveedor_id']))?></td>
		</tr>
		<tr>	
			<td>PERIODO DE CORTE</td>
			<td><?php echo $frm->input("Credito.periodo_liquidado",array('type' => 'select','options' => $periodosLiquidados))?></td>
		</tr>
		<tr id="trCuotasAdeudadas">	
			<td>CUOTAS ADEUDADAS</td>
			<td><?php echo $frm->number("Credito.cuotas",array('value' => (isset($this->data['Credito']['cuotas']) ? $this->data['Credito']['cuotas'] : 0), 'maxlength' => 3, 'size' => 3))?></td>
		</tr>
		<tr>
			<td>NO FILTRAR CUOTAS</td><td><input type="checkbox" name="data[Credito][todo]" onclick="$('trCuotasAdeudadas').toggle();" value="1" <?php echo (isset($this->data['Credito']['todo']) ? "checked" : "")?>/></td>
		</tr>		
		
		<tr><td colspan="2"><?php echo $frm->submit("GENERAR LISTADO")?></td></tr>
	
	</table>
	
</div>
<?php echo $frm->end()?>

<?php if($show_asincrono == 1):?>

	<?php 
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'solicitudes_listado_control_creditos',
											'accion' => '.v1.solicitudes.listados.3.XLS',
											'target' => '_blank',
											'btn_label' => 'Ver Listado',
											'titulo' => "CONTROL CREDITOS",
											'subtitulo' => 'PERIODO DE CORTE: '.$util->periodo($this->data['Credito']['periodo_liquidado'],true,'-'),
											'p1' => $this->data['Credito']['proveedor_id'],
											'p2' => $this->data['Credito']['periodo_liquidado'],
											'p3' => (empty($this->data['Credito']['cuotas']) ? 0 : $this->data['Credito']['cuotas']),
											'p4' => (empty($this->data['Credito']['codigo_organismo']) ? null : $this->data['Credito']['codigo_organismo']),
											'p5' => (empty($this->data['Credito']['codigo_empresa']) ? null : $this->data['Credito']['codigo_empresa']),
											'p6' => (empty($this->data['Credito']['todo']) ? 0 : $this->data['Credito']['todo']),
	));
	
	?>


<?php endif;?>
