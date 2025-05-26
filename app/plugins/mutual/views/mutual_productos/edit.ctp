<?php echo $this->renderElement('head',array('title' => 'MUTUAL PRODUCTOS :: MODIFICAR PRODUCTO','plugin' => 'config'))?>
<?php echo $frm->create('MutualProducto');?>
<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>PROVEEDOR</td><td><?php echo $this->renderElement('proveedor/combo_general',array('plugin' => 'proveedores','metodo' => 'proveedores_list/1','model' => 'MutualProducto.proveedor_id','selected' => $this->data['MutualProducto']['proveedor_id'],'disabled' => true))?></td>
		</tr>
		<tr>
			<td>PRODUCTO</td><td>
		<?php echo $this->renderElement('global_datos/combo_global',array(
																		'plugin'=>'config',
																		'label' => " ",
																		'model' => 'MutualProducto.tipo_producto',
																		'prefijo' => 'MUTUPROD',
																		'disabled' => true,
																		'empty' => false,
																		'metodo' => "get_tipo_productos",
																		'selected' => $this->data['MutualProducto']['tipo_producto']	
		))?>			
			<?php //   echo $this->requestAction('/config/global_datos/combo/&nbsp;/MutualProducto.tipo_producto/MUTUPROD/0/0/'.$this->data['MutualProducto']['tipo_producto'].'/1');?></td>
		</tr>
		<tr>
			<td>IMPORTE FIJO</td><td><?php echo $frm->money('MutualProducto.importe_fijo','',$this->data['MutualProducto']['importe_fijo'])?></td>
		</tr>
		<tr>
			<td>CUOTA SOCIAL DIFERENCIADA</td><td><?php echo $frm->money('MutualProducto.cuota_social_diferenciada','',$this->data['MutualProducto']['cuota_social_diferenciada'])?>(0 = TOMA VALOR GENERAL)</td>
		</tr>
		<tr>
			<td>ACTIVO</td><td><?php echo $frm->input('activo') ?></td>
		</tr>
		<tr>
			<td>MENSUAL</td><td><?php echo $frm->input('mensual',array('label'=>'','disabled' => 'disabled')) ?></td>
		</tr>										
		<tr>
			<td>SIN CARGO</td><td><?php echo $frm->input('sin_cargo',array('label'=>'','disabled' => 'disabled')) ?></td>
		</tr>	
		<tr>
			<td>PRESTAMO</td><td><?php echo $frm->input('prestamo',array('label'=>'','disabled' => 'disabled')) ?></td>
		</tr>
                
             
                
	</table>
    
    
    <div class="areaDatoForm2">
        <table class="tbl_form">
        <tr>
            <td>PLANTILLA DE IMPRESION</td>
            <td>
		<?php 
		echo $this->renderElement('global_datos/combo_global',array(
			'plugin' => 'config',
			'metodo' => 'get_solicitud_templates/0',
			'model' => 'MutualProducto.modelo_solicitud_codigo',
			'empty' => false,
			'selected' => (isset($this->data['MutualProducto']['modelo_solicitud_codigo']) ? $this->data['MutualProducto']['modelo_solicitud_codigo'] : NULL),
		));
		?>                 
            </td>
        </tr>
        <tr>
            <td>IMPRIMIR ANEXOS</td>
            <td>
		<?php 
		echo $this->renderElement('global_datos/grilla_checks',array(
			'plugin' => 'config',
			'metodo' => 'get_solicitud_anexos',
			'model' => 'MutualProducto.anexos',
			'header' => false,
			'selected' => Set::extract("MutualProductoAnexo/codigo_anexo",$this->data['MutualProductoAnexo']),
		));
		?>                
            </td>
        </tr>               
        </table>
    </div>
    

	<div style="clear: both;"></div>
<?php //   debug($provincias)?>
</div>
<?php echo $frm->hidden('id',array('value'=>$this->data['MutualProducto']['id'])) ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/mutual/mutual_productos'))?>

<?php // debug($this->data)?>