<?php echo $this->renderElement('vendedores/menu_padron',array('vendedor' => $vendedor))?>
<h3>MODIFICAR PLAN</h3>
<script type="text/javascript">
function validComision(){
	var num = parseFloat($('VendedorProveedorPlanComision').getValue());
	$('VendedorProveedorPlanComision').removeClassName(classFormError);
	$(contenedorMsgError).update('');
	$(contenedorMsgError).hide();
	if(!/^\d+\.?\d*$/.test(num) || num == 0){
		$(contenedorMsgError).update('Debe Indicar el porcentaje de comision');
		$(contenedorMsgError).show();
		$('VendedorProveedorPlanComision').addClassName(classFormError);
		$('VendedorProveedorPlanComision').focus();
		return false;
	}else{
		return true;
	}

}
</script>
<div class="areaDatoForm">
<?php echo $frm->create(null,array('action' => 'modificar_plan/' . $comision['VendedorProveedorPlan']['id'], 'onsubmit' => 'return validComision()'))?>
<table class="tbl_form">
	<tr>
		<td>PLAN</td>
		<td>
			<select name="data[VendedorProveedorPlan][proveedor_plan_id]" disabled="disabled">
				<?php foreach($planes as $plan):?>
				<option value="<?php echo $plan['ProveedorPlan']['id']?>" <?php echo ($plan['ProveedorPlan']['id'] == $comision['VendedorProveedorPlan']['proveedor_plan_id'] ? 'selected="selected"' : '')?>><?php echo $plan['ProveedorPlan']['cadena']?></option>
				<?php endforeach;?>
			</select>
		</td>
	</tr>
	<tr>
		<td>VENTA MAYOR A</td><td><?php echo $frm->money('VendedorProveedorPlan.monto_venta',null,$comision['VendedorProveedorPlan']['monto_venta'])?></td>
	</tr>
	<tr>
		<td>COMISION (%)</td><td><?php echo $frm->money('VendedorProveedorPlan.comision',null,$comision['VendedorProveedorPlan']['comision'])?></td>
	</tr>
	
</table>
<?php echo $frm->hidden('VendedorProveedorPlan.id',array('value' => $comision['VendedorProveedorPlan']['id']))?>
<?php echo $frm->hidden('VendedorProveedorPlan.proveedor_plan_id',array('value' => $comision['VendedorProveedorPlan']['proveedor_plan_id']))?>
<?php echo $frm->hidden('VendedorProveedorPlan.vendedor_id',array('value' => $comision['VendedorProveedorPlan']['vendedor_id']))?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/ventas/vendedores/planes/".$vendedor['Vendedor']['id'] : $fwrd) ))?>
</div>
<?php //   debug($plan)?>