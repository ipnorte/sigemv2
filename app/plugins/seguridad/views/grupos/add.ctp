<?php echo $frm->create('Grupo');?>
<h3>CREAR GRUPO DE USUARIOS NUEVO</h3>
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
		<tr>
			<td>COPIAR PERMISOS DE</td>
			<td><?php echo $frm->input('grupo_permiso',array('type'=>'select','options'=>$grupos,'empty'=>true,'selected' => ""));?></td>
		</tr>				
	</table>
 	<?php echo $frm->input('id');?>	

			
</div>
	<?php echo $frm->btnGuardarCancelar(array('URL' => '/seguridad/grupos'))?>

