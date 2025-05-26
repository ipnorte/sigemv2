<?php echo $this->renderElement('head',array('title' => 'ADMINISTRACION DE SERVICIOS','plugin' => 'config'))?>

<?php echo $this->renderElement("proveedor/datos_proveedor",array('plugin' => 'proveedores', 'proveedor_id' => $servicio['MutualServicio']['proveedor_id']))?>

<h3><?php echo $util->globalDato($servicio['MutualServicio']['tipo_producto'])?> :: MODIFICAR VALOR DE TARIFA</h3>

<script type="text/javascript">

function validateForm(){

	var impoAdicional = new Number($("MutualServicioValorImporteAdicional").getValue());
	if(impoAdicional == 0){
		alert("EL IMPORTE DE LA CUOTA MENSUAL PARA EL ADICIONAL NO PUEDE SER CERO!");
		$("MutualServicioValorImporteAdicional").focus();
		return false;
	}
	return true;
	
}

</script>


<?php echo $frm->create(null,array('action' => 'valores_add/'.$servicio['MutualServicio']['id'],'id' => 'NuevaTarifaServicio','onsubmit' => "return validateForm();"))?>

<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>ORGANISMO</td>
			<td>
			<?php echo $this->renderElement('global_datos/combo_global',array(
																			'plugin'=>'config',
																			'label' => " ",
																			'model' => 'MutualServicioValor.codigo_organismo',
																			'prefijo' => 'MUTUCORG',
																			'disabled' => false,
																			'empty' => false,
																			'metodo' => "get_organismos",
																			'selected' => (isset($this->data['MutualServicioValor']['codigo_organismo']) ? $this->data['MutualServicioValor']['codigo_organismo'] : "")	
			))?>				
			</td>
		</tr>
		<tr>
			<td>IMPORTE CUOTA TITULAR</td><td><?php echo $frm->money('MutualServicioValor.importe_titular',null,"0.00")?></td>
		</tr>	
		<tr>
			<td>IMPORTE CUOTA ADICIONAL</td><td><?php echo $frm->money('MutualServicioValor.importe_adicional',null,"0.00")?></td>
		</tr>
		<tr>
			<td>COSTO CUOTA TITULAR</td><td><?php echo $frm->money('MutualServicioValor.costo_titular',null,"0.00")?></td>
		</tr>	
		<tr>
			<td>COSTO CUOTA ADICIONAL</td><td><?php echo $frm->money('MutualServicioValor.costo_adicional',null,"0.00")?></td>
		</tr>
		<tr>
			<td>APLICAR A PARTIR DE</td><td><?php echo $frm->periodo('MutualServicioValor.periodo_vigencia',null,null,date('Y')-1,date('Y')+1)?></td>
		</tr>			
	</table>

</div>
<?php echo $frm->hidden('MutualServicioValor.mutual_servicio_id',array('value' => $servicio['MutualServicio']['id'])); ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/mutual/mutual_servicios/valores/'.$servicio['MutualServicio']['id']))?>
