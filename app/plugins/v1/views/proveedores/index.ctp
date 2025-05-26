<?php echo $this->renderElement('head',array('title' => 'Actualización de Grillas :: Gestión','plugin' => 'config'))?>

<?php if(!empty($proveedores)):?>
	<h3>Proveedores Activos</h3>
	<table>
	
		<tr>
			<th></th><!--  -->
			<th>CUIT</th>
			<th>RAZON SOCIAL</th>
		</tr>

	<?php foreach ($proveedores as $proveedor):?>
		<tr>
			<td><?php echo $controles->botonGenerico('/v1/proveedores/listar_planes/'.$proveedor['ProveedorV1']['codigo_proveedor'],'controles/yast_sysadmin.png')?></td>
			<td><?php echo $proveedor['ProveedorV1']['codigo_proveedor']?></td>
			<td style="font-weight: bold;"><?php echo $proveedor['ProveedorV1']['razon_social']?></td>
		</tr>
	<?php endforeach;?>

	</table>

<?php endif;?>

<?php 

//debug($proveedores);

?>