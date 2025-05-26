<?php echo $this->renderElement('proveedor/padron_header',array('proveedor' => $proveedor))?>
<h3>ADMINISTRACION DE PRODUCTOS :: NUEVO PRODUCTO</h3>

<div class="areaDatoForm">
<?php echo $frm->create(null,array('action' => 'nuevo_producto/' . $proveedor['Proveedor']['id']))?>

	<table class="tbl_form">
	
		<tr>
			<td>TIPO</td>
			<td>
				<?php echo $this->renderElement('global_datos/combo_global',array(
																			'plugin'=>'config',
																			'label' => "",
																			'model' => 'MutualProducto.tipo_producto',
																			'disabled' => false,
																			'empty' => false,
																			'selected' => $this->data['MutualProducto']['tipo_producto'],
																			'metodo' => 'get_tipo_productos'
				))?>			
			</td>
		</tr>
		<tr>
			<td>PLANES</td>
			<td>
			<?php 
			echo $this->renderElement('proveedor/planes_grilla_checks',
			array(
				'plugin' => 'proveedores',
				'model' => 'MutualProducto',
				'checkAll' => false,
			)
			);
			?>
			</td>
		</tr>
	</table>

<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/proveedores/proveedores/productos/".$proveedor['Proveedor']['id'] : $fwrd) ))?>
</div>