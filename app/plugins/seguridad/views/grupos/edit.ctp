<?php echo $frm->create('Grupo');?>
<h3>MODIFICAR DATOS DEL GRUPO</h3>
<div class="areaDatoForm">

	<table class="tbl_form">
		<tr>
			<td>DESCRIPCION</td>
			<td><?php echo $frm->input('nombre',array('size'=>50,'maxlength'=>50));?></td>
		</tr>
		<tr>
			<td>ACTIVO</td>
			<td><?php echo $frm->input('activo');?></td>
		</tr>
		<tr>
			<td>PERMITIR CONSULTA</td>
			<td><?php echo $frm->input('consultar');?></td>
		</tr>		
		<tr>
			<td>PERMITIR VISTA</td>
			<td><?php echo $frm->input('vista');?></td>
		</tr>
		<tr>
			<td>PERMITIR AGREGAR</td>
			<td><?php echo $frm->input('agregar');?></td>
		</tr>		
		<tr>
			<td>PERMITIR MODIFICAR</td>
			<td><?php echo $frm->input('modificar');?></td>
		</tr>
		<tr>
			<td>PERMITIR BORRAR</td>
			<td><?php echo $frm->input('borrar');?></td>
		</tr>		
							
	</table>
 	<?php echo $frm->input('id');?>	

			

		
	
	<?php if(isset($this->data['Usuario']) && count($this->data['Usuario']) != 0):?>
		
		<table>
			<tr><th colspan="3">USUARIOS PERTENECIENTES AL GRUPO</th></tr>
			<tr><th>USUARIO</th><th>DESCRIPCION</th></tr>
	
				<?php foreach($this->data['Usuario'] as $usuario):?>
					<tr>
						<td><strong><?php echo $html->link($usuario['usuario'],'/seguridad/usuarios/edit/'.$usuario['id'])?></strong></td>
						<td><?php echo $usuario['descripcion']?></td>
					</tr>
				<?php endforeach;?>		
		</table>
	
	<?php endif;?>
			


</div>
	<?php echo $frm->btnGuardarCancelar(array('URL' => '/seguridad/grupos'))?>
	
	