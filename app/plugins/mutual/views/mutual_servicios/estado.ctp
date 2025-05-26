<?php echo $this->renderElement('head',array('title' => 'ADMINISTRACION DE SERVICIOS','plugin' => 'config'))?>

<?php echo $this->renderElement("proveedor/datos_proveedor",array('plugin' => 'proveedores', 'proveedor_id' => $this->data['MutualServicio']['proveedor_id']))?>

<h3><?php echo $util->globalDato($this->data['MutualServicio']['tipo_producto'])?></h3>

<div class="areaDatoForm">
	<h4>MODIFICAR CONFIGURACION</h4>
<?php echo $frm->create(null,array('action' => 'estado/' . $this->data['MutualServicio']['id'],'id' => 'formHeader'))?>
	<table class="tbl_form">
		<tr>
			<td>DIA DE CORTE</td>
			<td>
				<?php echo $frm->comboDias('MutualServicio.dia_corte',$this->data['MutualServicio']['dia_corte']) ?>
			</td>
		</tr>
		<tr>
			<td>ANTES DEL CORTE</td>
			<td>
				<?php echo $frm->number('MutualServicio.meses_antes_dia_corte',array('value' => $this->data['MutualServicio']['meses_antes_dia_corte'],'size' => 3, 'maxlength' => 3))?> [+ meses]
			</td>
		</tr>
		<tr>
			<td>DESPUES DEL CORTE</td>
			<td>
				<?php echo $frm->number('MutualServicio.meses_despues_dia_corte',array('value' => $this->data['MutualServicio']['meses_despues_dia_corte'],'size' => 3, 'maxlength' => 3))?> [+ meses]
			</td>
		</tr>
		<tr>
			<td>ALTA EL DIA</td>
			<td>
				<?php echo $frm->comboDias('MutualServicio.dia_alta',$this->data['MutualServicio']['dia_alta']) ?>
			</td>
		</tr>
		<tr>
			<td>CALL CENTER</td><td><?php echo $frm->input('call_center') ?></td>
		</tr>		
		<tr>
			<td>ACTIVO</td><td><?php echo $frm->input('activo') ?></td>
		</tr>
	</table>
	<?php echo $frm->hidden('MutualServicio.id'); ?>
	<?php echo $frm->hidden('MutualServicio.proveedor_id'); ?>
	<?php echo $frm->hidden('MutualServicio.tipo_orden_dto'); ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/mutual/mutual_servicios/servicios_by_proveedor/'.$this->data['MutualServicio']['proveedor_id']))?>
</div>

