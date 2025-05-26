<h2>Grupos</h2>

<table cellpadding="0" cellspacing="0">
<tr>
	<td colspan="9" align="right">
		<?php echo $controles->botonGenerico('add','controles/group_add.png','Nuevo')?>
	</td>
</tr>
<tr>
	<th></th>
<!--	<th>#</th>-->
	<th><?php echo $paginator->sort('NOMBRE','nombre');?></th>
	<th><?php echo $paginator->sort('ACTIVO','activo');?></th>
	<th><?php echo $paginator->sort('CONSULTAR','consultar');?></th>
	<th><?php echo $paginator->sort('VISTA','vista');?></th>
	<th><?php echo $paginator->sort('AGREGAR','agregar');?></th>
	<th><?php echo $paginator->sort('MODIFICAR','modificar');?></th>
	<th><?php echo $paginator->sort('BORRAR','borrar');?></th>
	<th class="actions"></th>
</tr>
<?php
$i = 0;
foreach ($grupos as $grupo):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr>
		<td class="actions"><?php echo $controles->botonGenerico('/seguridad/grupos/permisos/'.$grupo['Grupo']['id'],'controles/lock_go.png') ?></td>
		<td><?php echo $grupo['Grupo']['nombre']; ?></td>
		<td align="center"><?php echo $controles->onOff($grupo['Grupo']['activo']); ?></td>
		<td align="center"><?php echo $controles->onOff($grupo['Grupo']['consultar']); ?></td>
		<td align="center"><?php echo $controles->onOff($grupo['Grupo']['vista']); ?></td>
		<td align="center"><?php echo $controles->onOff($grupo['Grupo']['agregar']); ?></td>
		<td align="center"><?php echo $controles->onOff($grupo['Grupo']['modificar']); ?></td>
		<td align="center"><?php echo $controles->onOff($grupo['Grupo']['borrar']); ?></td>				
		<td class="actions"><?php echo $controles->getAcciones($grupo['Grupo']['id'],false) ?></td>

	</tr>
<?php endforeach;?>
</table>
<?php echo $this->renderElement('paginado')?>
