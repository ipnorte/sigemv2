<?php echo $this->renderElement('head',array('title' => 'ADMINISTRACION DE SERVICIOS','plugin' => 'config'))?>

<?php echo $this->renderElement("proveedor/datos_proveedor",array('plugin' => 'proveedores', 'proveedor_id' => $proveedor_id))?>

<h3>SERVICIOS DEL PROVEEDOR</h3>
<div class="actions"><?php echo $controles->botonGenerico('add/'.$proveedor_id,'controles/cart_add.png','Nuevo Servicio')?></div>
<?php if(!empty($servicios)):?>

	<table>
		
		<tr>
			<th></th>
			<th></th>
            <th>#</th>
			<th>SERVICIO</th>
			<th>DIA CORTE</th>
			<th>MESES ANTES</th>
			<th>MESES DESPUES</th>
			<th>DIA ALTA</th>
			<th>CALL CENTER</th>
			<th>ACTIVO</th>
		</tr>
		<?php foreach($servicios as $servicio):?>
			<tr>
				<td><?php echo $controles->botonGenerico('estado/'.$servicio['MutualServicio']['id'],'controles/edit.png')?></td>
				<td><?php echo $controles->botonGenerico('valores/'.$servicio['MutualServicio']['id'],'controles/calculator.png')?></td>
				<td><?php echo $servicio['MutualServicio']['id']?></td>
                <td><?php echo $servicio['MutualServicio']['tipo_producto_desc']?></td>
				<td align="center"><?php echo $servicio['MutualServicio']['dia_corte']?></td>
				<td align="center"><?php echo $servicio['MutualServicio']['meses_antes_dia_corte']?></td>
				<td align="center"><?php echo $servicio['MutualServicio']['meses_despues_dia_corte']?></td>
				<td align="center"><?php echo $servicio['MutualServicio']['dia_alta']?></td>
				<td align="center"><?php echo $controles->onOff($servicio['MutualServicio']['call_center'])?></td>
				<td align="center"><?php echo $controles->onOff($servicio['MutualServicio']['activo'])?></td>
			</tr>
		<?php endforeach;?>
	
	</table>

<?php //   debug($servicios)?>

<?php endif;?>