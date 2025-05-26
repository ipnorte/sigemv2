
<div class="grupos view">
<h2><?php  __('Grupo');?> <?php echo $nombre; ?></h2>

</div>
<div class="related">
	<h3>Usuarios del Grupo</h3>
	<?php if (!empty($usuarios)):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th>USUARIO</th>
		<th>DESCRIPCION</th>
		<th>ACTIVO</th>
	</tr>
	<?php
		$i = 0;
		foreach ($usuarios as $usuario):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $usuario['Usuario']['usuario'];?></td>
			<td><?php echo $usuario['Usuario']['descripcion'];?></td>
			<td align="center"><?php echo $controles->onOff($usuario['Usuario']['activo']);?></td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

</div>

<div class="related" id="GrillaPermisosGrupo">
	<h3>Permisos Asignados al Grupo</h3>
	
	<?php if (!empty($habilitados)):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr><td colspan="8" align="right"><?php echo $controles->botonGenerico('permisos/'.$id.'/action:denegar/uid:all','controles/lock.png','Quitar Todos')?></td></tr>
	<tr>
		<th></th>
		<th>#</th>
		<th>DESCRIPCION</th>
		<th>URL</th>
		<th>MAIN</th>
		<th>QUICK</th>
		<th>ICON</th>
		<th>ACTIVO</th>
		<th>OBS</th>
	</tr>
	<?php
		$i = 0;
		$tab = "";
		foreach ($habilitados as $permiso):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
			if($permiso['main']==1 && $permiso['parent']== 0) {
				$class = ' class="strong"';
				$tab = "";
			}else{
				$class = ' class="italic"';
				$tab = "&nbsp;&nbsp;";
			}
		?>
		<tr<?php echo $class;?>>
			<td align="center"><?php echo $controles->botonGenerico('permisos/'.$id.'/action:denegar/uid:'.$permiso['id'],'controles/lock_unlock.png')?></td>
			<td><?php echo $permiso['id'];?></td>
			<td><?php echo $tab?><?php echo $permiso['descripcion'];?></td>
			<td><?php echo $permiso['url'];?></td>
			<td align="center"><?php echo $controles->onOff($permiso['main'],true);?></td>
			<td align="center"><?php echo $controles->onOff($permiso['quick'],true);?></td>
			<td align="center"><?php echo (!empty($permiso['icon']) ? $html->image('menu/'.$permiso['icon'],array("border"=>"0")): '');?></td>
			<td align="center"><?php echo $controles->onOff($permiso['activo'],true);?></td>
			<td><?php echo $permiso['obs'];?></td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

</div>

<div class="related">
	<h3>Permisos Disponibles para Agregar al Grupo</h3>
	
<?php if (!empty($denegados)):?>	

	<table>
	<tr><td colspan="8" align="right"><?php echo $controles->botonGenerico('permisos/'.$id.'/action:autorizar/uid:all','controles/lock_unlock.png','Autorizar Todos')?></td></tr>
	<tr>
		<th></th>
		<th>#</th>
		<th>DESCRIPCION</th>
		<th>URL</th>
		<th>MAIN</th>
		<th>QUICK</th>
		<th>ICON</th>
		<th>ACTIVO</th>
		<th>OBS</th>

	</tr>	

	<?php
	$i = 0;
	foreach ($denegados as $menu) {
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		if($menu['Permiso']['main']==1 && $menu['Permiso']['parent']== 0) $class = ' class="strong"'; 
	?>	
		<tr <?php echo $class;?>>
			<td align="center"><?php echo $controles->botonGenerico('permisos/'.$id.'/action:autorizar/uid:'.$menu['Permiso']['id'],'controles/lock.png')?></td>
			<td><?php echo $menu['Permiso']['id'];?></td>
			<td><?php echo $menu['Permiso']['descripcion']?></td>
			<td><?php echo $menu['Permiso']['url']?></td>
			<td align="center"><?php echo $controles->onOff($menu['Permiso']['main'],true)?></td>
			<td align="center"><?php echo $controles->onOff($menu['Permiso']['quick'],true)?></td>
			<td align="center"><?php echo (!empty($menu['Permiso']['icon']) ? $html->image('menu/'.$menu['Permiso']['icon'],array("border"=>"0")) : '')?></td>
			<td align="center"><?php echo $controles->onOff($menu['Permiso']['activo'],true)?></td>
			<td><?php echo $menu['Permiso']['obs']?></td>			
		</tr>
		        
	<?php }?> 	
	
	</table>
	
<?php endif; ?>	
</div>