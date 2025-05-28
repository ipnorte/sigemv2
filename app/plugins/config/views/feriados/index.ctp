<?php echo $this->renderElement('head',array('title' => 'FERIADOS'))?>
<div class="actions"><?php echo $controles->botonGenerico('add','controles/add.png','Nuevo Feriado')?></div>
<?php echo $this->renderElement('paginado')?>
<table>
	<tr>
		<th>#</th>
		<th><?php echo $paginator->sort('FERIADO','Feriado.fecha');?></th>
		<th></th>
	</tr>
<?php
$i = 0;
foreach ($feriados as $feriado):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>	
	
<tr<?php echo $class;?>>
	<td><?php echo $feriado['Feriado']['id']?></td>
	<td><?php echo $util->armaFecha($feriado['Feriado']['fecha'])?></td>
	<td class="actions"><?php echo $controles->getAcciones($feriado['Feriado']['id'],false) ?></td>
</tr>	
	
<?php endforeach;?>	
</table>
<?php //   debug($feriados)?>
<?php echo $this->renderElement('paginado')?>