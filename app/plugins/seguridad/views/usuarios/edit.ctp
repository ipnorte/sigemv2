<h2>Usuarios</h2>

<?php echo $frm->create('Usuario');?>
	<div class="areaDatoForm">
	<h3>MODIFICAR DATOS DEL USUARIO</h3>
        <hr/>
	<table class="tbl_form">
		<tr>
			<td>USUARIO</td>
			<td><?php echo $frm->input('usuario',array('size'=>10,'maxlength'=>10, 'disabled' => 'disabled'));?></td>
		</tr>
		<tr>
			<td>NOMBRE COMPLETO</td>
			<td><?php echo $frm->input('descripcion',array('size'=>50,'maxlength'=>50));?></td>
		</tr>
		<tr>
			<td>GRUPO AL QUE PERTENECE</td>
                        <td><?php echo $frm->input('grupo_id',array('type' => 'select', 'options' => $grupos, 'disabled' => (!empty($user['Usuario']['vendedor_id']) ? TRUE : FALSE)));?></td>
		</tr>
		<tr>
			<td>ACTIVO</td>
			<td><?php echo $frm->input('activo');?></td>
		</tr>
		<tr>
			<td>EMAIL</td>
			<td><?php echo $frm->input('email',array('size'=>45,'maxlength'=>45));?></td>
		</tr>
                
	</table>
 	<?php echo $frm->input('id');?>		
	
	</div>
	<?php echo $frm->btnGuardarCancelar(array('URL' => '/seguridad/usuarios'))?>

<?php // debug($user)?>