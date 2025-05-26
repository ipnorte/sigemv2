<?php echo $this->renderElement('head',array('title' => 'MUTUAL PRODUCTOS','plugin' => 'config'))?>
<?php echo $controles->botonGenerico('add','controles/add.png','Nuevo Producto')?>

<table>

	<tr>
	
		<th></th>
		<th></th>
		<th>PROVEEDOR</th>
		<th>TIPO DTO</th>
		<th>PRODUCTO</th>
		<th>ACTIVO</th>
		<th>IMP.FIJO</th>
		<th>C.SOCIAL</th>
		<th>MENSUAL</th>
		<th>SCARGO</th>
		<th>PRESTAMO</th>
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
		<td class="actions"><?php echo $controles->getAcciones($producto['MutualProducto']['id'],false,true,false) ?></td>
		<td><?php echo $controles->botonGenerico('del/'.$producto['MutualProducto']['id'],'controles/user-trash-full.png',null,null,"Borrar el Producto ".$producto['Proveedor']['razon_social']." - ".$producto['GlobalDato']['concepto_1']."?") ?></td>
		<td><strong><?php echo $producto['Proveedor']['razon_social']?></strong></td>
		<td align="center"><?php echo $producto['MutualProducto']['tipo_orden_dto']?></td>
		<td><?php echo $producto['GlobalDato']['concepto_1']?></td>
		
		<td align="center"><?php echo $controles->OnOff($producto['MutualProducto']['activo'])?></td>
		
		<td align="right"><?php echo ($producto['MutualProducto']['importe_fijo'] > 0 ? number_format($producto['MutualProducto']['importe_fijo'],2) : '')?></td>
		<td align="right"><?php echo ($producto['MutualProducto']['cuota_social_diferenciada'] > 0 ? number_format($producto['MutualProducto']['cuota_social_diferenciada'],2) : 'GENERAL')?></td>
		<td align="center"><?php echo $controles->OnOff($producto['MutualProducto']['mensual'])?></td>
		<td align="center"><?php echo $controles->OnOff($producto['MutualProducto']['sin_cargo'],true)?></td>
		<td align="center"><?php echo $controles->OnOff($producto['MutualProducto']['prestamo'],true)?></td>
		
	</tr>

<?php endforeach;?>

</table>

<?php //debug($productos) ?>