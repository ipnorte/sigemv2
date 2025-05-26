<h2>Usuarios</h2>
<?php echo $frm->create('Usuario');?>
	<div class="areaDatoForm">
            <h3>ALTA NUEVO USUARIO</h3>
            <hr/>
	<table class="tbl_form">
		<tr>
			<td>USUARIO (sin espacios)</td>
			<td><?php echo $frm->input('usuario',array('size'=>10,'maxlength'=>10));?></td>
		</tr>
		<tr>
			<td>NOMBRE COMPLETO</td>
			<td><?php echo $frm->input('descripcion',array('size'=>50,'maxlength'=>50));?></td>
		</tr>
		<tr>
			<td>GRUPO AL QUE PERTENECE</td>
			<td><?php echo $frm->input('grupo_id',$grupos);?></td>
		</tr>
		<tr>
			<td>EMAIL</td>
			<td><?php echo $frm->input('email',array('size'=>45,'maxlength'=>45));?></td>
		</tr>                
	</table>
 	<?php echo $frm->input('id');?>	
        <?php echo $frm->hidden('Usuario.activo',array('value' => 1));?>    
	
	</div>
	<?php echo $frm->btnGuardarCancelar(array('URL' => '/seguridad/usuarios'))?>

