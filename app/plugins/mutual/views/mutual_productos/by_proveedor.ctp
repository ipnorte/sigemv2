
<?
//echo $frm->input('mutual_producto_id',array('type'=>'select','options'=>$productos,'empty'=>TRUE,'selected' => '','label'=>'PRODUCTO','disabled' => ''));
?> 

<h4><?php echo $proveedor['Proveedor']['razon_social']?></h4>
<table>

	<tr>
		<th></th>
		<th>#</th>
		<th>TIPO</th>
		<th>DENOMINACION</th>
	
	</tr>

<?php
$i = 0;
foreach ($productos as $producto):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>	

<tr<?php echo $class;?>>

	<td><input type="radio" id="MutualProducto" name="data[MutualProductoSolicitud][tipo_producto_mutual_producto_id]" value="<?php echo $producto['MutualProducto']['id'] . '|' . $producto['MutualProducto']['tipo']?>" /></td>
	<td><?php echo $producto['MutualProducto']['id']?></td>
	<td><?php echo $this->requestAction('/config/global_datos/valor/'.$producto['MutualProducto']['tipo'])?></td>
	<td><?php echo $producto['MutualProducto']['descripcion']?></td>

</tr>
	
<?php endforeach;?>
</table>
<?php //   debug($proveedor)?>
<?php //   debug($productos)?>