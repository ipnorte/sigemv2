<?php echo $this->renderElement('proveedor/padron_header',array('proveedor' => $proveedor))?>
<h3>ADMINISTRACION DE PLANES Y/O PRODUCTOS</h3>

<div class="actions">
<?php echo $frm->btnForm(array('LABEL' => "NUEVO PLAN / PRODUCTO",'URL' => '/proveedores/proveedor_planes/nuevo_plan/'.$proveedor['Proveedor']['id']))?>
</div>

<?php if(!empty($planes)):?>

	<h3>DETALLE DE PLANES CARGADOS</h3>

	<table>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th>#</th>
			<th>PRODUCTO</th>
			<th>DENOMINACION</th>
			<th>ORGANISMOS</th>
			<th>ALTA</th>
			<th>VIGENTE</th>
		</tr>
		<?php foreach($planes as $plan):?>
			<tr>
				<td><?php echo $frm->btnForm(array('LABEL' => "ADM. GRILLAS",'URL' => '/proveedores/proveedor_planes/grillas/'.$plan['ProveedorPlan']['id']))?></td>
				<td><?php echo $controles->btnEdit(null,'/proveedores/proveedor_planes/editar_plan/'.$plan['ProveedorPlan']['id'])?></td>
				<td><?php echo $controles->btnDrop(null,'/proveedores/proveedor_planes/borrar_plan/'.$plan['ProveedorPlan']['id'],'Borrar el Plan "'.$plan['ProveedorPlan']['descripcion'].' y TODAS las Grillas de Cuotas vinculadas"?')?></td>
				<td><strong><?php echo $plan['ProveedorPlan']['id']?></strong></td>
				<td><strong><?php echo $util->globalDato($plan['ProveedorPlan']['tipo_producto'])?></strong></td>
				
				<td><strong><?php echo $plan['ProveedorPlan']['descripcion']?></strong></td>
				<td>
					<?php foreach($plan['ProveedorPlanOrganismo'] as $codigo_organismo):?>
						<strong><?php echo $util->globalDato($codigo_organismo['codigo_organismo'])?>, </strong>
					<?php endforeach;?>
				</td>
				<td><?php echo $plan['ProveedorPlan']['user_created'] . " [" . $plan['ProveedorPlan']['created']."]"?></td>
				<td align="center"><?php echo $controles->btnAjaxToggleOnOff('desactivar_plan/activo/'.$plan['ProveedorPlan']['id'],$plan['ProveedorPlan']['activo'],"ACTIVAR PLAN?","DESACTIVAR PLAN?");?></td>
				
				<!--
								
				<td><?php //   echo $util->armaFecha($plan['ProveedorPlan']['vigencia_desde'])?></td>
				<td><?php //   echo $util->armaFecha($plan['ProveedorPlan']['vigencia_hasta'])?></td>
				<td><?php //   echo $controles->botonGenerico('/proveedores/proveedor_planes/download_grilla/'.$plan['ProveedorPlan']['id'],'controles/ms_excel.png','',array('target' => 'blank'))?></td>
				-->
			</tr>
		<?php endforeach;?>
	</table>
<?php endif;?>
<?php //   debug($planes)?>