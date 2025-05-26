<?php echo $this->renderElement('head',array('title' => 'ADMINISTRACION DE SERVICIOS','plugin' => 'config'))?>

<div class="areaDatoForm">
<?php echo $frm->create(null,array('action' => 'index','id' => 'formHeader'))?>
	<table class="tbl_form">
		<tr>
			<td>PROVEEDOR</td>
			<td><?php echo $this->renderElement("proveedor/combo_general",array('plugin' => 'proveedores', 'metodo' => 'proveedores_list'))?></td>
			<td><?php echo $frm->submit("INGRESAR")?></td>
		</tr>
	
	</table>
<?php echo $frm->end();?>
</div>