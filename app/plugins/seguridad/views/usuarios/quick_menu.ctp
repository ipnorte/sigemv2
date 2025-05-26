<?php $quickMenus = $this->requestAction('/seguridad/permisos/opcionesMenu/'.$user['Usuario']['grupo_id'].'/0/true') ?>
<?php foreach($quickMenus as $quickMenu){ ?>
	|<?php // echo $html->link($html->image('menu/'.$quickMenu['Permiso']['icon'], array('border'=>0)).$quickMenu['Permiso']['descripcion'],$quickMenu['Permiso']['url'],null,false,false)?>
	<?php echo $html->link($quickMenu['Permiso']['descripcion'],$quickMenu['Permiso']['url'],null,false,false)?>
<?php }?>
