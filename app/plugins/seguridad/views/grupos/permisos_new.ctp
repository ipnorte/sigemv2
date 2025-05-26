<?php echo $this->renderElement('head',array('title' => 'ADMINISTRACION DE PERMISOS','plugin' => 'config'))?>
<div class="areaDatoForm2">
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
<div class="areaDatoForm3">
    <h3>Opciones de Menu / Funciones</h3>
    <table>
	<tr>
		<th></th>
		<th>#</th>
		<th>DESCRIPCION</th>
		<th>URL</th>
		<th>MAIN</th>
		<th></th>
	</tr>  
    <?php foreach ($permisos as $permiso):?>
    <?php $permiso[0]['habilitado'] = ($permiso[0]['habilitado'] == 0 ? 1 : 0);?>
    <tr class="<?php echo ($permiso['Permiso']['main'] == 0 ? 'italic' : ' strong ')?> <?php echo ($permiso['Permiso']['parent'] == 0 ? 'h3 gris grilla_destacada' : ' selected ')?> <?php echo ($permiso[0]['habilitado'] == 100 ? ' ' : ' ')?>">
        <!--<td><?php // echo $controles->btnAjaxToggleOnOff('cierre_liquidacion/'.$permiso['Permiso']['id'].'/'.$usuario['Usuario']['grupo_id'],$permiso[0]['habilitado'],"DESHABILITAR ".$permiso['Permiso']['descripcion']."?","HABILITAR ".$permiso['Permiso']['descripcion']."?","controles/lock.png","controles/lock_unlock.png");?></td>-->
        <!--<td><?php // echo $controles->btnAjaxToggleOnOff('look_unlook/'.$permiso['Permiso']['id'].'/'.$usuario['Usuario']['grupo_id'],$permiso[0]['habilitado'],null,null,"controles/lock.png","controles/lock_unlock.png");?></td>-->
        <td></td>
        <td><?php echo $permiso['Permiso']['id']?></td>
        <td><?php echo $permiso['Permiso']['descripcion']?></td>
        <td><?php echo $permiso['Permiso']['url']?></td>
        <td><?php echo (!empty($permiso['Permiso']['icon']) ? $html->image('menu/'.$permiso['Permiso']['icon'],array("border"=>"0")): '');?></td>
        <td><?php echo $controles->onOff($permiso[0]['habilitado'],true);?></td>
    </tr>
    <?php endforeach;?>
    </table>
</div>
<?php //   debug($permisos)?>
<?php
//$menus = $this->requestAction('/seguridad/permisos/opcionesMenu');
//debug($menus);
?>