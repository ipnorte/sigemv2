<?php echo $this->renderElement('head',array('title' => 'MUTUAL PRODUCTOS :: AGREGAR PRODUCTOS','plugin' => 'config'))?>
<?php echo $frm->create('MutualProducto');?>
<div class="areaDatoForm">
	<table class="tbl_form">
	
		<tr>
			<td>PROVEEDOR</td>
			<td>
			<?php echo $this->renderElement('proveedor/combo_general',array('plugin' => 'proveedores','metodo' => 'proveedores_list/1','model' => 'MutualProducto.proveedor_id'))?>
			</td>
		</tr>
		<tr>
			<td>PRODUCTO</td>
			<td>
		<?php echo $this->renderElement('global_datos/combo_global',array(
																		'plugin'=>'config',
																		'label' => " ",
																		'model' => 'MutualProducto.tipo_producto',
																		'prefijo' => 'MUTUPROD',
																		'disabled' => false,
																		'empty' => false,
																		'metodo' => "get_tipo_productos_consumos",
																		'selected' => "MUTUPROD0001"	
		))?>				
			<?php //   echo $this->requestAction('/config/global_datos/combo/&nbsp;/MutualProducto.tipo_producto/MUTUPROD/0/0/0/1');?>
			</td>
		</tr>
		<tr>
			<td>IMPORTE FIJO</td><td><?php echo $frm->money('MutualProducto.importe_fijo','',0)?></td>
		</tr>
		<tr>
			<td>CUOTA SOCIAL DIFERENCIADA</td><td><?php echo $frm->money('MutualProducto.cuota_social_diferenciada','',0)?>(0 = TOMA VALOR GENERAL)</td>
		</tr>
		<tr>
			<td>MENSUAL</td><td><?php echo $frm->input('mensual',array('label'=>'')) ?></td>
		</tr>										
		<tr>
			<td>SIN CARGO</td><td><?php echo $frm->input('sin_cargo',array('label'=>'')) ?></td>
		</tr>	
		<tr>
			<td>PRESTAMO</td><td><?php echo $frm->input('prestamo',array('label'=>'')) ?></td>
		</tr>	
	</table>
	<div style="clear: both;"></div>	
<?php //   debug($provincias)?>
</div>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/mutual/mutual_productos'))?>