<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'PROVEEDORES'))?>

<div class='row'>
	<?php echo $form->create(null,array('action'=> 'index'));?>
	<table>
		<tr>
			<td colspan="7"><strong>PROVEEDOR</strong></td>
		</tr>
		<tr>
			<td>
				<?php echo $frm->input('Movimiento.cuit', array('label' => 'C.U.I.T.:'))?>
			</td>
			<td>
				<?php echo $frm->input('Movimiento.razon_social',array('label' => 'Razon Social:'))?>
			</td>
			<td><input type="submit" class="btn_consultar" value="APROXIMAR" /></td>
		</tr>
	</table>
	<?php echo $form->end();?> 
</div>

<?php if(!empty($proveedores))echo $this->renderElement('proveedor/grilla_proveedores_paginada',array('proveedores'=>$proveedores,'plugin' => 'proveedores'))?>
