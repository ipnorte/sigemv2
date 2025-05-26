<?php echo $this->renderElement('head',array('title' => 'BARRIOS'))?>
<div class="actions"><?echo $controles->botonGenerico('add','controles/add.png','Nuevo Barrio')?></div>
<?php echo $this->renderElement('paginado')?>
<table>

	<tr>
		<th>COD</th>
		<th><?php echo $paginator->sort('BARRIO','Barrio.nombre');?></th>
		<th><?php echo $paginator->sort('CP','Localidad.cp');?></th>
		<th><?php echo $paginator->sort('LOCALIDAD','Localidad.nombre');?></th>
		<th class="actions"></th>
	</tr>
<?php
$i = 0;
foreach ($barrios as $barrio):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>

<tr<?php echo $class;?>>
	<td><?php echo $barrio['Barrio']['id']?></td>
	<td><?php echo $barrio['Barrio']['nombre']?></td>
	<td><?php echo $barrio['Localidad']['cp']?></td>
	<td><?php echo $barrio['Localidad']['nombre']?></td>
	<td class="actions"><?php echo $controles->getAcciones($barrio['Barrio']['id'],false) ?></td>
</tr>

<?php endforeach;?>
</table>
<?php //   debug($barrios)?>
<?php echo $this->renderElement('paginado')?>