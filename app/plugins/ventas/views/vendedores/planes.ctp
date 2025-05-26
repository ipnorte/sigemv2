<?php echo $this->renderElement('vendedores/menu_padron',array('vendedor' => $vendedor))?>
<h3>PLANES y COMISIONES HABILITADOS PARA EL VENDEDOR</h3>
<div class="actions">
<?php echo $controles->botonGenerico('nuevo_plan/'.$vendedor['Vendedor']['id'],'controles/chart_organisation.png','HABILITAR NUEVO PLAN')?>
</div>
<table>
	<tr>
		<th></th>
		<th></th>
		<th>PROVEEDOR</th>
		<th>PLAN</th>
		<th>VIGENTE</th>
	</tr>
	<?php foreach ($planes as $plan):?>
		<tr>
			<td><?php echo $controles->btnDrop(null,'/ventas/vendedores/borrar_plan/'.$plan['VendedorProveedorPlan']['id'],'Borrar la Comision?')?></td>
			<td><?php //   echo $controles->btnEdit(null,'/ventas/vendedores/modificar_plan/'.$plan['VendedorProveedorPlan']['id'])?></td>
			<td><?php echo $plan['Proveedor']['razon_social']?></td>
			<td>
                            <?php echo "#" . $plan['ProveedorPlan']['id']." - ".$plan['ProveedorPlan']['descripcion']?>
                            <?php if(!$plan['ProveedorPlan']['activo']):?>
                                &nbsp;<span style="color: red;">** NO VIGENTE **</span>
                            <?php endif;?>
                        </td>
			<td align="center"><?php echo $controles->btnAjaxToggleOnOff('/ventas/vendedores/desactivar_plan/activo/'.$plan['VendedorProveedorPlan']['id'],$plan['VendedorProveedorPlan']['activo'],"ACTIVAR PLAN?","DESACTIVAR PLAN?");?></td>
			</td>					
		</tr>
	<?php endforeach;?>
</table>

<?php // debug($planes)?>