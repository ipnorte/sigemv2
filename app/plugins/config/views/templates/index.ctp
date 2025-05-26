<?php echo $this->renderElement('head',array('title' => 'CONFIGURACION DE PLANTILLAS'))?>

<div class="actions">
	<?php echo $adrian->btnAdd('Nueva Plantilla','/config/templates/add')?>
</div>

<?php echo $this->renderElement('paginado')?>

<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('#','id');?></th>
	<th><?php echo $paginator->sort('TIPO','TIPO');?></th>
	<th><?php echo $paginator->sort('DESCRIPCION','descripcion');?></th>
	<th><?php echo $paginator->sort('ACTIVO','activo');?></th>
	<th class="actions"></th>
</tr>
<?php
$i = 0;
foreach ($templates as $template):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td><?php echo $template['Template']['id']; ?></td>
		<td><?php echo ($template['Template']['tipo'] == 'M' ? 'MUTUO' : ($template['Template']['tipo'] == 'P' ? 'PAGARE' : 'LIQUIDACION')); ?></td>
		<td><?php echo $template['Template']['descripcion']; ?></td>
		<td align="center"><?php echo $adrian->onOff($template['Template']['activo']); ?></td>
		<td class="actions"><?php echo $adrian->getAcciones($template['Template']['id'],false) ?></td>
	</tr>
<?php endforeach; ?>
</table>

<?php //   debug($templates)?>

<?php echo $this->renderElement('paginado')?>