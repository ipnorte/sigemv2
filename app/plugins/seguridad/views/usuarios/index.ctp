<h2>Usuarios</h2>

<table cellpadding="0" cellspacing="0">
<tr>
	<td colspan="6" align="right">
		<?php echo $controles->botonGenerico('add','controles/user_add.png','Nuevo')?>   
	</td>
    <td><?php echo $controles->botonGenerico('reset_pws/0/1','controles/16-security-key.png','Reset',NULL,'Blanquear Clave a TODOS los Usuarios?')?></td>
	<td colspan="6" align="right">
		<?php echo $controles->botonGenerico('accesos_listar','menu/world_link.png','Accesos')?>   
	</td>	
</tr>
<tr>
	<th></th>
	<th><?php echo $paginator->sort('USUARIO','usuario');?></th>
	<th><?php echo $paginator->sort('DESCRIPCION','descripcion');?></th>
	<th><?php echo $paginator->sort('GRUPO','Grupo.nombre');?></th>
	<th><?php echo $paginator->sort('ACTIVO','activo');?></th>
	
        <th>EMAIL VALIDACION</th>
        <th>CADUCIDAD</th>
        <th>RESET</th>
	<th class="actions"></th>
</tr>
<?php
$i = 0;
foreach ($usuarios as $usuario):
	$class = null;
//	if ($i++ % 2 == 0) {
//		$class = ' class="altrow"';
//	}
        if($usuario['Usuario']['reset_password'] == 1)$class = ' class="amarillo"';
//        debug($usuario);
?>
	<tr <?php echo $class?>>
		<td><?php echo $controles->btnModalBox(array('img'=> '../menu/world_link.png','title' => 'REGISTRO DE ACCESO','url' => '/seguridad/usuarios/accesos/'.$usuario['Usuario']['id'],'h' => 450, 'w' => 750))?></td>
		<td>
			<strong><?php echo $usuario['Usuario']['usuario']; ?></strong>
		</td>
		<td>
			<?php echo $usuario['Usuario']['descripcion']; ?>
		</td>
		<td>
			<?php echo $usuario['Grupo']['nombre']; ?>
		</td>
		<td align="center">
			<?php echo $controles->onOff($usuario['Usuario']['activo']); ?>
		</td>				
		<td>
			<?php echo $usuario['Usuario']['email']; ?>
		</td>
                <td style="text-align: center;">
			<?php echo ($usuario['Usuario']['reset_password'] == 1 ? "<span style='color: red;'>** REVALIDANDO ***</span>" : $usuario['Usuario']['caduca']); ?>
		</td>                
		<td align="center">
			<?php echo $controles->botonGenerico('reset_pws/'.$usuario['Usuario']['id'],'controles/16-security-key.png','','',null)?>
		</td>							

						
		<td class="actions"><?php echo $controles->getAcciones($usuario['Usuario']['id'],false) ?></td>

	</tr>
<?php endforeach;?>
</table>
<?php echo $this->renderElement('paginado')?>